<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\CourseMappingController;
use App\Http\Controllers\CourseUnitController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\LessonSlotController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\YearOfStudyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('instructors.index');
});

Route::resource('instructors', LecturerController::class);
Route::get('lecturers/upload', [LecturerController::class, 'showUploadForm'])->name('instructors.upload');
Route::post('lecturers/import', [LecturerController::class, 'importLecturers'])->name('instructors.import');

Route::resource('academic_years', AcademicYearController::class);
Route::resource('schools', SchoolController::class);
Route::resource('programmes', ProgrammeController::class);
Route::get('programmes/{programme}', [ProgrammeController::class, 'show'])->name('programmes.show');
Route::post('programmes/{programme}/add-course-unit', [ProgrammeController::class, 'addCourseUnit'])->name('programmes.add_course_unit');
Route::delete('programmes/{programme}/remove-course-unit/{courseUnit}', [ProgrammeController::class, 'removeCourseUnit'])->name('programmes.remove_course_unit');

Route::resource('semesters', SemesterController::class);
Route::resource('years_of_study', YearOfStudyController::class);

Route::controller(CourseUnitController::class)->group(function () {
    Route::get('course_units/upload', 'showUploadForm')->name('course_units.upload');
    Route::post('course_units/import', 'importCourseUnits')->name('course_units.import');
    Route::get('course_units/sample-csv', 'downloadSampleCsv')->name('course_units.sample');
});

Route::resource('course_units', CourseUnitController::class);

Route::post('programmes/{programme}/course-units/{courseUnit}/add-instructor', [ProgrammeController::class, 'addInstructor'])->name('programmes.add_instructor');
Route::delete('programmes/{programme}/course-units/{courseUnit}/remove-instructor/{lecturer}', [ProgrammeController::class, 'removeInstructor'])->name('programmes.remove_instructor');
// Route::post('programmes/{programme}/course-units/{courseUnit}/assign-slot', [LessonSlotController::class, 'store'])->name('lesson_slots.store');

Route::post('programmes/{programme}/course-units/{courseUnit}/assign-slot', [LessonSlotController::class, 'store'])->name('lesson_slots.store');

// Route::get('course-units/{courseUnit}/edit', [CourseUnitController::class, 'edit'])->name('course_units.edit');
Route::put('programmes/{programme}/course-units/{courseUnit}/lesson-slots/{lessonSlot}',
    [LessonSlotController::class, 'update'])->name('lesson_slots.update');

Route::get('timetable', [TimetableController::class, 'index'])->name('timetable.index');

Route::get('course-mapping/upload', [CourseMappingController::class, 'showUploadForm'])->name('course_mapping.upload');
Route::post('course-mapping/import', [CourseMappingController::class, 'importCourseMappings'])->name('course_mapping.import');
Route::get('course-mapping/sample-csv', [CourseMappingController::class, 'downloadSampleCsv'])->name('course_mapping.sample');
