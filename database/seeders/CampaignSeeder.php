<?php

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\Organization;
use App\Models\PublicProfile;
use App\Models\Recipient;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

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
                'Address' => 'No. 12, Jalan Mawar 3, Taman Sentosa, 56000 Kuala Lumpur',
                'Contact' => '+60129876543',
                'Need_Description' => 'Single father with 3 school-age children (ages 8, 11, 14). Former restaurant worker who lost employment during MCO. Currently unemployed and struggling with rent arrears (RM 2,400), children\'s education expenses, and basic necessities. Eldest child had to drop tuition classes. Seeking assistance for food, school supplies, and utility bills.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Siti Nurhaliza binti Ismail',
                'Address' => 'No. 45, Lorong Cempaka 2/3, Kampung Baru, 50300 Kuala Lumpur',
                'Contact' => '+60138765432',
                'Need_Description' => '68-year-old widow living alone. Diagnosed with diabetes and hypertension requiring monthly medication (RM 180/month). Relies on BR1M and children\'s occasional support. Recent hospitalization depleted savings. Need assistance with medical expenses, medications, and nutritious food for diabetic diet.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Raj Kumar a/l Subramaniam',
                'Address' => 'No. 78, Jalan Harmoni 12, Taman Sri Damai, 81100 Johor Bahru',
                'Contact' => '+60147654321',
                'Need_Description' => 'Family of 5 (couple + 3 children) severely affected by December 2024 floods. Lost furniture, electrical appliances, children\'s school items, and important documents. Currently staying at relief center. Estimated losses: RM 15,000. Need assistance to replace essential items, secure temporary housing, and restart livelihood (vegetable stall damaged).',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Mary Tan Siew Choo',
                'Address' => 'No. 23-1, Taman Bunga Raya, 14000 Bukit Mertajam, Penang',
                'Contact' => '+60156543210',
                'Need_Description' => 'Single mother caring for 7-year-old autistic son. Child requires special education (RM 800/month) and weekly occupational therapy (RM 240/month). Mother works part-time earning RM 1,200/month. Struggling to afford therapy, specialized diet, and sensory development tools. Application pending verification by social welfare.',
                'Status' => 'Pending',
            ],
            [
                'Name' => 'Wong Ah Kow & Lee Mei Fong',
                'Address' => 'No. 56, Jalan Selamat, Taman Megah, 30000 Ipoh, Perak',
                'Contact' => '+60165432109',
                'Need_Description' => 'Elderly couple (both 72 years old) with multiple chronic conditions - husband has kidney disease (on dialysis 3x/week), wife has heart condition. Monthly medical expenses exceed RM 2,500. Children are low-income workers providing minimal support. Need assistance with dialysis costs, medications, special dietary requirements, and home modifications for mobility.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Fatimah binti Hassan',
                'Address' => 'No. 89, Taman Harmoni, 43000 Kajang, Selangor',
                'Contact' => '+60174567890',
                'Need_Description' => 'Widow with 4 school-going children (ages 7-15) following husband\'s sudden death in traffic accident 8 months ago. Working as cleaner earning RM 1,400/month. Struggling with house rent (RM 600), children\'s school fees, uniforms, books, and daily expenses. Eldest daughter wants to continue studies but may need to work. Seeking educational support and household assistance.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Kumar s/o Rajan',
                'Address' => 'No. 34, Jalan Melur 5/8, Taman Bahagia, 43200 Cheras, Selangor',
                'Contact' => '+60183456789',
                'Need_Description' => '45-year-old diagnosed with Stage 3 colon cancer. Undergoing chemotherapy at government hospital. Treatment side effects prevent working (former lorry driver). Wife works as factory operator earning RM 1,600/month supporting family of 4. Need assistance with supplementary medications not covered by government (RM 500/month), nutritional supplements, transportation to hospital (RM 200/month), and household expenses during treatment.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Lim Mei Ling',
                'Address' => 'No. 67-2, Taman Sejahtera, 14100 Simpang Ampat, Penang',
                'Contact' => '+60192345678',
                'Need_Description' => 'Single mother of 3 children (ages 5, 9, 12) working multiple odd jobs - morning market helper and evening food stall assistant. Total monthly income unstable, averaging RM 1,100. Children showing signs of malnutrition. Eldest child struggling academically due to lack of tuition and study materials. Need assistance with nutritious meals, children\'s education support, and skills training for mother to secure stable employment. Application under review.',
                'Status' => 'Pending',
            ],
            [
                'Name' => 'Mohd Hafiz bin Abdul Rahman',
                'Address' => 'No. 112, Kampung Sungai Ramal, 43000 Kajang, Selangor',
                'Contact' => '+60198765432',
                'Need_Description' => 'Father of 5 children, suffered stroke 6 months ago leaving him partially paralyzed and unable to work. Former construction worker with no EPF savings. Wife sells kuih door-to-door earning RM 600-800/month. Family living in dilapidated wooden house requiring urgent repairs. Need assistance with medical rehabilitation, wheelchair, home repairs, children\'s education, and food aid.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Devi a/p Muniandy',
                'Address' => 'No. 23, Ladang Bukit Jalil, 57000 Kuala Lumpur',
                'Contact' => '+60187654321',
                'Need_Description' => 'Former estate worker, retrenched due to plantation closure. Single mother with 2 teenage children. Living in estate quarters with uncertain tenure. Daughter (16) has potential for university but family cannot afford. Son (13) showing academic promise but needs educational support. Mother seeking re-skilling opportunities. Need assistance with housing security, children\'s education fees, skills training for livelihood, and immediate food assistance.',
                'Status' => 'Approved',
            ],
            [
                'Name' => 'Roslan bin Hashim',
                'Address' => 'No. 45, Flat PKNS, Seksyen 7, 40000 Shah Alam, Selangor',
                'Contact' => '+60176543210',
                'Need_Description' => '50-year-old former factory worker recently diagnosed with liver cirrhosis. Medical condition requires dietary restrictions and monthly hospital visits. Unable to work, relying on wife\'s income as kindergarten assistant (RM 1,200/month). Family of 4 struggling with medical costs not fully covered by government aid. Awaiting specialist liver treatment. Need assistance with medical expenses, special dietary requirements, and household income support.',
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
                'Title' => 'Emergency Flood Relief Fund 2024/2025',
                'Description' => 'URGENT: Help flood-affected families in Kelantan, Terengganu, and Pahang rebuild their lives. Recent monsoon floods have displaced over 300 families, destroying homes and livelihoods. Your contribution provides emergency food supplies (RM 150/family), temporary shelter assistance (RM 800/family), clean water and hygiene kits (RM 100/family), replacement of essential household items, and medical aid. Every RM 50 makes a difference. Funds distributed directly to verified victims through collaboration with local welfare departments.',
                'Goal_Amount' => 85000.00,
                'Collected_Amount' => 0.00,  // Will be updated by DonationSeeder
                'Start_Date' => Carbon::now()->subDays(60),
                'End_Date' => Carbon::now()->addDays(30),
                'Status' => 'Active',
            ],
            [
                'Title' => 'Medical Fund for Critical Patients',
                'Description' => 'Supporting low-income families facing critical medical emergencies. Many Malaysian families struggle with medical costs not covered by government aid - dialysis treatments, cancer medications, emergency surgeries, and chronic disease management. This fund helps 50+ families monthly with: subsidized dialysis (RM 200-300/session), cancer treatment supplements (RM 500/month), urgent surgical procedures, specialized medications, hospital transportation costs. 100% of donations go directly to verified patients. Past beneficiaries include kidney patients, cancer survivors, and stroke victims requiring ongoing care.',
                'Goal_Amount' => 120000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(30),
                'End_Date' => Carbon::now()->addDays(60),
                'Status' => 'Active',
            ],
            [
                'Title' => 'Skills Training & Employment Program',
                'Description' => 'Empowering unemployed Malaysians with marketable skills for sustainable livelihoods. Target: 100 participants from B40 families. Training programs include: Mobile phone repair course (6 weeks, RM 800/participant), Basic tailoring and alterations (8 weeks), F&B operations and food handling, Digital marketing for small businesses, Starter tool kits provided upon completion. Program includes job placement assistance and micro-financing for those starting small businesses. Success rate: 75% of graduates gainfully employed within 3 months. Help break the cycle of poverty through skills, not handouts.',
                'Goal_Amount' => 95000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(15),
                'End_Date' => Carbon::now()->addDays(90),
                'Status' => 'Active',
            ],
            [
                'Title' => 'Back to School 2025 Program',
                'Description' => 'Ensure no child is denied education due to poverty. Providing comprehensive school assistance for 250 underprivileged students (Primary & Secondary) including: Complete school uniforms and shoes (RM 180/student), School bags and stationery sets (RM 100/student), Textbooks and workbooks (RM 150/student), Tuition fee assistance for SJKT/SJKC students, Monthly transportation allowance (RM 50 x 10 months). Focus on children from single-parent families, orphans, and families with disabled parents. Partner schools in Selangor, KL, and Negeri Sembilan. Your RM 500 sponsors one student for the entire academic year.',
                'Goal_Amount' => 130000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(20),
                'End_Date' => Carbon::now()->addDays(70),
                'Status' => 'Active',
            ],
            [
                'Title' => 'Elderly Care & Medical Support',
                'Description' => 'Our elders deserve dignity and care in their golden years. Supporting 80+ senior citizens (ages 60-85) living alone or with minimal family support. Monthly provisions include: Chronic disease medications (diabetes, hypertension, heart), Weekly grocery assistance (RM 150/month), Adult diapers and medical supplies, Home visits by volunteer nurses, Social companionship programs to combat loneliness. Priority given to bedridden elderly, stroke survivors, and those without EPF/pension. Collaboration with government hospitals for subsidized medication. Many of our beneficiaries are former laborers who built Malaysia but now face old age without savings.',
                'Goal_Amount' => 72000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(10),
                'End_Date' => Carbon::now()->addDays(50),
                'Status' => 'Active',
            ],
            [
                'Title' => 'Clean Water for Orang Asli Villages',
                'Description' => 'Installing water filtration systems in 5 remote Orang Asli villages in Pahang where communities still lack access to safe drinking water. Current situation: Villagers walk 2-3 km daily to fetch water from contaminated streams, causing waterborne diseases especially among children. Project scope: Bio-sand water filters for 45 households (RM 800/unit), Community rainwater harvesting system (RM 15,000), Water quality testing and monitoring (RM 5,000), Health education workshops on water hygiene. Implementation period: 4 months. Partner: Department of Orang Asli Development (JAKOA). Impact: 280 indigenous people will have access to clean water, reducing child mortality from diarrheal diseases.',
                'Goal_Amount' => 65000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(25),
                'End_Date' => Carbon::now()->addDays(65),
                'Status' => 'Active',
            ],
            [
                'Title' => 'Mental Health Support & Counseling',
                'Description' => 'Breaking the stigma of mental health in Malaysian communities. Providing accessible, affordable mental health services to those who cannot afford private counseling. Services offered: Free individual counseling sessions (RM 80/session value), Support groups for depression, anxiety, grief, Family counseling for domestic issues, Crisis intervention hotline, Mental health awareness workshops in schools and workplaces. Served 200+ individuals last year including youth facing academic pressure, working adults with burnout, domestic abuse survivors, and families in crisis. All counselors are licensed professionals. Especially needed post-pandemic as mental health cases surge. Your support saves lives.',
                'Goal_Amount' => 48000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(5),
                'End_Date' => Carbon::now()->addDays(85),
                'Status' => 'Active',
            ],

            // Completed Campaigns
            [
                'Title' => 'Orphan Education Fund 2024',
                'Description' => 'SUCCESSFULLY COMPLETED! Thanks to 450+ generous donors, we raised RM 42,500 to support 85 orphans throughout 2024. Achievements: Provided full school fee assistance for 85 students, Distributed school uniforms, books, and supplies, Sponsored 40 students for tuition classes, Provided laptops for 15 secondary school students, Created scholarship fund for 5 SPM top scorers. Impact: 100% of sponsored students advanced to next grade, 3 students scored straight As in UPSR. This campaign has changed lives - your continued support for our current education programs helps more children succeed.',
                'Goal_Amount' => 40000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(120),
                'End_Date' => Carbon::now()->subDays(30),
                'Status' => 'Completed',
            ],
            [
                'Title' => 'Ramadan Food Basket 2024',
                'Description' => 'CAMPAIGN SUCCESSFUL! Distributed food baskets to 320 families during Ramadan, exceeding our target of 250 families. Each basket (value RM 180) contained: Rice, cooking oil, flour, sugar, dates, Canned goods, biscuits, beverages, Halal meat and chicken, Special Ramadan items, Fresh vegetables. Distribution covered 8 locations across Klang Valley. Thank you to all donors and 50+ volunteers who made this possible. Beneficiaries included single mothers, elderly, refugees, and disabled individuals. Many families expressed this was their only proper meal during the fasting month.',
                'Goal_Amount' => 45000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(150),
                'End_Date' => Carbon::now()->subDays(60),
                'Status' => 'Completed',
            ],
            [
                'Title' => 'Kelantan Flood Emergency Relief 2023',
                'Description' => 'MISSION ACCOMPLISHED! Rapid response to December 2023 floods that displaced 8,000+ people in Kelantan. Campaign raised RM 68,900, surpassing goal by 25%. Relief provided: Emergency food packs for 450 families (first week), Clean water and hygiene kits (1,500 sets), Temporary shelter materials (200 families), Basic medication and first aid (5 clinics), School supply replacement (180 students). Collaborated with NADMA and local authorities. All funds accounted and distributed within 4 weeks. Donor reports sent to all contributors. Thank you for helping Malaysians in crisis. Your support truly saved lives.',
                'Goal_Amount' => 55000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(100),
                'End_Date' => Carbon::now()->subDays(70),
                'Status' => 'Completed',
            ],
            [
                'Title' => 'SPM/STPM Study Aid Program',
                'Description' => 'PROGRAM COMPLETED WITH EXCELLENT RESULTS! Provided study assistance to 75 students from low-income families preparing for SPM/STPM exams. Support included: Free intensive revision classes (8 weeks), Complete reference books and revision materials, Past year papers and online learning access, Exam fees for 30 students (RM 200/student), Motivational workshops and study skills training. Outcome: 68% achieved minimum 5 credits including BM and Maths, 12 students scored straight As, 85% qualified for public universities/polytechnics. Campaign cost: RM 24,500. Success stories: 3 students received JPA scholarships. Continuing this program in 2025 - support our current education campaigns!',
                'Goal_Amount' => 25000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(180),
                'End_Date' => Carbon::now()->subDays(150),
                'Status' => 'Completed',
            ],
            [
                'Title' => 'Wheelchair & Mobility Aid Distribution',
                'Description' => 'SUCCESSFULLY DISTRIBUTED! Procured and distributed mobility aids to 65 disabled individuals and accident victims. Items provided: 25 wheelchairs (manual and electric), 15 walking frames and crutches, 12 hospital beds for bedridden patients, 8 commode chairs, 5 shower chairs and bathroom aids. Total value: RM 48,200 including assessment, delivery, and basic training. Recipients aged 8-82 years across Selangor and KL. Partnered with Malaysian Disabled Persons Association. Life-changing impact reported - individuals gained independence and dignity. Follow-up home visits conducted for all recipients. Thank you donors for restoring mobility and hope to our disabled community members.',
                'Goal_Amount' => 45000.00,
                'Collected_Amount' => 0.00,
                'Start_Date' => Carbon::now()->subDays(90),
                'End_Date' => Carbon::now()->subDays(50),
                'Status' => 'Completed',
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
