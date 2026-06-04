<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Title;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FacultyUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Skip if we already have faculty users
        if (User::where('email', 'like', '%@ouk.ac.ke')->exists()) {
            $this->command->info('Faculty users already exist. Skipping...');
            return;
        }

        // Get the Doctor title
        $drTitle = Title::where('abbreviation', 'Dr.')->first();
        
        if (!$drTitle) {
            $this->command->error('Doctor title not found. Please run the TitleSeeder first.');
            return;
        }

        $facultyMembers = [
            ['email' => 'aoloo@ouk.ac.ke', 'name' => 'Abisaki OLOO'],
            ['email' => 'aali@ouk.ac.ke', 'name' => 'Ali ABDALLAH'],
            ['email' => 'anjagi@ouk.ac.ke', 'name' => 'Anne NJAGI'],
            ['email' => 'bobiero@ouk.ac.ke', 'name' => 'Ben OBIERO'],
            ['email' => 'bkiratu@ouk.ac.ke', 'name' => 'Beth KIRATU'],
            ['email' => 'cckiptoo@ouk.ac.ke', 'name' => 'Caroline KIPTOO'],
            ['email' => 'cndiritu@ouk.ac.ke', 'name' => 'Caroline NDIRITU'],
            ['email' => 'coloo@ouk.ac.ke', 'name' => 'Caroline OLOO'],
            ['email' => 'cchakua@ouk.ac.ke', 'name' => 'Carolyne CHAKUA'],
            ['email' => 'ccherotich@ouk.ac.ke', 'name' => 'Carolyne CHEROTICH'],
            ['email' => 'ckatila@ouk.ac.ke', 'name' => 'Charles KATILA'],
        ];

        $now = now();
        $password = Hash::make('password'); // Default password
        
        foreach ($facultyMembers as $faculty) {
            // Extract first name for username
            $firstName = strtolower(explode(' ', $faculty['name'])[0]);
            
            User::updateOrCreate(
                ['email' => $faculty['email']],
                [
                    'name' => $faculty['name'],
                    'email' => $faculty['email'],
                    'title_id' => $drTitle->id,
                    'password' => $password,
                    'email_verified_at' => $now,
                    'status' => 'active',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }

        // Assign instructor role to these users
        $instructorRole = \App\Models\Role::where('name', 'instructor')->first();
        
        if ($instructorRole) {
            $facultyUsers = User::whereIn('email', array_column($facultyMembers, 'email'))->get();
            foreach ($facultyUsers as $user) {
                // Attach the role using the relationship
                if (!$user->roles->contains($instructorRole->id)) {
                    $user->roles()->attach($instructorRole->id);
                }
            }
        } else {
            $this->command->warn('Instructor role not found. Please run the RoleSeeder first.');
        }

        $this->command->info('Seeded ' . count($facultyMembers) . ' faculty users.');
    }
}
