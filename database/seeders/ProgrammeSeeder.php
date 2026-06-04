<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\Programme;
use App\Models\School;
use Illuminate\Database\Seeder;

class ProgrammeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create schools
        $schools = [
            'Science and Technology' => School::where('name', 'Science and Technology')->first(),
            'Business and Economics' => School::where('name', 'Business and Economics')->first(),
            'Education' => School::where('name', 'Education')->first(),
        ];

        // Get the current academic session
        $academicSession = AcademicSession::where('is_current', true)->first();
        
        if (!$academicSession) {
            $this->command->error('No active academic session found. Please run AcademicSessionSeeder first.');
            return;
        }

        // Define programmes with their respective schools
        $programmes = [
            // Science and Technology programmes
            [
                'name' => 'Bachelor of Data Science',
                'programme_code' => 'ST01',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Bachelor of Science in Cyber Security and Digital Forensics',
                'programme_code' => 'ST02',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Bachelor of Science in Computer Science',
                'programme_code' => 'ST04',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Bachelor of Science in Mathematics and Computing',
                'programme_code' => 'ST03',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Master of Science in Artificial Intelligence',
                'programme_code' => 'ST62',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Master of Science in Cybersecurity and Digital Forensics',
                'programme_code' => 'ST63',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Master of Data Science',
                'programme_code' => 'ST61',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Master of Science in Digital Services Management',
                'programme_code' => 'ST64',
                'school_id' => $schools['Science and Technology']->id,
            ],
            [
                'name' => 'Bachelor of Agritechnology and Food Systems',
                'programme_code' => 'AG01',
                'school_id' => $schools['Science and Technology']->id,
            ],
            
            // Business and Economics programmes
            [
                'name' => 'Bachelor of Economics and Statistics',
                'programme_code' => 'BE01',
                'school_id' => $schools['Business and Economics']->id,
            ],
            [
                'name' => 'Bachelor of Business and Entrepreneurship',
                'programme_code' => 'BE02',
                'school_id' => $schools['Business and Economics']->id,
            ],
            [
                'name' => 'Bachelor of Economics and Data Science',
                'programme_code' => 'BE03',
                'school_id' => $schools['Business and Economics']->id,
            ],
            [
                'name' => 'Bachelor of Commerce',
                'programme_code' => 'BE04',
                'school_id' => $schools['Business and Economics']->id,
            ],
            [
                'name' => 'Master of Business Administration',
                'programme_code' => 'BE61',
                'school_id' => $schools['Business and Economics']->id,
            ],
            [
                'name' => 'Doctor of Philosophy in Business Management',
                'programme_code' => 'BE81',
                'school_id' => $schools['Business and Economics']->id,
            ],
            [
                'name' => 'Postgraduate Diploma in Leadership and Accountability',
                'programme_code' => 'PLA',
                'school_id' => $schools['Business and Economics']->id,
            ],
            
            // Education programmes
            [
                'name' => 'Bachelor of Technology Education (BCT)',
                'programme_code' => 'ED01 (BCT)',
                'school_id' => $schools['Education']->id,
            ],
            [
                'name' => 'Bachelor of Technology Education (CIT)',
                'programme_code' => 'ED01 (CIT)',
                'school_id' => $schools['Education']->id,
            ],
            [
                'name' => 'Bachelor of Technology Education (EET)',
                'programme_code' => 'ED01 (EET)',
                'school_id' => $schools['Education']->id,
            ],
            [
                'name' => 'Bachelor of Technology Education (MTT)',
                'programme_code' => 'ED01 (MTT)',
                'school_id' => $schools['Education']->id,
            ],
            [
                'name' => 'Bachelor of Technology Education (PMT)',
                'programme_code' => 'ED01 (PMT)',
                'school_id' => $schools['Education']->id,
            ],
            [
                'name' => 'Postgraduate Diploma in Education',
                'programme_code' => 'PDE',
                'school_id' => $schools['Education']->id,
            ],
            [
                'name' => 'Postgraduate Diploma in Learning Design and Technology',
                'programme_code' => 'LDT',
                'school_id' => $schools['Education']->id,
            ],
            [
                'name' => 'Master in Learning Design and Technology',
                'programme_code' => 'ED61',
                'school_id' => $schools['Education']->id,
            ],
        ];

        // Create or update programmes
        foreach ($programmes as $programme) {
            $createdProgramme = Programme::updateOrCreate(
                ['programme_code' => $programme['programme_code']],
                $programme
            );
        }
    }
}
