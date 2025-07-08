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
use App\Http\Controllers\Admin\CurriculumController;
use App\Http\Controllers\Admin\CurriculumMappingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Admin\AcademicSessionController;
use App\Http\Controllers\Admin\DashboardController;
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

    // Profile notifications routes
    Route::get('/profile/notifications', [ProfileController::class, 'notifications'])->name('profile.notifications');
    Route::patch('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications.update');
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

// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Programme Management
    Route::resource('programmes', ProgrammeController::class, [
        'names' => [
            'index' => 'admin.programmes.index',
            'create' => 'admin.programmes.create',
            'store' => 'admin.programmes.store',
            'show' => 'admin.programmes.show',
            'edit' => 'admin.programmes.edit',
            'update' => 'admin.programmes.update',
            'destroy' => 'admin.programmes.destroy',
        ]
    ]);
    Route::get('programmes/bulk-upload', [ProgrammeController::class, 'showBulkUploadForm'])->name('admin.programmes.bulk-upload');
    Route::post('programmes/process-bulk-upload', [ProgrammeController::class, 'processBulkUpload'])->name('admin.programmes.process-bulk-upload');
    Route::get('programmes/download-template', [ProgrammeController::class, 'downloadTemplate'])->name('admin.programmes.download-template');

    // Academic Sessions
    // Academic Sessions resource with show method included
    Route::resource('academic-sessions', AcademicSessionController::class, [
        'parameters' => ['academic-session' => 'academicSession'],
        'names' => [
            'index' => 'admin.academic-sessions.index',
            'create' => 'admin.academic-sessions.create',
            'store' => 'admin.academic-sessions.store',
            'show' => 'admin.academic-sessions.show',
            'edit' => 'admin.academic-sessions.edit',
            'update' => 'admin.academic-sessions.update',
            'destroy' => 'admin.academic-sessions.destroy',
        ]
    ]);
    
    Route::prefix('academic-sessions/{academicSession}')->name('admin.academic-sessions.')->group(function () {
        Route::get('select-programmes', [\App\Http\Controllers\Admin\ProgrammeMappingController::class, 'selectProgrammes'])
            ->name('select-programmes');
        Route::post('programmes', [\App\Http\Controllers\Admin\ProgrammeMappingController::class, 'storeProgrammes'])
            ->name('programmes.store');
            
        // Course unit mappings for programmes
        Route::prefix('programmes/{programme}')->name('programmes.')->group(function () {
            Route::get('map-course-units', [\App\Http\Controllers\Admin\ProgrammeMappingController::class, 'mapCourseUnits'])
                ->name('map-course-units');
            Route::post('course-units', [\App\Http\Controllers\Admin\ProgrammeMappingController::class, 'storeCourseUnits'])
                ->name('course-units.store');
            Route::delete('detach', [\App\Http\Controllers\Admin\ProgrammeMappingController::class, 'detach'])
                ->name('detach');
        });
        
        // Curriculum routes
        // Roll forward route must come before resource to avoid conflict
        Route::post('curricula/roll-forward/{sourceCurriculum}', [CurriculumController::class, 'rollForward'])
            ->name('curricula.roll-forward');
            
        // Set active curriculum
        Route::patch('curricula/{curriculum}/set-active', [CurriculumController::class, 'setActive'])
            ->name('curricula.set-active');
            
        // Curriculum resource
        Route::resource('curricula', CurriculumController::class, [
            'names' => [
                'index' => 'curricula.index',
                'create' => 'curricula.create',
                'store' => 'curricula.store',
                'show' => 'curricula.show',
                'edit' => 'curricula.edit',
                'update' => 'curricula.update',
                'destroy' => 'curricula.destroy',
            ]
        ]);
        
        // Curriculum mappings
        Route::prefix('curricula/{curriculum}')->name('curricula.')->group(function () {
            Route::get('mappings', [CurriculumMappingController::class, 'index'])->name('mappings.index');
            Route::get('mappings/create', [CurriculumMappingController::class, 'create'])->name('mappings.create');
            Route::post('mappings', [CurriculumMappingController::class, 'store'])->name('mappings.store');
            Route::get('mappings/{mapping}/edit', [CurriculumMappingController::class, 'edit'])->name('mappings.edit');
            Route::put('mappings/{mapping}', [CurriculumMappingController::class, 'update'])->name('mappings.update');
            Route::delete('mappings/{mapping}', [CurriculumMappingController::class, 'destroy'])->name('mappings.destroy');
            
            // Programme course units
            Route::get('programmes/{programme}/course-units', [CurriculumController::class, 'getProgrammeCourseUnits'])
                ->name('programmes.course-units');
        });
        
        Route::patch('set-current', [AcademicSessionController::class, 'setCurrent'])
            ->name('set-current');
        Route::patch('archive', [AcademicSessionController::class, 'archive'])
            ->name('archive');
    })->withoutMiddleware(['auth']);
});

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

// Curriculum Setup Routes - Now scoped under active academic session and curriculum
Route::middleware(['auth'])->group(function () {
    // Main curriculum setup page for the active curriculum
    Route::get('/curriculum', [CurriculumSetupController::class, 'index'])
        ->name('curriculum.index');
        
    // Programme-specific curriculum management
    Route::prefix('curriculum/programme/{programme}')->group(function () {
        // Show programme curriculum
        Route::get('/', [CurriculumSetupController::class, 'show'])
            ->name('curriculum.show');
            
        // Add course unit to programme curriculum
        Route::post('/mapping', [CurriculumSetupController::class, 'store'])
            ->name('curriculum.mapping.store');
            
        // Remove course unit from programme curriculum
        Route::delete('/mapping/{mapping}', [CurriculumSetupController::class, 'destroy'])
            ->name('curriculum.mapping.destroy');
    });
    
    // Bulk operations
    Route::prefix('curriculum')->group(function () {
        // Bulk upload mappings
        Route::post('/bulk-upload', [CurriculumSetupController::class, 'bulkUpload'])
            ->name('curriculum.bulk-upload');
            
        // Download sample import file
        Route::get('/download-sample', [CurriculumSetupController::class, 'downloadSample'])
            ->name('curriculum.download-sample');
    });
});

require __DIR__.'/auth.php';
