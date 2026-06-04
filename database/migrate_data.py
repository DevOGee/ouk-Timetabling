#!/usr/bin/env python3
"""
MySQL → PostgreSQL migration v4.
Uses subprocess with list args (no shell quoting issues) and
directly pipes MySQL output into PostgreSQL COPY.
"""

import subprocess, os, io, sys
import psycopg2

# ── Connection settings ────────────────────────────────────────────────────────
SSH_ARGS = [
    "sshpass", "-p", "jar3d@g2345U",
    "ssh", "-o", "StrictHostKeyChecking=no", "-p", "19873",
    "jared@102.210.148.223",
]
MYSQL_ARGS = [
    "mysql",
    "-h", "planner.ouk.ac.ke",
    "-u", "bentito",
    "-pSpoiler@2050#",
    "planner_prod",
    "--batch", "--silent",
]
PG_DSN = "dbname=ouk_timetabling user=mwarabu host=127.0.0.1"

# ── Tables in FK-dependency order ─────────────────────────────────────────────
TABLES = [
    "titles", "academic_years", "schools", "modes_of_learning",
    "semesters", "years_of_study", "days", "roles", "users",
    "programmes", "course_units", "academic_sessions",
    "academic_session_programme", "course_unit_programme_mappings",
    "personal_access_tokens", "role_user", "programme_timetable",
    "exams", "exam_schedules", "sessions", "password_resets",
    "cache", "cache_locks", "jobs", "job_batches",
]

# ── Helpers ───────────────────────────────────────────────────────────────────
def mysql_query(sql):
    """Run a MySQL query via SSH and return output lines."""
    remote_cmd = " ".join(MYSQL_ARGS) + f" -e '{sql}'"
    result = subprocess.run(
        SSH_ARGS + [remote_cmd],
        capture_output=True, text=True
    )
    return result.stdout.splitlines()

def get_pg_cols(conn, table):
    cur = conn.cursor()
    cur.execute("""
        SELECT column_name, data_type
        FROM information_schema.columns
        WHERE table_schema = 'public' AND table_name = %s
        ORDER BY ordinal_position
    """, (table,))
    return cur.fetchall()   # [(name, type), ...]

def clean(val, dtype):
    """Normalise a single MySQL TSV cell for PostgreSQL COPY."""
    if val in ("NULL", "\\N", "null", "None"):
        return ""
    if dtype in ("timestamp without time zone",
                 "timestamp with time zone", "date"):
        if val.startswith("0000") or val == "0":
            return ""
    if dtype == "time without time zone":
        # tinyint stored as '1'/'0' – shouldn't end up here but guard it
        if val in ("0", "1"):
            return ""
    return val

def migrate_table(conn, table):
    """Full export-and-import for one table."""
    # ── 1. PG schema ──────────────────────────────────────────────────────────
    pg_cols = get_pg_cols(conn, table)
    if not pg_cols:
        print("  ⚠ Not found in PG schema — skipping"); return 0

    pg_names = [c[0] for c in pg_cols]
    pg_types = {c[0]: c[1] for c in pg_cols}

    # ── 2. MySQL columns ──────────────────────────────────────────────────────
    raw = mysql_query(f"SHOW COLUMNS FROM `{table}`;")
    mysql_names = [r.split("\t")[0] for r in raw if r.strip()]
    if not mysql_names:
        print("  ⚠ Not found in MySQL — skipping"); return 0

    # ── 3. Intersection (preserve PG order) ───────────────────────────────────
    common = [c for c in pg_names if c in mysql_names]
    if not common:
        print(f"  ⚠ No overlapping columns"); return 0

    # ── 4. Export from MySQL ───────────────────────────────────────────────────
    sel = ", ".join(f"`{c}`" for c in common)
    rows = mysql_query(f"SELECT {sel} FROM `{table}`;")
    if not rows:
        print("  ⚠ Empty table"); return 0

    print(f"  ↓ {len(rows)} rows × {len(common)} cols from MySQL")

    # ── 5. Build cleaned TSV buffer ───────────────────────────────────────────
    buf = io.StringIO()
    for row in rows:
        fields = row.split("\t")
        out = []
        for i, col in enumerate(common):
            val = fields[i] if i < len(fields) else ""
            out.append(clean(val, pg_types.get(col, "text")))
        buf.write("\t".join(out) + "\n")
    buf.seek(0)

    # ── 6. Truncate & COPY into PostgreSQL ────────────────────────────────────
    cur = conn.cursor()
    try:
        cur.execute(f'TRUNCATE TABLE "{table}" CASCADE')
        conn.commit()
    except Exception as e:
        conn.rollback()
        print(f"  ⚠ Truncate error: {e}")

    col_list = ", ".join(f'"{c}"' for c in common)
    copy_sql = (
        f'COPY "{table}" ({col_list}) FROM STDIN '
        f"WITH (FORMAT text, DELIMITER E'\\t', NULL '')"
    )
    try:
        cur.copy_expert(copy_sql, buf)
        conn.commit()
        return len(rows)
    except Exception as e:
        conn.rollback()
        print(f"  ✗ COPY error: {e}")
        return 0

def reset_sequences(conn):
    cur = conn.cursor()
    cur.execute("""
        SELECT table_name, column_name
        FROM information_schema.columns
        WHERE table_schema = 'public'
          AND column_default LIKE 'nextval%%'
          AND column_name = 'id'
    """)
    for tbl, col in cur.fetchall():
        try:
            c2 = conn.cursor()
            c2.execute(f'SELECT COALESCE(MAX("{col}"), 1) FROM "{tbl}"')
            mx = c2.fetchone()[0]
            c2.execute(
                f"SELECT setval(pg_get_serial_sequence('{tbl}', '{col}'), {max(mx, 1)})"
            )
            conn.commit()
            if mx > 1:
                print(f"  ✓ {tbl}.{col} → {mx}")
        except Exception as e:
            conn.rollback()

# ── Main ──────────────────────────────────────────────────────────────────────
def main():
    print("=" * 54)
    print("  OUK MySQL → PostgreSQL Migration (v4)")
    print("=" * 54)

    conn = psycopg2.connect(PG_DSN)
    conn.cursor().execute("SET session_replication_role = 'replica'")
    conn.commit()

    results = {}
    for table in TABLES:
        print(f"\n→ {table}")
        results[table] = migrate_table(conn, table)
        if results[table]:
            print(f"  ✓ {results[table]} rows imported")

    conn.cursor().execute("SET session_replication_role = 'origin'")
    conn.commit()

    print("\n→ Resetting sequences …")
    reset_sequences(conn)
    conn.close()

    print("\n" + "=" * 54)
    print("  Summary")
    print("=" * 54)
    total = 0
    for t, c in results.items():
        icon = "✓" if c > 0 else "⚠"
        print(f"  {icon}  {t:<46} {c:>5}")
        total += c
    print(f"\n  Total rows imported: {total}")
    print("=" * 54)
    print("  ✅ Done!")
    print("=" * 54)

if __name__ == "__main__":
    main()
