<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles
        Role::firstOrCreate(['name' => 'admin'], [
            'display_name' => 'Administrator',
            'description' => 'Full system access',
            'is_system' => true,
        ]);

        Role::firstOrCreate(['name' => 'user'], [
            'display_name' => 'Regular User',
            'description' => 'Standard user access',
            'is_system' => true,
        ]);

        // Create portfolio owner (you)
        $owner = User::updateOrCreate(
            ['email' => 'hello@ultrashots.net'],
            [
                'first_name' => 'Kingsley',
                'last_name' => 'Nyirenda',
                'job_title' => 'Brand Strategist & UI Architect',
                'password' => bcrypt('password'),
                'phone_number' => '+265 995 000 000',
                'bio' => 'I help ambitious businesses in Malawi and beyond transform from invisible to irresistible through strategic brand positioning and conversion-focused design.',
                'website' => 'https://ultrashots.net',
                'location' => 'Lilongwe, Malawi',
                'timezone' => 'Africa/Blantyre',
                'socials' => [
                    'instagram' => 'https://instagram.com/ultrashots',
                    'linkedin' => 'https://linkedin.com/in/kingsleynyirenda',
                    'behance' => 'https://behance.net/ultrashoots',
                    'twitter' => 'https://twitter.com/ultrashots',
                ],
                'preferences' => [
                    'theme' => 'light',
                    'language' => 'en',
                ],
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Assign admin role
        $owner->assignRole('admin');

        // Seed portfolio content
        $this->call([
            CaseStudySeeder::class,
            PortfolioContentSeeder::class,
        ]);
    }
}
