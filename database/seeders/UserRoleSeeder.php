<?php

namespace Database\Seeders;

use App\Models\Donor;
use App\Models\Organization;
use App\Models\PublicProfile;
use App\Models\Skill;
use App\Models\User;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles if they don't exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $donorRole = Role::firstOrCreate(['name' => 'donor']);
        $volunteerRole = Role::firstOrCreate(['name' => 'volunteer']);
        $organizerRole = Role::firstOrCreate(['name' => 'organizer']);
        $publicRole = Role::firstOrCreate(['name' => 'public']);

        // Create Admin account
        $this->createAdmin($adminRole);

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

    private function createAdmin($role)
    {
        // Admin user is already created by RoleSeeder
        // Just verify it exists and has the correct role
        $user = User::where('email', 'admin@gmail.com')->first();

        if ($user && ! $user->hasRole('admin')) {
            $user->assignRole($role);
        }

        $this->command->info('Admin account verified (admin@gmail.com / password)');
    }

    private function createDonors($role)
    {
        $donors = [
            [
                'name' => 'Ahmad',
                'email' => 'ahmad.donor@gmail.com',
                'Full_Name' => 'Ahmad bin Ibrahim',
                'Phone_Num' => '+60123456789',
                'Total_Donated' => 0.00,
            ],
            [
                'name' => 'Siti',
                'email' => 'siti.donor@gmail.com',
                'Full_Name' => 'Siti Nurhaliza binti Hassan',
                'Phone_Num' => '+60138765432',
                'Total_Donated' => 0.00,
            ],
            [
                'name' => 'Kumar',
                'email' => 'kumar.donor@gmail.com',
                'Full_Name' => 'Kumar s/o Rajan',
                'Phone_Num' => '+60147654321',
                'Total_Donated' => 0.00,
            ],
            [
                'name' => 'Michelle',
                'email' => 'michelle.donor@gmail.com',
                'Full_Name' => 'Michelle Tan Mei Ling',
                'Phone_Num' => '+60156543210',
                'Total_Donated' => 0.00,
            ],
            [
                'name' => 'Azman',
                'email' => 'azman.donor@gmail.com',
                'Full_Name' => 'Azman bin Othman',
                'Phone_Num' => '+60165432109',
                'Total_Donated' => 0.00,
            ],
            [
                'name' => 'Priya',
                'email' => 'priya.donor@gmail.com',
                'Full_Name' => 'Priya Devi a/p Subramaniam',
                'Phone_Num' => '+60174321098',
                'Total_Donated' => 0.00,
            ],
            [
                'name' => 'David',
                'email' => 'david.donor@gmail.com',
                'Full_Name' => 'David Wong Wei Liang',
                'Phone_Num' => '+60183210987',
                'Total_Donated' => 0.00,
            ],
            [
                'name' => 'Nora',
                'email' => 'nora.donor@gmail.com',
                'Full_Name' => 'Nora binti Abdullah',
                'Phone_Num' => '+60192109876',
                'Total_Donated' => 0.00,
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

        $this->command->info('Created 8 donor accounts with realistic Malaysian names');
    }

    private function createVolunteers($role)
    {
        $volunteers = [
            [
                'name' => 'Izzati',
                'email' => 'izzati.volunteer@gmail.com',
                'Availability' => 'Weekends',
                'Address' => 'No. 45, Jalan Bunga Raya 3/2, Taman Sentosa',
                'City' => 'Kuala Lumpur',
                'State' => 'Wilayah Persekutuan',
                'Gender' => 'Female',
                'Phone_Num' => '+60123456793',
                'Description' => 'Passionate about community service and helping underprivileged children. 2 years volunteering experience with NGOs.',
            ],
            [
                'name' => 'Wei Jie',
                'email' => 'weijie.volunteer@gmail.com',
                'Availability' => 'Weekdays after 5pm',
                'Address' => 'No. 78, Lorong Seri Harmoni, Taman Maju',
                'City' => 'Seremban',
                'State' => 'Negeri Sembilan',
                'Gender' => 'Male',
                'Phone_Num' => '+60134567890',
                'Description' => 'University student majoring in Education. Love teaching and mentoring youth.',
            ],
            [
                'name' => 'Rajesh',
                'email' => 'rajesh.volunteer@gmail.com',
                'Availability' => 'Flexible',
                'Address' => 'No. 123, Jalan Damai 5/6, Bandar Sri Damansara',
                'City' => 'Petaling Jaya',
                'State' => 'Selangor',
                'Gender' => 'Male',
                'Phone_Num' => '+60145678901',
                'Description' => 'IT professional with first aid certification. Interested in community health programs.',
            ],
            [
                'name' => 'Amira',
                'email' => 'amira.volunteer@gmail.com',
                'Availability' => 'Weekends and Public Holidays',
                'Address' => 'No. 56, Taman Melati Indah',
                'City' => 'Shah Alam',
                'State' => 'Selangor',
                'Gender' => 'Female',
                'Phone_Num' => '+60156789012',
                'Description' => 'Event coordinator with 3 years experience organizing charity events and food drives.',
            ],
            [
                'name' => 'Chong',
                'email' => 'chong.volunteer@gmail.com',
                'Availability' => 'Weekends',
                'Address' => 'No. 89, Jalan Kasturi 12, Taman Megah',
                'City' => 'Ipoh',
                'State' => 'Perak',
                'Gender' => 'Male',
                'Phone_Num' => '+60167890123',
                'Description' => 'Retired teacher passionate about literacy programs and elderly care.',
            ],
            [
                'name' => 'Farah',
                'email' => 'farah.volunteer@gmail.com',
                'Availability' => 'Weekdays evening',
                'Address' => 'No. 34, Lorong Mawar 2, Kampung Baru',
                'City' => 'Johor Bahru',
                'State' => 'Johor',
                'Gender' => 'Female',
                'Phone_Num' => '+60178901234',
                'Description' => 'Healthcare worker interested in medical outreach and health screening programs.',
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

        $this->command->info('Created 6 volunteer accounts with diverse Malaysian backgrounds');
    }

    private function createOrganizers($role)
    {
        $organizers = [
            [
                'name' => 'Yayasan Kebajikan Rakyat Malaysia',
                'email' => 'admin@ykr.org.my',
                'Phone_No' => '+603-7956-4321',
                'Register_No' => 'PPM-002-10-11012020',
                'Address' => 'No. 45-47, Jalan Sultan Ismail, Bukit Bintang',
                'State' => 'Wilayah Persekutuan',
                'City' => 'Kuala Lumpur',
                'Description' => 'Established in 2020, YKR is a registered welfare foundation dedicated to alleviating poverty and improving quality of life for underprivileged communities across Malaysia. We focus on education support, healthcare assistance, and emergency relief programs.',
            ],
            [
                'name' => 'Pertubuhan Amal Sejahtera Negeri Selangor',
                'email' => 'info@amalsejahtera.org.my',
                'Phone_No' => '+603-5544-7890',
                'Register_No' => 'PPM-003-14-25072019',
                'Address' => 'No. 23, Jalan SS 2/24, Petaling Jaya',
                'State' => 'Selangor',
                'City' => 'Petaling Jaya',
                'Description' => 'A community-driven charitable organization providing comprehensive support to families in need. Our programs include food assistance, skills training, micro-financing for small businesses, and youth development initiatives. Serving Selangor communities since 2019.',
            ],
            [
                'name' => 'Malaysian Hearts Charitable Foundation',
                'email' => 'contact@malaysianhearts.org',
                'Phone_No' => '+604-229-6543',
                'Register_No' => 'PPM-007-10-14032018',
                'Address' => 'No. 156, Jalan Masjid Kapitan Keling, George Town',
                'State' => 'Pulau Pinang',
                'City' => 'George Town',
                'Description' => 'Focusing on sustainable community development through education, healthcare, and environmental programs. We operate scholarship programs, mobile health clinics, and clean water initiatives across Penang and Northern Malaysia.',
            ],
            [
                'name' => 'Persatuan Kebajikan Masyarakat Johor',
                'email' => 'info@pkm-johor.org.my',
                'Phone_No' => '+607-223-8901',
                'Register_No' => 'PPM-001-01-03112017',
                'Address' => 'No. 78, Jalan Trus, Bandar Johor Bahru',
                'State' => 'Johor',
                'City' => 'Johor Bahru',
                'Description' => 'Southern Malaysia\'s leading welfare association committed to empowering marginalized communities. We run orphanages, elderly care centers, and vocational training programs. Recognized by the state government for outstanding community service.',
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

        $this->command->info('Created 4 organizer accounts with authentic Malaysian charitable organizations');
    }

    private function createPublicProfiles($role)
    {
        $publicProfiles = [
            [
                'name' => 'Hassan',
                'email' => 'hassan.public@gmail.com',
                'Full_Name' => 'Hassan bin Mahmud',
                'Phone' => '+60123456800',
                'Position' => 'Social Worker',
            ],
            [
                'name' => 'Lina',
                'email' => 'lina.public@gmail.com',
                'Full_Name' => 'Lina Wong Siew Mei',
                'Phone' => '+60134567891',
                'Position' => 'Community Leader',
            ],
            [
                'name' => 'Suresh',
                'email' => 'suresh.public@gmail.com',
                'Full_Name' => 'Suresh a/l Muthu',
                'Phone' => '+60145678902',
                'Position' => 'Local Resident',
            ],
            [
                'name' => 'Aishah',
                'email' => 'aishah.public@gmail.com',
                'Full_Name' => 'Aishah binti Razak',
                'Phone' => '+60156789013',
                'Position' => 'Welfare Officer',
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

        $this->command->info('Created 4 public accounts (for recipient applications)');
    }
}
