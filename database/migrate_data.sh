#!/bin/bash
# Export MySQL tables as CSV via SSH and import into PostgreSQL

SSH_CMD="sshpass -p 'jar3d@g2345U' ssh -o StrictHostKeyChecking=no -p 19873 jared@102.210.148.223"
MYSQL_CMD="mysql -h planner.ouk.ac.ke -u bentito -p'Spoiler@2050#' planner_prod"
PG_DB="ouk_timetabling"
PG_USER="mwarabu"
TMP_DIR="/tmp/ouk_csv_export"

# Tables to migrate (in dependency order, skip Laravel internals we don't need)
TABLES=(
  "titles"
  "academic_years"
  "schools"
  "modes_of_learning"
  "semesters"
  "years_of_study"
  "days"
  "roles"
  "users"
  "programmes"
  "course_units"
  "academic_sessions"
  "academic_session_programme"
  "course_unit_programme_mappings"
  "personal_access_tokens"
  "role_user"
  "programme_timetable"
  "exams"
  "exam_schedules"
  "sessions"
  "password_resets"
  "cache"
  "cache_locks"
  "jobs"
  "job_batches"
  "permissions"
)

mkdir -p "$TMP_DIR"

echo "======================================"
echo " OUK MySQL → PostgreSQL Data Import"
echo "======================================"
echo ""

# First, disable FK checks on PG side
psql -U $PG_USER -d $PG_DB -c "SET session_replication_role = 'replica';" 2>/dev/null

for TABLE in "${TABLES[@]}"; do
  echo "→ Exporting: $TABLE"

  # Export from MySQL as CSV via SSH
  eval "$SSH_CMD" "\"$MYSQL_CMD --batch --silent -e \\\"
    SELECT * INTO OUTFILE '/tmp/${TABLE}.csv'
    FIELDS TERMINATED BY ','
    OPTIONALLY ENCLOSED BY '\\\\\"'
    LINES TERMINATED BY '\\\\n'
    FROM \\\`${TABLE}\\\`;
  \\\"\" 2>/dev/null || echo '  (OUTFILE failed, using alternative)'"

  # Alternative: use mysql -e with tab-separated output
  eval "$SSH_CMD" "\"$MYSQL_CMD --batch -e 'SELECT * FROM \\\`${TABLE}\\\`;'\"" 2>/dev/null > "$TMP_DIR/${TABLE}.tsv"

  ROW_COUNT=$(wc -l < "$TMP_DIR/${TABLE}.tsv" 2>/dev/null || echo 0)

  if [ "$ROW_COUNT" -gt 1 ]; then
    echo "  ✓ Got $((ROW_COUNT - 1)) rows"

    # Truncate PG table first
    psql -U $PG_USER -d $PG_DB -c "TRUNCATE TABLE \"$TABLE\" CASCADE;" 2>/dev/null

    # Import into PostgreSQL (skip header row)
    tail -n +2 "$TMP_DIR/${TABLE}.tsv" | \
      psql -U $PG_USER -d $PG_DB -c "\COPY \"$TABLE\" FROM STDIN WITH (FORMAT text, DELIMITER E'\t', NULL '\\N');" 2>&1

    echo "  ✓ Imported into PostgreSQL"
  else
    echo "  ⚠ Empty or skipped: $TABLE"
  fi
  echo ""
done

# Re-enable FK checks
psql -U $PG_USER -d $PG_DB -c "SET session_replication_role = 'origin';" 2>/dev/null

# Reset all sequences to match imported data
echo "→ Resetting PostgreSQL sequences..."
psql -U $PG_USER -d $PG_DB << 'EOF'
DO $$
DECLARE
  rec RECORD;
  max_id BIGINT;
BEGIN
  FOR rec IN
    SELECT table_name, column_name
    FROM information_schema.columns
    WHERE table_schema = 'public'
      AND column_default LIKE 'nextval%'
      AND column_name = 'id'
  LOOP
    BEGIN
      EXECUTE format('SELECT COALESCE(MAX(%I), 0) FROM %I', rec.column_name, rec.table_name) INTO max_id;
      EXECUTE format('SELECT setval(pg_get_serial_sequence(%L, %L), GREATEST(%s, 1))', rec.table_name, rec.column_name, max_id);
      RAISE NOTICE 'Reset sequence for %.% to %', rec.table_name, rec.column_name, max_id;
    EXCEPTION WHEN OTHERS THEN
      RAISE NOTICE 'Skipped %: %', rec.table_name, SQLERRM;
    END;
  END LOOP;
END $$;
EOF

echo ""
echo "======================================"
echo " ✅ Migration complete!"
echo "======================================"

# Cleanup
rm -rf "$TMP_DIR"
