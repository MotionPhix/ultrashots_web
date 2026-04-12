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
                'challenges' => 'The coalition faced three critical challenges: First, each of the 12 member NGOs had different visual identities, causing confusion among donors about who they were supporting. Second, their messaging was inconsistent across platforms, weakening their collective impact. Third, donor engagement had plateaued at 23% despite growing social media followings, indicating a disconnect between awareness and action.',
                'solutions' => 'We created a master brand architecture that unified all 12 NGOs under one visual identity while preserving their individual missions. Developed a comprehensive content strategy featuring beneficiary success stories, implemented coordinated social media campaigns across Facebook, Instagram, and LinkedIn, and created shareable video content that humanized the impact of donations.',
                'features' => [
                    'Unified brand identity system',
                    'Multi-platform social media strategy',
                    'Video storytelling series',
                    'Donor engagement dashboard',
                    'Content calendar & guidelines',
                    'Impact reporting templates',
                ],
                'client_feedback' => 'Working with UltraShots transformed how we communicate our mission. The unified brand gave us credibility we never had before, and the storytelling approach helped donors connect emotionally with our work. We saw immediate results in both engagement and donations.',
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 1,
                'technologies' => ['Figma', 'Adobe Creative Suite', 'Meta Ads Manager'],
                'poster_url' => 'https://picsum.photos/seed/ngo-coalition/800/600',
                'process_steps' => [
                    [
                        'title' => 'Discovery & Research',
                        'description' => 'Conducted stakeholder interviews with all 12 member NGOs, analyzed donor behavior patterns, and identified key messaging gaps across the coalition.',
                    ],
                    [
                        'title' => 'Unified Brand Strategy',
                        'description' => 'Developed a cohesive visual identity system that represented all member organizations while maintaining their individual strengths. Created brand guidelines for consistent application.',
                    ],
                    [
                        'title' => 'Content Creation',
                        'description' => 'Produced authentic storytelling content featuring real beneficiary stories from across Malawi. Shot photography and video at project sites to create emotional connections.',
                    ],
                    [
                        'title' => 'Digital Campaign Launch',
                        'description' => 'Executed coordinated social media campaign across multiple platforms, targeting both local and international donors with tailored messaging for each audience segment.',
                    ],
                ],
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
                'challenges' => 'Limbe Hospitality was perceived as a mid-range option despite having luxury-level amenities. Their branding communicated affordability rather than exclusivity, attracting budget-conscious travelers who didn\'t appreciate the premium experience. Online reviews consistently mentioned "great value" but rarely "luxury" or "exceptional," limiting their ability to command higher rates.',
                'solutions' => 'We conducted a complete brand repositioning, shifting from value-focused messaging to experience-driven storytelling. Redesigned the visual identity with sophisticated typography, refined color palette, and elegant photography style. Created new website emphasizing unique experiences, personalized service, and exclusive amenities. Developed partnerships with luxury travel agencies and implemented staff training on five-star service standards.',
                'features' => [
                    'Complete brand identity redesign',
                    'Luxury positioning strategy',
                    'Premium website development',
                    'Professional photography & videography',
                    'Staff service training program',
                    'Luxury travel agency partnerships',
                ],
                'client_feedback' => 'The transformation has been remarkable. We\'re now attracting the right clientele—people who appreciate luxury and are willing to pay for it. Our occupancy rates are up, but more importantly, our average booking value has increased significantly. We\'re finally positioned where we should have been all along.',
                'status' => 'completed',
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 2,
                'technologies' => ['Figma', 'Webflow', 'Lightroom'],
                'poster_url' => 'https://picsum.photos/seed/limbe-hospitality/800/600',
                'process_steps' => [
                    [
                        'title' => 'Market Positioning Analysis',
                        'description' => 'Researched luxury hospitality market in Malawi, analyzed competitor positioning, and identified opportunity gaps in the boutique hotel segment.',
                    ],
                    [
                        'title' => 'Brand Repositioning Strategy',
                        'description' => 'Redefined brand values, voice, and visual identity to appeal to high-net-worth travelers. Created luxury positioning framework aligned with international standards.',
                    ],
                    [
                        'title' => 'Visual Identity Redesign',
                        'description' => 'Designed sophisticated logo, color palette, and typography system that communicated premium quality while celebrating authentic Malawian heritage.',
                    ],
                    [
                        'title' => 'Implementation & Training',
                        'description' => 'Rolled out new brand across all touchpoints including website, signage, staff uniforms, and marketing materials. Conducted staff training on luxury service standards.',
                    ],
                ],
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
                'process_steps' => [
                    [
                        'title' => 'User Research & Requirements',
                        'description' => 'Interviewed potential investors and agricultural experts to understand data visualization needs, trust factors, and decision-making criteria.',
                    ],
                    [
                        'title' => 'Information Architecture',
                        'description' => 'Mapped complex investment workflows and designed intuitive navigation structure that made agricultural data accessible to non-expert investors.',
                    ],
                    [
                        'title' => 'UI Design & Prototyping',
                        'description' => 'Created clean, professional interface with real-time data dashboards, interactive charts, and transparent reporting features to build investor confidence.',
                    ],
                    [
                        'title' => 'Development & Testing',
                        'description' => 'Built responsive platform with Vue.js frontend and integrated D3.js for dynamic data visualizations. Conducted extensive user testing with target investors.',
                    ],
                ],
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
                'process_steps' => [
                    [
                        'title' => 'Brand Strategy Workshop',
                        'description' => 'Facilitated workshops with founders to define TechHub\'s mission, values, and unique positioning in the African startup ecosystem.',
                    ],
                    [
                        'title' => 'Visual Identity Development',
                        'description' => 'Created dynamic, youthful brand identity that resonated with entrepreneurs while maintaining credibility with international partners and investors.',
                    ],
                    [
                        'title' => 'Marketing Collateral Design',
                        'description' => 'Designed comprehensive brand assets including logo system, presentation templates, social media kits, and launch event materials.',
                    ],
                    [
                        'title' => 'Launch Campaign',
                        'description' => 'Executed multi-channel launch campaign targeting startups, mentors, and investors. Created buzz through strategic partnerships and media outreach.',
                    ],
                ],
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
                'process_steps' => [
                    [
                        'title' => 'Market Research',
                        'description' => 'Studied European specialty coffee packaging trends, analyzed competitor designs, and identified what appeals to premium coffee buyers.',
                    ],
                    [
                        'title' => 'Packaging Concept Development',
                        'description' => 'Created multiple packaging concepts that highlighted Zomba\'s origin story, sustainability practices, and exceptional quality. Designed for both shelf appeal and export durability.',
                    ],
                    [
                        'title' => 'Design Refinement',
                        'description' => 'Iterated on chosen concept with client feedback, ensuring design met international food packaging standards and communicated premium positioning.',
                    ],
                    [
                        'title' => 'Production Support',
                        'description' => 'Worked with printers to ensure color accuracy and material quality. Created 3D mockups for sales presentations to potential buyers.',
                    ],
                ],
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
                'process_steps' => [
                    [
                        'title' => 'Sales Process Analysis',
                        'description' => 'Mapped existing sales journey, identified pain points where buyers lost confidence, and determined what information was needed at each stage.',
                    ],
                    [
                        'title' => 'Marketing Suite Design',
                        'description' => 'Created comprehensive suite including luxury brochures, virtual tour videos, property fact sheets, and interactive web presentations showcasing lifestyle benefits.',
                    ],
                    [
                        'title' => 'Content Production',
                        'description' => 'Produced high-quality photography and video of properties, surrounding areas, and lifestyle experiences. Crafted compelling copy emphasizing exclusivity and value.',
                    ],
                    [
                        'title' => 'Sales Team Training',
                        'description' => 'Trained sales team on using new materials effectively in presentations. Provided guidelines for customizing materials for different buyer segments.',
                    ],
                ],
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
                'process_steps' => [
                    [
                        'title' => 'Community Research',
                        'description' => 'Conducted focus groups with rural women to understand health knowledge gaps, cultural beliefs, and preferred communication channels.',
                    ],
                    [
                        'title' => 'Visual-First Campaign Design',
                        'description' => 'Designed culturally relevant imagery and simple visual messaging that transcended literacy barriers. Created materials suitable for radio, print, and mobile distribution.',
                    ],
                    [
                        'title' => 'Multi-Channel Distribution',
                        'description' => 'Partnered with local radio stations, community health workers, and mobile networks to reach women across rural Malawi through their preferred channels.',
                    ],
                    [
                        'title' => 'Impact Measurement',
                        'description' => 'Tracked campaign reach through radio listener surveys, clinic visit data, and SMS engagement metrics to measure behavior change.',
                    ],
                ],
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
                'process_steps' => [
                    [
                        'title' => 'Positioning Strategy',
                        'description' => 'Redefined Malawi Fashion Week from local fashion show to premier business platform connecting African designers with international buyers and investors.',
                    ],
                    [
                        'title' => 'Brand Refresh',
                        'description' => 'Updated visual identity to communicate sophistication and commercial focus. Created dynamic branding system adaptable across digital and physical touchpoints.',
                    ],
                    [
                        'title' => 'Buyer Outreach Program',
                        'description' => 'Developed targeted outreach to international fashion buyers, creating compelling pitch materials highlighting unique African design talent and business opportunities.',
                    ],
                    [
                        'title' => 'Event Execution',
                        'description' => 'Coordinated all event elements from venue design to runway production, ensuring professional execution that matched luxury positioning.',
                    ],
                ],
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
