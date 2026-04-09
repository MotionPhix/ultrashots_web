<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Project;
use Illuminate\Database\Seeder;

class CaseStudySeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            Customer::firstOrCreate(['email' => 'info@malawingo.org'], [
                'company_name' => 'Malawi NGO Coalition',
                'first_name' => 'Grace',
                'last_name' => 'Phiri',
                'phone_number' => '+265 999 123 456',
                'city' => 'Lilongwe',
                'country' => 'Malawi',
            ]),
            Customer::firstOrCreate(['email' => 'hello@limbehospitality.com'], [
                'company_name' => 'Limbe Hospitality Group',
                'first_name' => 'James',
                'last_name' => 'Kamwendo',
                'phone_number' => '+265 888 234 567',
                'city' => 'Blantyre',
                'country' => 'Malawi',
            ]),
            Customer::firstOrCreate(['email' => 'contact@agriinvest.mw'], [
                'company_name' => 'AgriInvest Malawi',
                'first_name' => 'Sarah',
                'last_name' => 'Banda',
                'phone_number' => '+265 999 345 678',
                'city' => 'Zomba',
                'country' => 'Malawi',
            ]),
            Customer::firstOrCreate(['email' => 'info@techhubmw.com'], [
                'company_name' => 'TechHub Malawi',
                'first_name' => 'David',
                'last_name' => 'Chirwa',
                'phone_number' => '+265 888 456 789',
                'city' => 'Lilongwe',
                'country' => 'Malawi',
            ]),
            Customer::firstOrCreate(['email' => 'hello@zombacoffee.com'], [
                'company_name' => 'Zomba Coffee Collective',
                'first_name' => 'Esther',
                'last_name' => 'Mkandawire',
                'phone_number' => '+265 999 567 890',
                'city' => 'Zomba',
                'country' => 'Malawi',
            ]),
            Customer::firstOrCreate(['email' => 'info@lakeshoreproperties.mw'], [
                'company_name' => 'LakeShore Properties',
                'first_name' => 'Michael',
                'last_name' => 'Nkhata',
                'phone_number' => '+265 888 678 901',
                'city' => 'Cape Maclear',
                'country' => 'Malawi',
            ]),
            Customer::firstOrCreate(['email' => 'contact@healthfirstmw.org'], [
                'company_name' => 'HealthFirst Malawi',
                'first_name' => 'Dr. Patricia',
                'last_name' => 'Jere',
                'phone_number' => '+265 999 789 012',
                'city' => 'Lilongwe',
                'country' => 'Malawi',
            ]),
            Customer::firstOrCreate(['email' => 'hello@mwfashionweek.com'], [
                'company_name' => 'Malawi Fashion Week',
                'first_name' => 'Tione',
                'last_name' => 'Gondwe',
                'phone_number' => '+265 888 890 123',
                'city' => 'Lilongwe',
                'country' => 'Malawi',
            ]),
        ];

        $projects = [
            [
                'name' => 'Malawi NGO Coalition - Digital Awareness Campaign',
                'slug' => 'malawi-ngo-coalition-digital-campaign',
                'customer_id' => $customers[0]->id,
                'category' => 'NGO Campaign',
                'production_type' => 'Branding & Digital Strategy',
                'short_description' => 'Rebranding and digital campaign that increased donor engagement by 47% for coalition of 12 NGOs.',
                'description' => "The Malawi NGO Coalition struggled with fragmented brand presence across 12 member organizations, resulting in diluted messaging and stagnant donor engagement. We developed a unified visual identity system and launched a coordinated digital awareness campaign.\n\nOur approach centered on creating emotional connections through authentic storytelling, showcasing real impact stories from beneficiaries across Malawi.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 1,
                'technologies' => ['Figma', 'Adobe Creative Suite', 'Meta Ads Manager'],
                'poster_url' => 'https://picsum.photos/seed/ngo-coalition/800/600',
                'results' => [
                    ['metric' => 'Donor engagement rate', 'value' => '+47%'],
                    ['metric' => 'Monthly donations', 'value' => '+MK 2.3M'],
                    ['metric' => 'Social media reach', 'value' => '180K+'],
                ],
            ],
            [
                'name' => 'Limbe Hospitality Group - Luxury Repositioning',
                'slug' => 'limbe-hospitality-luxury-repositioning',
                'customer_id' => $customers[1]->id,
                'category' => 'Brand Strategy',
                'production_type' => 'Brand Strategy & Visual Identity',
                'short_description' => 'Luxury repositioning that increased average booking value by 34% for boutique hotel group.',
                'description' => "Limbe Hospitality Group wanted to compete in the luxury market but lacked the brand presence to justify premium pricing. We repositioned them from budget-friendly to luxury boutique experience.\n\nThrough strategic visual identity redesign and refined messaging, we attracted high-net-worth travelers seeking authentic Malawian luxury experiences.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 2,
                'technologies' => ['Figma', 'Webflow', 'Lightroom'],
                'poster_url' => 'https://picsum.photos/seed/limbe-hospitality/800/600',
                'results' => [
                    ['metric' => 'Average booking value', 'value' => '+34%'],
                    ['metric' => 'Luxury segment bookings', 'value' => '+127%'],
                    ['metric' => 'Brand perception score', 'value' => '8.9/10'],
                ],
            ],
            [
                'name' => 'AgriInvest Malawi - Digital Investment Platform',
                'slug' => 'agriinvest-malawi-platform',
                'customer_id' => $customers[2]->id,
                'category' => 'UI/UX Design',
                'production_type' => 'UI/UX Design & Development',
                'short_description' => 'Investment platform UI that secured MK 45M in funding within first quarter of launch.',
                'description' => "AgriInvest Malawi needed to build trust with international investors through a professional, transparent platform. We designed an intuitive dashboard showcasing real-time agricultural data and investment opportunities.\n\nThe clean, data-rich interface made complex agricultural metrics accessible, helping investors make confident decisions.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 3,
                'technologies' => ['Figma', 'Vue.js', 'D3.js'],
                'poster_url' => 'https://picsum.photos/seed/agriinvest/800/600',
                'results' => [
                    ['metric' => 'Funding secured', 'value' => 'MK 45M'],
                    ['metric' => 'Investor sign-ups', 'value' => '340+'],
                    ['metric' => 'Platform adoption', 'value' => '92%'],
                ],
            ],
            [
                'name' => 'TechHub Malawi - Startup Ecosystem Launch',
                'slug' => 'techhub-malawi-launch',
                'customer_id' => $customers[3]->id,
                'category' => 'Brand Identity',
                'production_type' => 'Brand Identity & Marketing',
                'short_description' => 'Complete brand launch that attracted 247 startup applications in first month.',
                'description' => "TechHub Malawi needed to establish itself as the premier startup incubator in the region. We created a dynamic brand identity that resonated with young entrepreneurs and international partners.\n\nOur strategy positioned TechHub as the bridge between Malawian innovation and global opportunity.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 4,
                'technologies' => ['Illustrator', 'After Effects', 'React'],
                'poster_url' => 'https://picsum.photos/seed/techhub/800/600',
                'results' => [
                    ['metric' => 'Startup applications', 'value' => '247'],
                    ['metric' => 'Media mentions', 'value' => '38'],
                    ['metric' => 'Partner inquiries', 'value' => '+180%'],
                ],
            ],
            [
                'name' => 'Zomba Coffee Collective - Export Packaging Design',
                'slug' => 'zomba-coffee-packaging',
                'customer_id' => $customers[4]->id,
                'category' => 'Packaging Design',
                'production_type' => 'Packaging & Brand Design',
                'short_description' => 'Export-ready packaging design that drove 156% revenue increase in European markets.',
                'description' => "Zomba Coffee Collective had exceptional coffee but amateur packaging that couldn't compete in premium markets. We designed export-ready packaging that communicated origin, quality, and sustainability.\n\nThe new design opened doors to 18 European specialty cafes and doubled their export revenue.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 5,
                'technologies' => ['Illustrator', 'Photoshop', '3D Mockups'],
                'poster_url' => 'https://picsum.photos/seed/zomba-coffee/800/600',
                'results' => [
                    ['metric' => 'Revenue increase', 'value' => '+156%'],
                    ['metric' => 'New cafe partnerships', 'value' => '18'],
                    ['metric' => 'Export markets', 'value' => '7 countries'],
                ],
            ],
            [
                'name' => 'LakeShore Properties - Luxury Marketing Suite',
                'slug' => 'lakeshore-properties-marketing',
                'customer_id' => $customers[5]->id,
                'category' => 'Marketing Collateral',
                'production_type' => 'Marketing & Sales Collateral',
                'short_description' => 'Marketing suite that reduced sales cycle from 6 months to 8 weeks for luxury villas.',
                'description' => "LakeShore Properties struggled to justify premium pricing for their lakeside villas. We created a comprehensive marketing suite including brochures, virtual tours, and presentation materials.\n\nThe professional materials helped buyers visualize the lifestyle, accelerating decision-making and justifying the luxury price point.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 6,
                'technologies' => ['Premiere Pro', 'InDesign', 'Webflow'],
                'poster_url' => 'https://picsum.photos/seed/lakeshore/800/600',
                'results' => [
                    ['metric' => 'Sales cycle reduction', 'value' => '-78%'],
                    ['metric' => 'Average sale price', 'value' => '+22%'],
                    ['metric' => 'Qualified leads', 'value' => '+165%'],
                ],
            ],
            [
                'name' => 'HealthFirst Malawi - Public Health Campaign',
                'slug' => 'healthfirst-public-health',
                'customer_id' => $customers[6]->id,
                'category' => 'NGO Campaign',
                'production_type' => 'Campaign Design & Strategy',
                'short_description' => 'Health awareness campaign that reached 52K+ women across rural Malawi.',
                'description' => "HealthFirst Malawi needed to communicate critical maternal health information to rural communities with low literacy rates. We designed a visual-first campaign using culturally relevant imagery and simple messaging.\n\nThe campaign reached 52,000+ women through radio partnerships, community workshops, and mobile-friendly digital assets.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 7,
                'technologies' => ['Illustrator', 'Radio Scripts', 'SMS Platform'],
                'poster_url' => 'https://picsum.photos/seed/healthfirst/800/600',
                'results' => [
                    ['metric' => 'Women reached', 'value' => '52K+'],
                    ['metric' => 'Health literacy increase', 'value' => '+41%'],
                    ['metric' => 'Clinic visits', 'value' => '+28%'],
                ],
            ],
            [
                'name' => 'Malawi Fashion Week - Event Rebrand',
                'slug' => 'malawi-fashion-week-rebrand',
                'customer_id' => $customers[7]->id,
                'category' => 'Event Branding',
                'production_type' => 'Event Branding & Marketing',
                'short_description' => 'Event rebrand that sold out in 72 hours and attracted 23 international buyers.',
                'description' => "Malawi Fashion Week was seen as a local event with limited commercial appeal. We rebranded it as a premier fashion business platform connecting African designers with international buyers.\n\nThe new positioning attracted 23 international buyers and sold out all 450 tickets within 72 hours.",
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 8,
                'technologies' => ['Figma', 'Motion Graphics', 'Ticketing API'],
                'poster_url' => 'https://picsum.photos/seed/malawi-fashion/800/600',
                'results' => [
                    ['metric' => 'Tickets sold', 'value' => '72 hrs'],
                    ['metric' => 'International buyers', 'value' => '23'],
                    ['metric' => 'Media coverage', 'value' => '14 outlets'],
                ],
            ],

        ];

        foreach ($projects as $projectData) {
            Project::updateOrCreate(['slug' => $projectData['slug']], $projectData);
        }

        $this->command->info('✅ Created 8 strategic case studies with Malawi-focused projects');
    }
}
