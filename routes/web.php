<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CourseMappingController;
use App\Http\Controllers\CourseUnitController;
use App\Http\Controllers\CurriculumSetupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstructorController;
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
use App\Http\Controllers\Admin\TimetableManagementController;
use App\Http\Controllers\Instructor\InstructorDashboardController;
use App\Models\User;
use App\Models\Programme;
use App\Models\CourseUnit;
use App\Models\School;
use App\Models\YearOfStudy;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Admin\ProgrammeMappingController;
use App\Http\Controllers\Admin\ProgrammeSchedulingController;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\AcademicSession;
use App\Models\AcademicYear;
use App\Http\Controllers\Api\TimetableApiController;    


// Clear rate limiter - Remove this in production
// Route::get('/clear-limiter', function () {
//     $throttleKey = 'password.request|' . request()->ip();
//     cache()->forget($throttleKey);
//     return 'Rate limiter cleared for IP: ' . request()->ip();
// });

// API Routes
Route::prefix('api')->group(function () {
    // Get levels with timetables for a programme
    Route::get('levels-with-timetables', [TimetableApiController::class, 'getLevelsWithTimetables'])
        ->name('api.levels-with-timetables');
        
    // Get programmes by school
    Route::get('programmes-by-school', [TimetableApiController::class, 'getProgrammesBySchool'])
        ->name('api.programmes-by-school');
});

// Test email route - Remove this in production
Route::get('/test-email', function () {
    try {
        Mail::raw('This is a test email from OUK Timetable System', function($message) {
            $message->to('bentito@ouk.ac.ke')
                    ->subject('Test Email from OUK Timetable');
        });
        return 'Test email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Test bulk upload form - Remove this in production
Route::get('/test-bulk-upload', function () {
    $roles = \Spatie\Permission\Models\Role::all();
    return view('admin.users.index', compact('roles'));
})->middleware('auth');

// Authentication Routes
// Register authentication routes except registration
Auth::routes([
    'verify' => true,
    'register' => false, // Disable registration routes
    'reset' => true,    // Keep password reset functionality
    'confirm' => true,  // Keep password confirmation
    'verify' => true,   // Keep email verification
]);

// Google OAuth Routes
Route::middleware(['web', 'guest'])->group(function () {
    Route::get('/login/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
});

use App\Http\Controllers\WelcomeController;

// Root URL serves the landing page
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Main dashboard route
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Management
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
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
// Instructor Management
Route::resource('instructors', InstructorController::class);
Route::get('instructors/upload', [InstructorController::class, 'showUploadForm'])->name('instructors.upload');
Route::post('instructors/import', [InstructorController::class, 'importInstructors'])->name('instructors.import');

// Instructor Dashboard Routes
Route::prefix('instructor')->middleware(['auth'])->group(function () {
    Route::get('/my-course-units', [InstructorDashboardController::class, 'myCourseUnits'])
        ->name('instructor.course-units');
        
    Route::get('/my-timetable', [InstructorDashboardController::class, 'myTimetable'])
        ->name('instructor.timetable');
});

// Academic Year routes
Route::resource('academic_years', AcademicYearController::class);

// School routes
Route::resource('schools', SchoolController::class);

// Debug route for timetable status
Route::get('/debug/timetable-status/{academicSessionId?}', function($academicSessionId = null) {
    $academicSessionId = $academicSessionId ?? 3; // Default to session 3 if not provided
    
    $programmes = Programme::with(['courseUnitMappings' => function($q) use ($academicSessionId) {
        $q->where('academic_session_id', $academicSessionId);
    }])->get();
    
    // Get some sample mappings for detailed view
    $sampleMappings = CourseUnitProgrammeMapping::with(['programme', 'courseUnit', 'instructor', 'day'])
        ->where('academic_session_id', $academicSessionId)
        ->limit(5)
        ->get();
    
    return view('debug.timetable-status', [
        'programmes' => $programmes,
        'sampleMappings' => $sampleMappings,
        'academicSessionId' => $academicSessionId
    ]);
})->name('debug.timetable-status');

// Semester routes
Route::resource('semesters', SemesterController::class);

// Year of Study routes
Route::resource('years_of_study', YearOfStudyController::class)->parameters([
    'years_of_study' => 'yearOfStudy'
]);

// Course Unit routes
Route::controller(CourseUnitController::class)->group(function () {
    Route::get('course_units/upload', 'showUploadForm')->name('course_units.upload');
    Route::post('course_units/import', 'importCourseUnits')->name('course_units.import');
    Route::get('course_units/sample-csv', 'downloadSampleCsv')->name('course_units.sample');
});
Route::resource('course_units', CourseUnitController::class);

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SettingsController;

// Admin routes
Route::prefix('admin')->middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('dashboard/todays-events', [\App\Http\Controllers\DashboardController::class, 'todaysEvents'])->name('dashboard.todays-events');
    Route::post('users/{user}/toggle-zoom', [\App\Http\Controllers\DashboardController::class, 'toggleZoomLicense'])->name('users.toggle-zoom');

    // Email / SMTP Settings
    Route::get('settings/email', [SettingsController::class, 'emailSetup'])->name('admin.settings.email');
    Route::patch('settings/email', [SettingsController::class, 'updateEmailSetup'])->name('admin.settings.email.update');
    Route::post('settings/email/test', [SettingsController::class, 'testEmail'])->name('admin.settings.email.test');
    
    // Reports
    Route::prefix('reports')->name('admin.reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\ReportsController::class, 'index'])->name('index');
        
        // Instructor Schedules
        Route::get('instructor-schedules', [\App\Http\Controllers\Admin\ReportsController::class, 'instructorSchedules'])->name('instructor-schedules');
        Route::get('export-instructor-schedules/{format}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportInstructorSchedules'])->name('export-instructor-schedules');
        
        // Class Schedules
        Route::get('class-schedules', [\App\Http\Controllers\Admin\ReportsController::class, 'classSchedules'])->name('class-schedules');
        Route::post('export-class-schedules/{format?}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportClassSchedules'])
            ->where('format', 'xlsx|pdf')
            ->name('export-class-schedules');
            
        // Workload Distribution
        Route::get('workload-distribution', [\App\Http\Controllers\Admin\ReportsController::class, 'workloadDistribution'])->name('workload-distribution');
        Route::get('export-workload-distribution/{format}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportWorkloadDistribution'])->name('export-workload-distribution');
        
        // Time Conflicts
        Route::get('time-conflicts', [\App\Http\Controllers\Admin\ReportsController::class, 'timeConflicts'])->name('time-conflicts');
        Route::get('export-time-conflicts/{format}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportTimeConflicts'])
            ->name('export-time-conflicts')
            ->where('format', 'xlsx|pdf');
            
        // Lecturer Conflicts
        Route::get('lecturer-conflicts', [\App\Http\Controllers\Admin\ReportsController::class, 'lecturerConflicts'])->name('lecturer-conflicts');
        Route::get('export-lecturer-conflicts/{format}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportLecturerConflicts'])
            ->name('export-lecturer-conflicts')
            ->where('format', 'xlsx|pdf');
        
        // Legacy export route
        Route::get('/export/{format}', [\App\Http\Controllers\Admin\ReportsController::class, 'exportInstructorSchedules'])
            ->name('export')
            ->where('format', 'excel|pdf');
            
        // Exam Conflicts Report
        Route::get('exam-conflicts', [\App\Http\Controllers\Admin\ExamReportController::class, 'conflicts'])
            ->name('exam-conflicts');

        // Today's Classes Report
        Route::get('todays-classes', [\App\Http\Controllers\Admin\ReportsController::class, 'todaysClasses'])->name('todays-classes');
    });
    
    // User Management
    Route::prefix('users')->name('admin.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::post('/import', [UserController::class, 'import'])->name('import');
        // Add other user management routes as needed
    });
    
    // Timetable Management
    Route::prefix('timetables')->name('admin.timetables.')->group(function () {
        Route::get('/', [TimetableManagementController::class, 'index'])->name('manage');
        Route::get('/create', [TimetableManagementController::class, 'create'])->name('create');
        Route::post('/', [TimetableManagementController::class, 'store'])->name('store');
        Route::post('/{timetable}/publish', [TimetableManagementController::class, 'publish'])
            ->name('publish')
            ->where('timetable', '[0-9]+');
        Route::post('/{timetable}/unpublish', [TimetableManagementController::class, 'unpublish'])
            ->name('unpublish')
            ->where('timetable', '[0-9]+');
        Route::delete('/{timetable}', [TimetableManagementController::class, 'destroy'])
            ->name('destroy')
            ->where('timetable', '[0-9]+');
    });
    
    // User Management
    Route::resource('users', RoleController::class, [
        'names' => [
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'store' => 'admin.users.store',
            'show' => 'admin.users.show',
            'edit' => 'admin.users.edit',
            'update' => 'admin.users.update',
            'destroy' => 'admin.users.destroy',
        ]
    ]);
    Route::patch('users/{user}/toggle-status', [RoleController::class, 'toggleStatus'])->name('admin.users.toggle-status');
    Route::post('users/{user}/assign-role', [RoleController::class, 'assign'])->name('admin.users.assign-role');
    Route::post('users/{user}/remove-role', [RoleController::class, 'remove'])->name('admin.users.remove-role');

    // Bulk Zoom license management
    Route::post('zoom/unassign-non-thursday', [RoleController::class, 'zoomUnassignNonThursday'])->name('admin.zoom.unassign-non-thursday');
    Route::post('zoom/assign-thursday', [RoleController::class, 'zoomAssignThursday'])->name('admin.zoom.assign-thursday');
    
    // User Import/Export
    Route::get('users/import', [RoleController::class, 'showImportForm'])->name('admin.users.import.form');
    Route::post('users/import', [RoleController::class, 'import'])->name('admin.users.import');
    Route::get('users/export', [RoleController::class, 'export'])->name('admin.users.export');
    
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
    
    // Additional programme routes
    Route::post('programmes/{programme}/add-course-unit', [ProgrammeController::class, 'addCourseUnit'])->name('admin.programmes.add_course_unit');
    Route::delete('programmes/{programme}/remove-course-unit/{courseUnit}', [ProgrammeController::class, 'removeCourseUnit'])->name('admin.programmes.remove_course_unit');
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
    
    // Route for copying course unit mappings from another session
    Route::post('academic-sessions/{academicSession}/copy-mappings', [AcademicSessionController::class, 'copyMappings'])
        ->name('admin.academic-sessions.copy-mappings');
    
    Route::prefix('academic-sessions/{academicSession}')->name('admin.academic-sessions.')->group(function () {
        Route::get('select-programmes', [ProgrammeMappingController::class, 'selectProgrammes'])
            ->name('select-programmes');
        Route::post('programmes', [ProgrammeMappingController::class, 'storeProgrammes'])
            ->name('programmes.store');
            
        // Bulk upload course unit mappings
        Route::get('bulk-upload', [ProgrammeMappingController::class, 'showBulkUploadForm'])
            ->name('bulk-upload');
        Route::post('bulk-upload', [ProgrammeMappingController::class, 'processBulkUpload'])
            ->name('bulk-upload.process');
            
        // Download bulk upload report
        Route::get('bulk-upload/report/{filename}', [ProgrammeMappingController::class, 'downloadReport'])
            ->name('bulk-upload.report');
            
        // Course unit mappings for programmes
        Route::prefix('programmes/{programme}')->name('programmes.')->group(function () {
            Route::get('map-course-units', [ProgrammeMappingController::class, 'mapCourseUnits'])
                ->name('map-course-units');
            Route::post('course-units', [ProgrammeMappingController::class, 'addCourseUnit'])
                ->name('course-units.add');
            Route::delete('course-units/{courseUnit}', [ProgrammeMappingController::class, 'removeCourseUnit'])
                ->name('course-units.remove');
            Route::delete('detach', [ProgrammeMappingController::class, 'detach'])
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

    // Programme routes
    Route::resource('programmes', ProgrammeController::class);
    Route::get('programmes/upload', [ProgrammeController::class, 'showUploadForm'])->name('admin.programmes.upload');
    Route::post('programmes/import', [ProgrammeController::class, 'importProgrammes'])->name('admin.programmes.import');
    
    // Programme scheduling routes (scoped to academic session)
    Route::prefix('academic-sessions/{academicSession}/programmes/{programme}')->name('admin.academic-sessions.programmes.')->group(function () {
        // View schedules
        Route::get('scheduling', [ProgrammeSchedulingController::class, 'show'])->name('scheduling.show');
        
        // Instructor management
        Route::post('add-instructor', [ProgrammeSchedulingController::class, 'addInstructor'])->name('scheduling.add-instructor');
        Route::delete('remove-instructor', [ProgrammeSchedulingController::class, 'removeInstructor'])->name('scheduling.remove-instructor');
        
        // Schedule management
        Route::post('assign-slot', [ProgrammeSchedulingController::class, 'assignSlot'])->name('scheduling.assign-slot');
        Route::put('update-slot/{mapping}', [ProgrammeSchedulingController::class, 'updateSlot'])->name('scheduling.update-slot');
        Route::delete('delete-slot/{mapping}', [ProgrammeSchedulingController::class, 'deleteSlot'])->name('scheduling.delete-slot');
        Route::post('bulk-schedule', [ProgrammeSchedulingController::class, 'bulkSchedule'])->name('scheduling.bulk-schedule');
        Route::get('download-courses', [ProgrammeSchedulingController::class, 'downloadCourses'])->name('scheduling.download-courses');
        Route::get('export-schedule', [ProgrammeSchedulingController::class, 'exportSchedule'])
            ->name('scheduling.export')
            ->where('format', 'pdf|excel');
    });

    // Programme Course Unit Instructor routes
    Route::post('programmes/{programme}/course-units/{courseUnit}/add-instructor', [ProgrammeController::class, 'addInstructor'])->name('admin.programmes.add_instructor');
    Route::delete('programmes/{programme}/course-units/{courseUnit}/remove-instructor/{user}', [ProgrammeController::class, 'removeInstructor'])->name('admin.programmes.remove_instructor');

    // Lesson Slot routes
    Route::post('programmes/{programme}/course-units/{courseUnit}/assign-slot', [LessonSlotController::class, 'store'])->name('admin.lesson_slots.store');
    Route::put('programmes/{programme}/course-units/{courseUnit}/lesson-slots/{lessonSlot}',
        [LessonSlotController::class, 'update'])->name('admin.lesson_slots.update');

    // Course Mapping routes
    Route::get('course-mapping/upload', [CourseMappingController::class, 'showUploadForm'])->name('admin.course_mapping.upload');
    Route::post('course-mapping/import', [CourseMappingController::class, 'importCourseMappings'])->name('admin.course_mapping.import');
    Route::get('course-mapping/sample-csv', [CourseMappingController::class, 'downloadSampleCsv'])->name('admin.course_mapping.sample');
    Route::get('get-programmes', [ProgrammeController::class, 'getProgrammes'])->name('admin.get.programmes');

    // Timetable Export
    Route::get('/timetable/export-pdf', [TimetableController::class, 'exportPDF'])->name('timetable.export.pdf');
    Route::get('/timetable/sync-google-calendar', [App\Http\Controllers\GoogleCalendarController::class, 'generateIcs'])->name('timetable.sync.google');

// Instructor Search
Route::get('/search-instructors', function (Request $request) {
    $query = $request->input('q');
    $instructors = User::role('instructor')->where('name', 'LIKE', "%{$query}%")
        ->orWhereHas('title', function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%");
        })
        ->limit(10)
        ->get();
    return response()->json($instructors);
})->name('search.instructors');

// Public Examinations Timetable
Route::get('/examinations', [App\Http\Controllers\ExaminationsController::class, 'index'])->name('examinations.index');

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

// Exam Timetabling Routes
Route::prefix('admin/exams')->name('admin.exams.')->middleware(['auth'])->group(function () {
    Route::get('/', [App\Http\Controllers\Admin\ExamController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\Admin\ExamController::class, 'create'])->name('create');
    Route::post('/', [App\Http\Controllers\Admin\ExamController::class, 'store'])->name('store');
    Route::get('/{examSchedule}', [App\Http\Controllers\Admin\ExamController::class, 'show'])->name('show');
    Route::delete('/{examSchedule}', [App\Http\Controllers\Admin\ExamController::class, 'destroy'])->name('destroy');
    
    // Custom Actions
    Route::post('/{examSchedule}/rollover', [App\Http\Controllers\Admin\ExamController::class, 'rollover'])->name('rollover');
    Route::post('/{examSchedule}/import', [App\Http\Controllers\Admin\ExamController::class, 'import'])->name('import');
    Route::post('/{examSchedule}/publish', [App\Http\Controllers\Admin\ExamController::class, 'publish'])->name('publish');
    Route::post('/{examSchedule}/unpublish', [App\Http\Controllers\Admin\ExamController::class, 'unpublish'])->name('unpublish');
    Route::get('/{examSchedule}/export-unscheduled', [App\Http\Controllers\Admin\ExamController::class, 'exportUnscheduled'])->name('export-unscheduled');
    Route::post('/update-slot/{exam}', [App\Http\Controllers\Admin\ExamController::class, 'updateSlot'])->name('updateSlice');
});

// require __DIR__.'/auth.php'; // Commented out to prevent duplicate route definitions

// Block access to registration page using middleware
Route::match(['get', 'post'], '/register', function () {
    return redirect('/login');
})->name('register')->middleware('block.registration');
