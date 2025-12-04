<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');

        // Seed in specific order due to dependencies
        $this->call([
            // 1. First create roles and permissions
            RoleAndPermissionSeeder::class,

            // 2. Create users (will auto-assign roles)
            UserSeeder::class,

            // 3. Create customers
            CustomerSeeder::class,

            // 4. Create additional projects (customers already have some)
            ProjectSeeder::class,

            // 5. Create logos/downloads
            // LogoSeeder::class,

            // 6. Create subscribers
            SubscriberSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('🎉 Database seeding completed successfully!');
        $this->command->info('');
        $this->command->info('📊 Summary:');
        $this->command->info('- Roles & Permissions: 6 roles, 30+ permissions');
        $this->command->info('- Users: 11 users with various roles');
        $this->command->info('- Customers: 15+ customers with projects');
        $this->command->info('- Projects: 50+ projects with different statuses');
        $this->command->info('- Logos: 31 logo downloads');
        $this->command->info('- Subscribers: 70 newsletter subscribers');
        $this->command->info('');
        $this->command->info('🔑 Login credentials:');
        $this->command->info('Super Admin: hello@ultrashots.net / run%$Ace5');
        $this->command->info('Manager: manager@example.com / password');
        $this->command->info('Editor: editor@example.com / password');
        $this->command->info('Viewer: viewer@example.com / password');
        $this->command->info('');
        $this->command->info('🚀 Your portfolio is ready to showcase!');
    }
}
