<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Campaign;
use App\Models\Recipient;
use App\Models\DonationAllocation;
use App\Models\Organization;
use App\Models\PublicProfile;
use Carbon\Carbon;

class CampaignSeeder extends Seeder
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

        // Get existing public profiles
        $publicProfiles = PublicProfile::all();

        if ($publicProfiles->isEmpty()) {
            $this->command->warn('No public profiles found. Please run UserRoleSeeder first.');
            return;
        }

        // Create recipients first
        $recipients = $this->createRecipients($publicProfiles);

        // Create campaigns for each organization
        foreach ($organizations as $organization) {
            $this->createCampaignsForOrganization($organization, $recipients);
        }

        $this->command->info('Campaigns, recipients, and allocations seeded successfully!');
    }

    private function createRecipients($publicProfiles)
    {
        $recipientData = [
            [
                'Name' => 'Ahmad bin Abdullah',
                'Address' => '12 Jalan Mawar, Taman Sentosa',
                'Contact' => '+60129876543',
                'Need_Description' => 'Single parent with 3 children. Lost job during pandemic and struggling with daily expenses and children education.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Siti Nurhaliza',
                'Address' => '45 Lorong Cempaka, Kampung Baru',
                'Contact' => '+60138765432',
                'Need_Description' => 'Elderly woman living alone with medical condition requiring monthly medication. Limited income from pension.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Raj Kumar',
                'Address' => '78 Jalan Harmoni, Taman Sri Damai',
                'Contact' => '+60147654321',
                'Need_Description' => 'Family of 5 affected by flood. Lost household items and temporary shelter needed.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Mary Tan',
                'Address' => '23 Taman Bunga Raya',
                'Contact' => '+60156543210',
                'Need_Description' => 'Single mother with disabled child. Need financial assistance for special education and therapy.',
                'Status' => 'Pending',
            ],
            [
                'Name' => 'Wong Ah Kow',
                'Address' => '56 Jalan Selamat',
                'Contact' => '+60165432109',
                'Need_Description' => 'Elderly couple with chronic illnesses. Need assistance with medical bills and daily necessities.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Fatimah binti Hassan',
                'Address' => '89 Taman Harmoni',
                'Contact' => '+60174567890',
                'Need_Description' => 'Widow with 4 school-going children. Needs assistance with school fees and daily living expenses.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Kumar s/o Rajan',
                'Address' => '34 Jalan Melur',
                'Contact' => '+60183456789',
                'Need_Description' => 'Cancer patient undergoing treatment. Need help with medical expenses and transportation costs.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Lim Mei Ling',
                'Address' => '67 Taman Sejahtera',
                'Contact' => '+60192345678',
                'Need_Description' => 'Single mother working odd jobs. Children need educational support and nutrition assistance.',
                'Status' => 'Pending',
            ],
        ];

        $recipients = collect();

        foreach ($recipientData as $data) {
            // Distribute recipients among public profiles
            $publicProfile = $publicProfiles->random();

            $recipientCreate = [
                'Public_ID' => $publicProfile->Public_ID,
                'Name' => $data['Name'],
                'Address' => $data['Address'],
                'Contact' => $data['Contact'],
                'Need_Description' => $data['Need_Description'],
                'Status' => $data['Status'],
                'Approved_At' => null,
            ];

            $recipient = Recipient::create($recipientCreate);
            $recipients->push($recipient);
        }

        return $recipients;
    }

    private function createCampaignsForOrganization($organization, $recipients)
    {
        $campaigns = [
            // Active Campaigns
            [
                'Title' => 'Emergency Relief Fund',
                'Description' => 'Providing immediate financial assistance to families affected by natural disasters and emergencies. Funds will be distributed to verified recipients for food, shelter, and basic necessities.',
                'Goal_Amount' => 50000.00,
                'Collected_Amount' => 0.00,  // Will be updated by DonationSeeder
                'Start_Date' => Carbon::now()->subDays(60),
                'End_Date' => Carbon::now()->addDays(30),
                'Status' => 'Active'
            ],
            [
                'Title' => 'Healthcare Assistance',
                'Description' => 'Providing medical assistance to low-income families including medication, treatment costs, and health screening services.',
                'Goal_Amount' => 40000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(30),
                'End_Date' => Carbon::now()->addDays(60),
                'Status' => 'Active'
            ],
            [
                'Title' => 'Community Development Fund',
                'Description' => 'Building and improving community facilities, providing skills training, and creating sustainable livelihood opportunities.',
                'Goal_Amount' => 60000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(15),
                'End_Date' => Carbon::now()->addDays(90),
                'Status' => 'Active'
            ],
            [
                'Title' => 'Children Education Support',
                'Description' => 'Helping underprivileged children with school fees, books, uniforms, and learning materials. Every child deserves quality education.',
                'Goal_Amount' => 35000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(20),
                'End_Date' => Carbon::now()->addDays(70),
                'Status' => 'Active'
            ],
            [
                'Title' => 'Elderly Care Program',
                'Description' => 'Supporting senior citizens with medical care, daily necessities, and companionship services. Help us care for our elders.',
                'Goal_Amount' => 28000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(10),
                'End_Date' => Carbon::now()->addDays(50),
                'Status' => 'Active'
            ],
            [
                'Title' => 'Clean Water Initiative',
                'Description' => 'Providing clean water access to rural communities through water filtration systems and infrastructure development.',
                'Goal_Amount' => 45000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(25),
                'End_Date' => Carbon::now()->addDays(65),
                'Status' => 'Active'
            ],
            [
                'Title' => 'Mental Health Awareness',
                'Description' => 'Providing free counseling services, support groups, and mental health education to the community.',
                'Goal_Amount' => 22000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(5),
                'End_Date' => Carbon::now()->addDays(85),
                'Status' => 'Active'
            ],

            // Completed Campaigns
            [
                'Title' => 'Education Support Program',
                'Description' => 'Supporting underprivileged children with school supplies, tuition fees, and educational resources. Help us give every child a chance at quality education.',
                'Goal_Amount' => 30000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(120),
                'End_Date' => Carbon::now()->subDays(30),
                'Status' => 'Completed'
            ],
            [
                'Title' => 'Food Bank Initiative',
                'Description' => 'Monthly food distribution program providing groceries and basic necessities to families in need. Every contribution helps feed a family.',
                'Goal_Amount' => 25000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(150),
                'End_Date' => Carbon::now()->subDays(60),
                'Status' => 'Completed'
            ],
            [
                'Title' => 'Flood Relief 2024',
                'Description' => 'Emergency response to recent flooding. Provided food, shelter, and essential supplies to affected families.',
                'Goal_Amount' => 55000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(100),
                'End_Date' => Carbon::now()->subDays(70),
                'Status' => 'Completed'
            ],
            [
                'Title' => 'Back to School 2024',
                'Description' => 'Provided school supplies, uniforms, and bags to 200 underprivileged students for the new academic year.',
                'Goal_Amount' => 18000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(180),
                'End_Date' => Carbon::now()->subDays(150),
                'Status' => 'Completed'
            ],
            [
                'Title' => 'Medical Equipment Drive',
                'Description' => 'Successfully raised funds to purchase wheelchairs, crutches, and medical equipment for disabled individuals.',
                'Goal_Amount' => 32000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(90),
                'End_Date' => Carbon::now()->subDays(50),
                'Status' => 'Completed'
            ],
        ];

        foreach ($campaigns as $campaignData) {
            // Random created_at within the last week
            $createdAt = Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            $campaign = Campaign::create([
                'Organization_ID' => $organization->Organization_ID,
                'Title' => $campaignData['Title'],
                'Description' => $campaignData['Description'],
                'Goal_Amount' => $campaignData['Goal_Amount'],
                'Collected_Amount' => $campaignData['Collected_Amount'],
                'Start_Date' => $campaignData['Start_Date'],
                'End_Date' => $campaignData['End_Date'],
                'Status' => $campaignData['Status'],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Note: Collected_Amount will be updated by DonationSeeder
            // We're setting it to 0 here so DonationSeeder can properly increment it
        }
    }
}
