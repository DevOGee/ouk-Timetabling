<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $year
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AcademicYear whereYear($value)
 */
	class AcademicYear extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $color
 * @property int $year_of_study_id
 * @property int $semester_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lecturer> $instructors
 * @property-read int|null $instructors_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LessonSlot> $lessonSlots
 * @property-read int|null $lesson_slots_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Programme> $programmes
 * @property-read int|null $programmes_count
 * @property-read \App\Models\Semester $semester
 * @property-read \App\Models\YearOfStudy $yearOfStudy
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereSemesterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseUnit whereYearOfStudyId($value)
 */
	class CourseUnit extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Day newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Day newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Day query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Day whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Day whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Day whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Day whereUpdatedAt($value)
 */
	class Day extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $title_id
 * @property string $name
 * @property string $email
 * @property string|null $image_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseUnit> $courseUnits
 * @property-read int|null $course_units_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseUnit> $courses
 * @property-read int|null $courses_count
 * @property-read \App\Models\Title $title
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer whereImagePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer whereTitleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Lecturer whereUpdatedAt($value)
 */
	class Lecturer extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $course_unit_id
 * @property int $programme_id
 * @property int $day_id
 * @property string $start_time
 * @property int $duration
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CourseUnit $courseUnit
 * @property-read \App\Models\Day $day
 * @property-read \App\Models\Programme $programme
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereCourseUnitId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereDayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereProgrammeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LessonSlot whereUpdatedAt($value)
 */
	class LessonSlot extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $school_id
 * @property string $name
 * @property string $programme_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseUnit> $courseUnits
 * @property-read int|null $course_units_count
 * @property-read \App\Models\School $school
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereProgrammeCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Programme whereUpdatedAt($value)
 */
	class Programme extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Programme> $programmes
 * @property-read int|null $programmes_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|School whereUpdatedAt($value)
 */
	class School extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Semester whereUpdatedAt($value)
 */
	class Semester extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lecturer> $lecturers
 * @property-read int|null $lecturers_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Title newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Title newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Title query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Title whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Title whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Title whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Title whereUpdatedAt($value)
 */
	class Title extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|YearOfStudy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|YearOfStudy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|YearOfStudy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|YearOfStudy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|YearOfStudy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|YearOfStudy whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|YearOfStudy whereUpdatedAt($value)
 */
	class YearOfStudy extends \Eloquent {}
}

