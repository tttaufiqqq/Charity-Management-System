<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Donor;
use App\Models\PublicProfile;
use App\Models\Organization;
use App\Models\Volunteer;
use App\Models\Skill;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $donorRole = Role::firstOrCreate(['name' => 'donor']);
        $volunteerRole = Role::firstOrCreate(['name' => 'volunteer']);
        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);
        $publicRole = Role::firstOrCreate(['name' => 'public']);

        // Create Donor accounts
        $this->createDonors($donorRole);

        // Create Volunteer accounts
        $this->createVolunteers($volunteerRole);

        // Create Organizer accounts
        $this->createOrganizers($organizerRole);

        // Create Public accounts
        $this->createPublicProfiles($publicRole);
    }

    private function getRandomCreatedAt()
    {
        return Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
    }

    private function createDonors($role)
    {
        $donors = [
            [
                'name' => 'Izz',
                'email' => 'izz@example.com',
                'Full_Name' => 'Izz Rahman',
                'Phone_Num' => '+60123456789',
                'Total_Donated' => 0.00
            ],
            [
                'name' => 'Sashvini',
                'email' => 'sashvini@example.com',
                'Full_Name' => 'Sashvini Devi',
                'Phone_Num' => '+60123456790',
                'Total_Donated' => 0.00
            ],
            [
                'name' => 'Hannah',
                'email' => 'hannah@example.com',
                'Full_Name' => 'Hannah Lee',
                'Phone_Num' => '+60123456791',
                'Total_Donated' => 0.00
            ],
            [
                'name' => 'Adam',
                'email' => 'adam@example.com',
                'Full_Name' => 'Adam Tan',
                'Phone_Num' => '+60123456792',
                'Total_Donated' => 0.00
            ],
        ];

        foreach ($donors as $donorData) {
            $createdAt = $this->getRandomCreatedAt();

            $user = User::create([
                'name' => $donorData['name'],
                'email' => $donorData['email'],
                'password' => Hash::make('password'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $user->assignRole($role);

            Donor::create([
                'User_ID' => $user->id,
                'Full_Name' => $donorData['Full_Name'],
                'Phone_Num' => $donorData['Phone_Num'],
                'Total_Donated' => $donorData['Total_Donated'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Created 4 donor accounts');
    }

    private function createVolunteers($role)
    {
        $volunteers = [
            [
                'name' => 'Izzati',
                'email' => 'izzati@example.com',
                'Availability' => 'Weekends',
                'Address' => '123 Volunteer Street',
                'City' => 'Kuala Lumpur',
                'State' => 'Wilayah Persekutuan',
                'Gender' => 'Female',
                'Phone_Num' => '+60123456793',
                'Description' => 'Passionate about community service and helping others.'
            ],
            [
                'name' => 'Hannah',
                'email' => 'hannah.volunteer@example.com',
                'Availability' => 'Weekdays after 5pm',
                'Address' => '456 Helper Avenue',
                'City' => 'Seremban',
                'State' => 'Negeri Sembilan',
                'Gender' => 'Female',
                'Phone_Num' => '+60123456794',
                'Description' => 'Experienced volunteer with skills in education and mentoring.'
            ],
            [
                'name' => 'Adam',
                'email' => 'adam.volunteer@example.com',
                'Availability' => 'Flexible',
                'Address' => '789 Community Road',
                'City' => 'Petaling Jaya',
                'State' => 'Selangor',
                'Gender' => 'Male',
                'Phone_Num' => '+60123456795',
                'Description' => 'Enthusiastic about making a positive impact in the community.'
            ],
            [
                'name' => 'Izz',
                'email' => 'izz.volunteer@example.com',
                'Availability' => 'Weekends and Public Holidays',
                'Address' => '321 Helping Hand Lane',
                'City' => 'Shah Alam',
                'State' => 'Selangor',
                'Gender' => 'Male',
                'Phone_Num' => '+60123456796',
                'Description' => 'Dedicated volunteer with experience in event coordination.'
            ],
        ];

        // Create some skills first
        $skills = [
            Skill::firstOrCreate(['Skill_Name' => 'Teaching']),
            Skill::firstOrCreate(['Skill_Name' => 'First Aid']),
            Skill::firstOrCreate(['Skill_Name' => 'Event Planning']),
            Skill::firstOrCreate(['Skill_Name' => 'Cooking']),
            Skill::firstOrCreate(['Skill_Name' => 'Computer Literacy']),
            Skill::firstOrCreate(['Skill_Name' => 'Public Speaking']),
        ];

        foreach ($volunteers as $index => $volunteerData) {
            $createdAt = $this->getRandomCreatedAt();

            $user = User::create([
                'name' => $volunteerData['name'],
                'email' => $volunteerData['email'],
                'password' => Hash::make('password'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $user->assignRole($role);

            $volunteer = Volunteer::create([
                'User_ID' => $user->id,
                'Availability' => $volunteerData['Availability'],
                'Address' => $volunteerData['Address'],
                'City' => $volunteerData['City'],
                'State' => $volunteerData['State'],
                'Gender' => $volunteerData['Gender'],
                'Phone_Num' => $volunteerData['Phone_Num'],
                'Description' => $volunteerData['Description'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Attach random skills (2-3 skills per volunteer)
            $skillCount = rand(2, 3);
            $selectedSkills = collect($skills)->random($skillCount);

            foreach ($selectedSkills as $skill) {
                $skillLevels = ['Beginner', 'Intermediate', 'Advanced'];
                $volunteer->skills()->attach($skill->Skill_ID, [
                    'Skill_Level' => $skillLevels[array_rand($skillLevels)],
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('Created 4 volunteer accounts');
    }

    private function createOrganizers($role)
    {
        $organizers = [
            [
                'name' => 'Hope Foundation',
                'email' => 'admin@hopefoundation.org',
                'Organization_Name' => 'Hope Foundation Malaysia',
                'Phone_No' => '+60123456797',
                'Register_No' => 'REG-2020-001',
                'Address' => '789 Charity Lane',
                'State' => 'Selangor',
                'City' => 'Petaling Jaya',
                'Description' => 'A non-profit organization dedicated to helping underprivileged communities.'
            ],
            [
                'name' => 'Care Malaysia',
                'email' => 'info@caremalaysia.org',
                'Organization_Name' => 'Care Malaysia Foundation',
                'Phone_No' => '+60123456798',
                'Register_No' => 'REG-2019-045',
                'Address' => '321 NGO Boulevard',
                'State' => 'Penang',
                'City' => 'George Town',
                'Description' => 'Providing education and healthcare support to those in need.'
            ],
            [
                'name' => 'Community Hearts',
                'email' => 'contact@communityhearts.org',
                'Organization_Name' => 'Community Hearts Foundation',
                'Phone_No' => '+60123456799',
                'Register_No' => 'REG-2021-088',
                'Address' => '456 Kindness Avenue',
                'State' => 'Johor',
                'City' => 'Johor Bahru',
                'Description' => 'Empowering communities through sustainable development programs.'
            ],
        ];

        foreach ($organizers as $organizerData) {
            $createdAt = $this->getRandomCreatedAt();

            $user = User::create([
                'name' => $organizerData['name'],
                'email' => $organizerData['email'],
                'password' => Hash::make('password'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $user->assignRole($role);

            Organization::create([
                'Organizer_ID' => $user->id,
                'Phone_No' => $organizerData['Phone_No'],
                'Register_No' => $organizerData['Register_No'],
                'Address' => $organizerData['Address'],
                'State' => $organizerData['State'],
                'City' => $organizerData['City'],
                'Description' => $organizerData['Description'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Created 3 organizer accounts');
    }

    private function createPublicProfiles($role)
    {
        $publicProfiles = [
            [
                'name' => 'Sashvini',
                'email' => 'sashvini.public@example.com',
                'Full_Name' => 'Sashvini Kumar',
                'Phone' => '+60123456800',
                'Position' => 'Community Member'
            ],
            [
                'name' => 'Adam',
                'email' => 'adam.public@example.com',
                'Full_Name' => 'Adam Wong',
                'Phone' => '+60123456801',
                'Position' => 'Local Resident'
            ],
            [
                'name' => 'Izzati',
                'email' => 'izzati.public@example.com',
                'Full_Name' => 'Izzati Zainal',
                'Phone' => '+60123456802',
                'Position' => 'Community Volunteer'
            ],
        ];

        foreach ($publicProfiles as $publicData) {
            $createdAt = $this->getRandomCreatedAt();

            $user = User::create([
                'name' => $publicData['name'],
                'email' => $publicData['email'],
                'password' => Hash::make('password'),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $user->assignRole($role);

            PublicProfile::create([
                'User_ID' => $user->id,
                'Full_Name' => $publicData['Full_Name'],
                'Phone' => $publicData['Phone'],
                'Email' => $publicData['email'],
                'Position' => $publicData['Position'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
        }

        $this->command->info('Created 3 public accounts');
    }
}
