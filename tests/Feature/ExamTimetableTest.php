<?php

namespace Tests\Feature;

use App\Models\AcademicSession;
use App\Models\CourseUnit;
use App\Models\CourseUnitProgrammeMapping;
use App\Models\Exam;
use App\Models\ExamSchedule;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use Maatwebsite\Excel\Facades\Excel;

class ExamTimetableTest extends TestCase
{
    // use RefreshDatabase; // Caution: RefreshDatabase wipes DB. User might not want that in dev env unless using sqlite memory.
    // Given the environment, I'll rely on manual cleanup or carefully scoped tests. 
    // Actually, standard Laravel tests use RefreshDatabase trait which uses transactions.
    use RefreshDatabase;

    protected $admin;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Setup Admin
        $this->admin = User::factory()->create();
        // Assuming custom roles
        $role = Role::firstOrCreate(['name' => 'admin']);
        $this->admin->roles()->attach($role);

        // Setup Data
        $this->session = AcademicSession::create([
            'name' => 'Test Session',
            'start_date' => now(),
            'end_date' => now()->addMonths(4)
        ]);
    }

    public function test_admin_can_create_exam_schedule()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.exams.store'), [
                'name' => 'Finals',
                'academic_session_id' => $this->session->id,
                'start_date' => now()->addMonth()->format('Y-m-d'),
                'end_date' => now()->addMonths(2)->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exam_schedules', ['name' => 'Finals']);
    }

    public function test_rollover_creates_exams()
    {
        // Create active mapping
        $programme = Programme::factory()->create();
        $course = CourseUnit::factory()->create(['code' => 'TEST101']);
        $mapping = CourseUnitProgrammeMapping::create([
            'programme_id' => $programme->id,
            'course_unit_id' => $course->id,
            'academic_session_id' => $this->session->id,
        ]);

        $schedule = ExamSchedule::create([
            'name' => 'Test Schedule',
            'academic_session_id' => $this->session->id,
            'start_date' => now(),
            'end_date' => now()->addMonth()
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.exams.rollover', $schedule));

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('exams', [
            'exam_schedule_id' => $schedule->id,
            'course_unit_id' => $course->id
        ]);
    }

    public function test_can_update_exam_slot()
    {
        $schedule = ExamSchedule::create([
            'name' => 'Test Schedule',
            'academic_session_id' => $this->session->id,
            'start_date' => now(),
            'end_date' => now()->addMonth()
        ]);

        $course = CourseUnit::factory()->create();
        $exam = Exam::create([
            'exam_schedule_id' => $schedule->id,
            'course_unit_id' => $course->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.exams.updateSlice', $exam), [
                'exam_date' => now()->format('Y-m-d'),
                'start_time' => '10:00',
                'duration_minutes' => 90
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('exams', [
            'id' => $exam->id,
            'duration_minutes' => 90
        ]);
    }
}
