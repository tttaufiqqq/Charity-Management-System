<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\Organization;
use App\Models\Volunteer;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing organizations
        $organizations = Organization::all();

        if ($organizations->isEmpty()) {
            $this->command->warn('No organizations found. Please run UserRoleSeeder first.');
            return;
        }

        // Get existing volunteers
        $volunteers = Volunteer::all();

        if ($volunteers->isEmpty()) {
            $this->command->warn('No volunteers found. Please run UserRoleSeeder first.');
            return;
        }

        // Create events for each organization
        foreach ($organizations as $organization) {
            $this->createEventsForOrganization($organization, $volunteers);
        }

        $this->command->info('Events and participations seeded successfully!');
    }

    private function createEventsForOrganization($organization, $volunteers)
    {
        $events = [
            // Upcoming Events
            [
                'Title' => 'Community Food Drive',
                'Description' => 'Join us in collecting and distributing food to families in need. We will be organizing donations and packing food parcels for distribution.',
                'Location' => $organization->City . ', ' . $organization->State,
                'Start_Date' => Carbon::now()->addDays(10),
                'End_Date' => Carbon::now()->addDays(10),
                'Capacity' => 20,
                'Status' => 'Upcoming'
            ],
            [
                'Title' => 'Beach Cleanup Campaign',
                'Description' => 'Help us keep our beaches clean! Bring your enthusiasm and we will provide all the necessary equipment.',
                'Location' => 'Port Dickson Beach, Negeri Sembilan',
                'Start_Date' => Carbon::now()->addDays(5),
                'End_Date' => Carbon::now()->addDays(5),
                'Capacity' => 30,
                'Status' => 'Upcoming'
            ],
            [
                'Title' => 'Free Health Screening',
                'Description' => 'Providing free health screening services to the community including blood pressure, diabetes, and BMI checks.',
                'Location' => $organization->City . ' Community Center',
                'Start_Date' => Carbon::now()->addDays(15),
                'End_Date' => Carbon::now()->addDays(15),
                'Capacity' => 25,
                'Status' => 'Upcoming'
            ],
            [
                'Title' => 'Tree Planting Day',
                'Description' => 'Join our environmental initiative to plant 500 trees in the local park. Help us make our community greener!',
                'Location' => 'Taman Botani ' . $organization->City,
                'Start_Date' => Carbon::now()->addDays(20),
                'End_Date' => Carbon::now()->addDays(20),
                'Capacity' => 40,
                'Status' => 'Upcoming'
            ],
            [
                'Title' => 'Senior Citizens Lunch Program',
                'Description' => 'Volunteers needed to help prepare and serve lunch to senior citizens in our community center.',
                'Location' => $organization->Address,
                'Start_Date' => Carbon::now()->addDays(8),
                'End_Date' => Carbon::now()->addDays(8),
                'Capacity' => 15,
                'Status' => 'Upcoming'
            ],

            // Ongoing Events
            [
                'Title' => 'Youth Mentoring Program',
                'Description' => 'Ongoing mentoring program where volunteers guide and support underprivileged youth in their education and career development.',
                'Location' => $organization->Address,
                'Start_Date' => Carbon::now()->subDays(7),
                'End_Date' => Carbon::now()->addDays(60),
                'Capacity' => 15,
                'Status' => 'Ongoing'
            ],
            [
                'Title' => 'Community Tuition Center',
                'Description' => 'Weekly tutoring sessions for underprivileged students. Volunteers teach Math, Science, and English to primary school children.',
                'Location' => $organization->City . ' Learning Center',
                'Start_Date' => Carbon::now()->subDays(14),
                'End_Date' => Carbon::now()->addDays(45),
                'Capacity' => 20,
                'Status' => 'Ongoing'
            ],
            [
                'Title' => 'Mobile Food Distribution',
                'Description' => 'Weekly mobile food truck distributing groceries and cooked meals to low-income neighborhoods.',
                'Location' => 'Various locations in ' . $organization->State,
                'Start_Date' => Carbon::now()->subDays(21),
                'End_Date' => Carbon::now()->addDays(30),
                'Capacity' => 12,
                'Status' => 'Ongoing'
            ],

            // Completed Events
            [
                'Title' => 'Charity Run 2024',
                'Description' => 'Annual charity run event that was successfully completed last month. Raised significant funds for our cause.',
                'Location' => 'KLCC Park, Kuala Lumpur',
                'Start_Date' => Carbon::now()->subDays(30),
                'End_Date' => Carbon::now()->subDays(30),
                'Capacity' => 100,
                'Status' => 'Completed'
            ],
            [
                'Title' => 'Flood Relief Distribution',
                'Description' => 'Emergency relief distribution to families affected by recent floods. Distributed food, clothing, and emergency supplies.',
                'Location' => 'Pahang Flood Relief Center',
                'Start_Date' => Carbon::now()->subDays(45),
                'End_Date' => Carbon::now()->subDays(42),
                'Capacity' => 30,
                'Status' => 'Completed'
            ],
            [
                'Title' => 'Back to School Program',
                'Description' => 'Provided school supplies and new uniforms to 150 underprivileged students.',
                'Location' => $organization->Address,
                'Start_Date' => Carbon::now()->subDays(60),
                'End_Date' => Carbon::now()->subDays(60),
                'Capacity' => 25,
                'Status' => 'Completed'
            ],
            [
                'Title' => 'Ramadan Food Basket Distribution',
                'Description' => 'Distributed food baskets to 200 families during Ramadan. Each basket contained essential groceries for the month.',
                'Location' => 'Multiple distribution points',
                'Start_Date' => Carbon::now()->subDays(90),
                'End_Date' => Carbon::now()->subDays(85),
                'Capacity' => 35,
                'Status' => 'Completed'
            ],

            // Pending Events (Awaiting Approval)
            [
                'Title' => 'Skills Training Workshop',
                'Description' => 'Vocational training workshop teaching computer literacy, resume writing, and interview skills to job seekers.',
                'Location' => $organization->City . ' Skills Center',
                'Start_Date' => Carbon::now()->addDays(25),
                'End_Date' => Carbon::now()->addDays(27),
                'Capacity' => 30,
                'Status' => 'Pending'
            ],
            [
                'Title' => 'Orphanage Visit & Activity Day',
                'Description' => 'Planned visit to local orphanage with games, activities, and gift distribution for children.',
                'Location' => 'Rumah Anak Yatim Al-Falah',
                'Start_Date' => Carbon::now()->addDays(18),
                'End_Date' => Carbon::now()->addDays(18),
                'Capacity' => 20,
                'Status' => 'Pending'
            ],
            [
                'Title' => 'Medical Camp for Rural Community',
                'Description' => 'Free medical checkup, consultation, and basic medication for residents in rural areas.',
                'Location' => 'Kampung Sungai Rusa, ' . $organization->State,
                'Start_Date' => Carbon::now()->addDays(35),
                'End_Date' => Carbon::now()->addDays(35),
                'Capacity' => 25,
                'Status' => 'Pending'
            ],
            [
                'Title' => 'Youth Sports Tournament',
                'Description' => 'Organizing futsal and badminton tournament for underprivileged youth to promote healthy lifestyle and teamwork.',
                'Location' => 'Kompleks Sukan ' . $organization->City,
                'Start_Date' => Carbon::now()->addDays(40),
                'End_Date' => Carbon::now()->addDays(42),
                'Capacity' => 50,
                'Status' => 'Pending'
            ],
        ];

        foreach ($events as $eventData) {
            // Random created_at within the last week
            $createdAt = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $event = Event::create([
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

            // Attach volunteers to events with different statuses
            // Don't attach volunteers to Pending events
            if ($event->Status !== 'Pending') {
                $this->attachVolunteersToEvent($event, $volunteers);
            }
        }
    }

    private function attachVolunteersToEvent($event, $volunteers)
    {
        // Determine participation based on event status
        $statuses = ['Registered', 'Approved', 'Rejected', 'Completed'];

        // Determine how many volunteers should participate based on event status
        $participationCount = match($event->Status) {
            'Completed' => rand(3, min(5, $volunteers->count())),
            'Ongoing' => rand(2, min(4, $volunteers->count())),
            'Upcoming' => rand(1, min(3, $volunteers->count())),
            default => 0
        };

        $selectedVolunteers = $volunteers->random(min($participationCount, $volunteers->count()));

        foreach ($selectedVolunteers as $volunteer) {
            $participationStatus = match($event->Status) {
                'Completed' => 'Completed',
                'Ongoing' => ['Approved', 'Registered'][array_rand(['Approved', 'Registered'])],
                'Upcoming' => ['Registered', 'Approved', 'Rejected'][array_rand(['Registered', 'Approved', 'Rejected'])],
                default => 'Registered'
            };

            $totalHours = match($participationStatus) {
                'Completed' => rand(3, 8),
                'Approved' => rand(0, 4),
                default => 0
            };

            EventParticipation::create([
                'Volunteer_ID' => $volunteer->Volunteer_ID,
                'Event_ID' => $event->Event_ID,
                'Status' => $participationStatus,
                'Total_Hours' => $totalHours
            ]);
        }
    }
}
