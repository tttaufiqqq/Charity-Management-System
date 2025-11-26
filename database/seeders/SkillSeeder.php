<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            // Communication Skills
            [
                'Skill_Name' => 'Public Speaking',
                'Description' => 'Ability to speak confidently and effectively to groups of people'
            ],
            [
                'Skill_Name' => 'Written Communication',
                'Description' => 'Strong writing skills for reports, emails, and documentation'
            ],
            [
                'Skill_Name' => 'Interpersonal Communication',
                'Description' => 'Ability to interact effectively with diverse groups of people'
            ],

            // Technical Skills
            [
                'Skill_Name' => 'Social Media Management',
                'Description' => 'Managing and creating content for social media platforms'
            ],
            [
                'Skill_Name' => 'Graphic Design',
                'Description' => 'Creating visual content using design software'
            ],
            [
                'Skill_Name' => 'Video Editing',
                'Description' => 'Editing and producing video content'
            ],
            [
                'Skill_Name' => 'Web Development',
                'Description' => 'Building and maintaining websites'
            ],
            [
                'Skill_Name' => 'Data Entry',
                'Description' => 'Accurate and efficient data input and management'
            ],
            [
                'Skill_Name' => 'Photography',
                'Description' => 'Taking and editing professional photographs'
            ],

            // Healthcare & Medical
            [
                'Skill_Name' => 'First Aid',
                'Description' => 'Basic emergency medical care and CPR'
            ],
            [
                'Skill_Name' => 'Healthcare Support',
                'Description' => 'Assisting with patient care and medical support'
            ],
            [
                'Skill_Name' => 'Mental Health Support',
                'Description' => 'Providing emotional support and counseling'
            ],

            // Education & Training
            [
                'Skill_Name' => 'Teaching',
                'Description' => 'Instructing and educating individuals or groups'
            ],
            [
                'Skill_Name' => 'Tutoring',
                'Description' => 'One-on-one educational support and mentoring'
            ],
            [
                'Skill_Name' => 'Child Care',
                'Description' => 'Supervising and caring for children'
            ],
            [
                'Skill_Name' => 'Youth Mentoring',
                'Description' => 'Guiding and supporting young people'
            ],

            // Administrative Skills
            [
                'Skill_Name' => 'Event Planning',
                'Description' => 'Organizing and coordinating events and activities'
            ],
            [
                'Skill_Name' => 'Project Management',
                'Description' => 'Planning, executing, and overseeing projects'
            ],
            [
                'Skill_Name' => 'Fundraising',
                'Description' => 'Planning and executing fundraising campaigns'
            ],
            [
                'Skill_Name' => 'Office Administration',
                'Description' => 'General office tasks and administrative support'
            ],
            [
                'Skill_Name' => 'Bookkeeping',
                'Description' => 'Managing financial records and transactions'
            ],

            // Customer Service
            [
                'Skill_Name' => 'Customer Service',
                'Description' => 'Assisting and supporting customers or clients'
            ],
            [
                'Skill_Name' => 'Reception',
                'Description' => 'Greeting visitors and managing front desk operations'
            ],

            // Creative Skills
            [
                'Skill_Name' => 'Content Writing',
                'Description' => 'Creating written content for various purposes'
            ],
            [
                'Skill_Name' => 'Marketing',
                'Description' => 'Promoting and advertising services or events'
            ],
            [
                'Skill_Name' => 'Arts and Crafts',
                'Description' => 'Creating handmade items and artistic works'
            ],

            // Physical & Manual Labor
            [
                'Skill_Name' => 'Manual Labor',
                'Description' => 'Physical work including lifting and moving'
            ],
            [
                'Skill_Name' => 'Construction',
                'Description' => 'Building and repair work'
            ],
            [
                'Skill_Name' => 'Gardening',
                'Description' => 'Plant care and landscaping'
            ],
            [
                'Skill_Name' => 'Cleaning',
                'Description' => 'Maintaining cleanliness of facilities'
            ],

            // Food & Hospitality
            [
                'Skill_Name' => 'Cooking',
                'Description' => 'Preparing meals and food service'
            ],
            [
                'Skill_Name' => 'Food Service',
                'Description' => 'Serving food and beverage'
            ],

            // Transportation
            [
                'Skill_Name' => 'Driving',
                'Description' => 'Operating vehicles for transportation needs'
            ],

            // Languages
            [
                'Skill_Name' => 'Translation',
                'Description' => 'Converting text or speech between languages'
            ],
            [
                'Skill_Name' => 'Sign Language',
                'Description' => 'Communicating using sign language'
            ],

            // Animal Care
            [
                'Skill_Name' => 'Animal Care',
                'Description' => 'Caring for and handling animals'
            ],

            // Sports & Recreation
            [
                'Skill_Name' => 'Sports Coaching',
                'Description' => 'Training and coaching in sports activities'
            ],
            [
                'Skill_Name' => 'Recreation Leadership',
                'Description' => 'Leading recreational activities and programs'
            ],

            // Legal & Advocacy
            [
                'Skill_Name' => 'Legal Support',
                'Description' => 'Providing legal assistance and advocacy'
            ],
            [
                'Skill_Name' => 'Community Organizing',
                'Description' => 'Mobilizing and organizing community members'
            ],
        ];

        $timestamp = Carbon::now();

        foreach ($skills as &$skill) {
            $skill['created_at'] = $timestamp;
            $skill['updated_at'] = $timestamp;
        }

        DB::table('skill')->insert($skills);
    }
}

// ================================
// To run this seeder:
// php artisan db:seed --class=SkillSeeder
//
// Or add to DatabaseSeeder.php:
// $this->call([
//     SkillSeeder::class,
// ]);
// ================================
