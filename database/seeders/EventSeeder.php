<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\EventRole;
use App\Models\Organization;
use App\Models\Volunteer;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Databases:
     * - izzati (PostgreSQL): Organizations, Events, EventRoles
     * - sashvini (MariaDB): Volunteers, EventParticipation
     */
    public function run(): void
    {
        // Get existing organizations from izzati
        $organizations = Organization::on('izzati')->get();

        if ($organizations->isEmpty()) {
            $this->command->warn('No organizations found. Please run UserRoleSeeder first.');

            return;
        }

        // Get existing volunteers from sashvini
        $volunteers = Volunteer::on('sashvini')->get();

        if ($volunteers->isEmpty()) {
            $this->command->warn('No volunteers found. Please run UserRoleSeeder first.');

            return;
        }

        $this->command->info("Found {$organizations->count()} organizations and {$volunteers->count()} volunteers");

        // Create events for each organization
        foreach ($organizations as $organization) {
            $this->createEventsForOrganization($organization, $volunteers);
        }

        $this->command->info('✓ Events and participations seeded successfully!');
    }

    private function createEventsForOrganization($organization, $volunteers)
    {
        $events = [
            // Upcoming Events
            [
                'Title' => 'Community Food Drive',
                'Description' => 'Join us in collecting and distributing food to families in need. We will be organizing donations and packing food parcels for distribution.',
                'Location' => $organization->City.', '.$organization->State,
                'Start_Date' => Carbon::now()->addDays(10),
                'End_Date' => Carbon::now()->addDays(10),
                'Capacity' => 20,
                'Status' => 'Upcoming',
            ],
            [
                'Title' => 'Beach Cleanup Campaign',
                'Description' => 'Help us keep our beaches clean! Bring your enthusiasm and we will provide all the necessary equipment.',
                'Location' => 'Port Dickson Beach, Negeri Sembilan',
                'Start_Date' => Carbon::now()->addDays(5),
                'End_Date' => Carbon::now()->addDays(5),
                'Capacity' => 30,
                'Status' => 'Upcoming',
            ],
            [
                'Title' => 'Free Health Screening',
                'Description' => 'Providing free health screening services to the community including blood pressure, diabetes, and BMI checks.',
                'Location' => $organization->City.' Community Center',
                'Start_Date' => Carbon::now()->addDays(15),
                'End_Date' => Carbon::now()->addDays(15),
                'Capacity' => 25,
                'Status' => 'Upcoming',
            ],
            [
                'Title' => 'Tree Planting Day',
                'Description' => 'Join our environmental initiative to plant 500 trees in the local park. Help us make our community greener!',
                'Location' => 'Taman Botani '.$organization->City,
                'Start_Date' => Carbon::now()->addDays(20),
                'End_Date' => Carbon::now()->addDays(20),
                'Capacity' => 40,
                'Status' => 'Upcoming',
            ],
            [
                'Title' => 'Senior Citizens Lunch Program',
                'Description' => 'Volunteers needed to help prepare and serve lunch to senior citizens in our community center.',
                'Location' => $organization->Address,
                'Start_Date' => Carbon::now()->addDays(8),
                'End_Date' => Carbon::now()->addDays(8),
                'Capacity' => 15,
                'Status' => 'Upcoming',
            ],

            // Ongoing Events
            [
                'Title' => 'Youth Mentoring Program',
                'Description' => 'Ongoing mentoring program where volunteers guide and support underprivileged youth in their education and career development.',
                'Location' => $organization->Address,
                'Start_Date' => Carbon::now()->subDays(7),
                'End_Date' => Carbon::now()->addDays(60),
                'Capacity' => 15,
                'Status' => 'Ongoing',
            ],
            [
                'Title' => 'Community Tuition Center',
                'Description' => 'Weekly tutoring sessions for underprivileged students. Volunteers teach Math, Science, and English to primary school children.',
                'Location' => $organization->City.' Learning Center',
                'Start_Date' => Carbon::now()->subDays(14),
                'End_Date' => Carbon::now()->addDays(45),
                'Capacity' => 20,
                'Status' => 'Ongoing',
            ],
            [
                'Title' => 'Mobile Food Distribution',
                'Description' => 'Weekly mobile food truck distributing groceries and cooked meals to low-income neighborhoods.',
                'Location' => 'Various locations in '.$organization->State,
                'Start_Date' => Carbon::now()->subDays(21),
                'End_Date' => Carbon::now()->addDays(30),
                'Capacity' => 12,
                'Status' => 'Ongoing',
            ],

            // Completed Events
            [
                'Title' => 'Charity Run 2024',
                'Description' => 'Annual charity run event that was successfully completed last month. Raised significant funds for our cause.',
                'Location' => 'KLCC Park, Kuala Lumpur',
                'Start_Date' => Carbon::now()->subDays(30),
                'End_Date' => Carbon::now()->subDays(30),
                'Capacity' => 100,
                'Status' => 'Completed',
            ],
            [
                'Title' => 'Flood Relief Distribution',
                'Description' => 'Emergency relief distribution to families affected by recent floods. Distributed food, clothing, and emergency supplies.',
                'Location' => 'Pahang Flood Relief Center',
                'Start_Date' => Carbon::now()->subDays(45),
                'End_Date' => Carbon::now()->subDays(42),
                'Capacity' => 30,
                'Status' => 'Completed',
            ],
            [
                'Title' => 'Back to School Program',
                'Description' => 'Provided school supplies and new uniforms to 150 underprivileged students.',
                'Location' => $organization->Address,
                'Start_Date' => Carbon::now()->subDays(60),
                'End_Date' => Carbon::now()->subDays(60),
                'Capacity' => 25,
                'Status' => 'Completed',
            ],
            [
                'Title' => 'Ramadan Food Basket Distribution',
                'Description' => 'Distributed food baskets to 200 families during Ramadan. Each basket contained essential groceries for the month.',
                'Location' => 'Multiple distribution points',
                'Start_Date' => Carbon::now()->subDays(90),
                'End_Date' => Carbon::now()->subDays(85),
                'Capacity' => 35,
                'Status' => 'Completed',
            ],

            // Pending Events (Awaiting Approval)
            [
                'Title' => 'Skills Training Workshop',
                'Description' => 'Vocational training workshop teaching computer literacy, resume writing, and interview skills to job seekers.',
                'Location' => $organization->City.' Skills Center',
                'Start_Date' => Carbon::now()->addDays(25),
                'End_Date' => Carbon::now()->addDays(27),
                'Capacity' => 30,
                'Status' => 'Pending',
            ],
            [
                'Title' => 'Orphanage Visit & Activity Day',
                'Description' => 'Planned visit to local orphanage with games, activities, and gift distribution for children.',
                'Location' => 'Rumah Anak Yatim Al-Falah',
                'Start_Date' => Carbon::now()->addDays(18),
                'End_Date' => Carbon::now()->addDays(18),
                'Capacity' => 20,
                'Status' => 'Pending',
            ],
            [
                'Title' => 'Medical Camp for Rural Community',
                'Description' => 'Free medical checkup, consultation, and basic medication for residents in rural areas.',
                'Location' => 'Kampung Sungai Rusa, '.$organization->State,
                'Start_Date' => Carbon::now()->addDays(35),
                'End_Date' => Carbon::now()->addDays(35),
                'Capacity' => 25,
                'Status' => 'Pending',
            ],
            [
                'Title' => 'Youth Sports Tournament',
                'Description' => 'Organizing futsal and badminton tournament for underprivileged youth to promote healthy lifestyle and teamwork.',
                'Location' => 'Kompleks Sukan '.$organization->City,
                'Start_Date' => Carbon::now()->addDays(40),
                'End_Date' => Carbon::now()->addDays(42),
                'Capacity' => 50,
                'Status' => 'Pending',
            ],
        ];

        foreach ($events as $eventData) {
            // Random created_at within the last week
            $createdAt = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            // Create event in izzati database
            $event = new Event([
                'Organizer_ID' => $organization->Organization_ID,
                'Title' => $eventData['Title'],
                'Description' => $eventData['Description'],
                'Location' => $eventData['Location'],
                'Start_Date' => $eventData['Start_Date'],
                'End_Date' => $eventData['End_Date'],
                'Capacity' => $eventData['Capacity'],
                'Status' => $eventData['Status'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);
            $event->setConnection('izzati');
            $event->save();

            // Create volunteer roles for this event (izzati)
            $this->createRolesForEvent($event);

            // Attach volunteers to events with different statuses
            // Don't attach volunteers to Pending events
            if ($event->Status !== 'Pending') {
                $this->attachVolunteersToEvent($event, $volunteers);
            }
        }
    }

    private function createRolesForEvent($event)
    {
        // Define role templates based on event type
        $roleTemplates = [
            'food' => [
                ['name' => 'Food Distributor', 'description' => 'Distribute food items to recipients, manage queue', 'needed' => rand(5, 10)],
                ['name' => 'Registration Desk', 'description' => 'Register participants and verify eligibility', 'needed' => rand(2, 4)],
                ['name' => 'Setup Crew', 'description' => 'Setup tables, tents, and distribution stations', 'needed' => rand(3, 6)],
                ['name' => 'Logistics Support', 'description' => 'Transport and organize food supplies', 'needed' => rand(2, 5)],
            ],
            'cleanup' => [
                ['name' => 'Cleanup Crew', 'description' => 'Collect trash and recyclables from designated areas', 'needed' => rand(10, 15)],
                ['name' => 'Team Leader', 'description' => 'Lead and coordinate cleanup teams', 'needed' => rand(2, 3)],
                ['name' => 'Equipment Manager', 'description' => 'Manage cleaning tools and garbage bags', 'needed' => rand(2, 4)],
            ],
            'health' => [
                ['name' => 'Registration Staff', 'description' => 'Register participants and maintain records', 'needed' => rand(3, 5)],
                ['name' => 'Screening Assistant', 'description' => 'Assist medical staff with health screenings', 'needed' => rand(5, 8)],
                ['name' => 'Queue Management', 'description' => 'Manage patient flow and waiting areas', 'needed' => rand(2, 4)],
                ['name' => 'Data Entry', 'description' => 'Record screening results and patient information', 'needed' => rand(2, 3)],
            ],
            'education' => [
                ['name' => 'Tutor', 'description' => 'Teach students in assigned subjects', 'needed' => rand(8, 12)],
                ['name' => 'Activity Coordinator', 'description' => 'Organize educational games and activities', 'needed' => rand(3, 5)],
                ['name' => 'Admin Support', 'description' => 'Handle attendance and student records', 'needed' => rand(2, 3)],
            ],
            'environment' => [
                ['name' => 'Planting Crew', 'description' => 'Dig holes and plant saplings', 'needed' => rand(15, 25)],
                ['name' => 'Water Carrier', 'description' => 'Distribute water to planted trees', 'needed' => rand(5, 10)],
                ['name' => 'Site Supervisor', 'description' => 'Oversee planting operations and quality', 'needed' => rand(2, 4)],
            ],
            'general' => [
                ['name' => 'General Volunteer', 'description' => 'Assist with various tasks as needed', 'needed' => rand(5, 10)],
                ['name' => 'Coordinator', 'description' => 'Coordinate activities and manage volunteers', 'needed' => rand(2, 3)],
                ['name' => 'Support Staff', 'description' => 'Provide general support and assistance', 'needed' => rand(3, 5)],
            ],
        ];

        // Determine event type based on title
        $title = strtolower($event->Title);
        $eventType = 'general';
        if (str_contains($title, 'food') || str_contains($title, 'lunch') || str_contains($title, 'distribution')) {
            $eventType = 'food';
        } elseif (str_contains($title, 'cleanup') || str_contains($title, 'beach')) {
            $eventType = 'cleanup';
        } elseif (str_contains($title, 'health') || str_contains($title, 'medical') || str_contains($title, 'screening')) {
            $eventType = 'health';
        } elseif (str_contains($title, 'tuition') || str_contains($title, 'mentoring') || str_contains($title, 'education') || str_contains($title, 'school')) {
            $eventType = 'education';
        } elseif (str_contains($title, 'tree') || str_contains($title, 'environment') || str_contains($title, 'planting')) {
            $eventType = 'environment';
        }

        $roles = $roleTemplates[$eventType];

        foreach ($roles as $roleData) {
            // Create event role in izzati database
            $eventRole = new EventRole([
                'Event_ID' => $event->Event_ID,
                'Role_Name' => $roleData['name'],
                'Role_Description' => $roleData['description'],
                'Volunteers_Needed' => $roleData['needed'],
                'Volunteers_Filled' => 0,
            ]);
            $eventRole->setConnection('izzati');
            $eventRole->save();
        }
    }

    private function attachVolunteersToEvent($event, $volunteers)
    {
        // Get roles for this event from izzati
        $eventRoles = EventRole::on('izzati')->where('Event_ID', $event->Event_ID)->get();

        if ($eventRoles->isEmpty()) {
            return;
        }

        // Determine how many volunteers should participate based on event status
        $participationCount = match ($event->Status) {
            'Completed' => rand(3, min(8, $volunteers->count())),
            'Ongoing' => rand(2, min(6, $volunteers->count())),
            'Upcoming' => rand(1, min(5, $volunteers->count())),
            default => 0
        };

        $selectedVolunteers = $volunteers->random(min($participationCount, $volunteers->count()));

        foreach ($selectedVolunteers as $volunteer) {
            // Assign volunteer to a random non-full role
            $availableRoles = $eventRoles->filter(function ($role) {
                return $role->Volunteers_Filled < $role->Volunteers_Needed;
            });

            if ($availableRoles->isEmpty()) {
                continue; // Skip if all roles are full
            }

            $assignedRole = $availableRoles->random();

            // Valid participation statuses: Registered, Attended, No-Show, Cancelled
            $participationStatus = match ($event->Status) {
                'Completed' => (rand(1, 100) <= 75) ? 'Attended' : 'No-Show', // 75% attended, 25% no-show
                'Ongoing' => 'Registered',
                'Upcoming' => 'Registered',
                default => 'Registered'
            };

            $totalHours = match ($participationStatus) {
                'Attended' => rand(3, 8),
                default => 0
            };

            // Create event participation in sashvini database
            $participation = new EventParticipation([
                'Volunteer_ID' => $volunteer->Volunteer_ID,
                'Event_ID' => $event->Event_ID,
                'Role_ID' => $assignedRole->Role_ID,
                'Status' => $participationStatus,
                'Total_Hours' => $totalHours,
            ]);
            $participation->setConnection('sashvini');
            $participation->save();

            // Increment role filled count (in izzati)
            $assignedRole->increment('Volunteers_Filled');
        }
    }
}
