<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseMappingController;
use App\Http\Controllers\CourseUnitController;
use App\Http\Controllers\CurriculumSetupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LecturerController;
use App\Http\Controllers\LessonSlotController;
use App\Http\Controllers\ProgrammeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\TimetableController;
use App\Http\Controllers\YearOfStudyController;
use App\Http\Controllers\RoleController;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('timetable.index');
});

// Welcome page route
Route::get('/welcome', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Role Management
    Route::middleware('auth')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/{user}', [RoleController::class, 'edit'])->name('roles.edit');
        Route::patch('/roles/{user}', [RoleController::class, 'update'])->name('roles.update');
    });

    Route::patch('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');

    // Profile additional routes
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    Route::post('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
    Route::get('/profile/notifications/{notification}', [ProfileController::class, 'showNotification'])->name('profile.notifications.show');
    Route::delete('/profile/notifications/{notification}', [ProfileController::class, 'markAsRead'])->name('profile.notifications.markAsRead');
});

Route::get('timetable', [TimetableController::class, 'index'])->name('timetable.index');

// Instructor routes
Route::resource('instructors', LecturerController::class);
Route::get('lecturers/upload', [LecturerController::class, 'showUploadForm'])->name('instructors.upload');
Route::post('lecturers/import', [LecturerController::class, 'importLecturers'])->name('instructors.import');

// Academic Year routes
Route::resource('academic_years', AcademicYearController::class);

// School routes
Route::resource('schools', SchoolController::class);

// Programme routes
Route::resource('programmes', ProgrammeController::class);
Route::get('programmes/{programme}', [ProgrammeController::class, 'show'])->name('programmes.show');
Route::post('programmes/{programme}/add-course-unit', [ProgrammeController::class, 'addCourseUnit'])->name('programmes.add_course_unit');
Route::delete('programmes/{programme}/remove-course-unit/{courseUnit}', [ProgrammeController::class, 'removeCourseUnit'])->name('programmes.remove_course_unit');

// Semester routes
Route::resource('semesters', SemesterController::class);

// Year of Study routes
Route::resource('years_of_study', YearOfStudyController::class);

// Course Unit routes
Route::controller(CourseUnitController::class)->group(function () {
    Route::get('course_units/upload', 'showUploadForm')->name('course_units.upload');
    Route::post('course_units/import', 'importCourseUnits')->name('course_units.import');
    Route::get('course_units/sample-csv', 'downloadSampleCsv')->name('course_units.sample');
});
Route::resource('course_units', CourseUnitController::class);

// Programme Course Unit Instructor routes
Route::post('programmes/{programme}/course-units/{courseUnit}/add-instructor', [ProgrammeController::class, 'addInstructor'])->name('programmes.add_instructor');
Route::delete('programmes/{programme}/course-units/{courseUnit}/remove-instructor/{lecturer}', [ProgrammeController::class, 'removeInstructor'])->name('programmes.remove_instructor');

// Lesson Slot routes
Route::post('programmes/{programme}/course-units/{courseUnit}/assign-slot', [LessonSlotController::class, 'store'])->name('lesson_slots.store');
Route::put('programmes/{programme}/course-units/{courseUnit}/lesson-slots/{lessonSlot}',
    [LessonSlotController::class, 'update'])->name('lesson_slots.update');

// Course Mapping routes
Route::get('course-mapping/upload', [CourseMappingController::class, 'showUploadForm'])->name('course_mapping.upload');
Route::post('course-mapping/import', [CourseMappingController::class, 'importCourseMappings'])->name('course_mapping.import');
Route::get('course-mapping/sample-csv', [CourseMappingController::class, 'downloadSampleCsv'])->name('course_mapping.sample');
Route::get('/get-programmes', [ProgrammeController::class, 'getProgrammes'])->name('get.programmes');

// Timetable Export
Route::get('/timetable/export-pdf', [TimetableController::class, 'exportPDF'])->name('timetable.export.pdf');

// Instructor Search
Route::get('/search-instructors', function (Request $request) {
    $query = $request->input('q');
    $instructors = Lecturer::where('name', 'LIKE', "%{$query}%")
        ->orWhereHas('title', function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get();
    return response()->json($instructors);
})->name('search.instructors');

// Curriculum Setup routes
Route::prefix('curriculum')->name('curriculum.')->group(function () {
    Route::get('/', [CurriculumSetupController::class, 'index'])->name('index');
    Route::get('/programme/{programme}', [CurriculumSetupController::class, 'show'])->name('show');
    Route::post('/map', [CurriculumSetupController::class, 'store'])->name('map');
    Route::delete('/mapping/{id}', [CurriculumSetupController::class, 'destroy'])->name('unmap');
    Route::post('/bulk-upload', [CurriculumSetupController::class, 'bulkUpload'])->name('bulk-upload');
    Route::get('/sample-download', [CurriculumSetupController::class, 'downloadSample'])->name('download-sample');
});

require __DIR__.'/auth.php';
