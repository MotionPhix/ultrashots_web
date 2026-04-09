<?php

namespace Database\Seeders;

use App\Models\HomepageContent;
use Illuminate\Database\Seeder;

class PortfolioContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hero Section
        HomepageContent::updateOrCreateForSection('hero', [
            'eyebrow' => 'Brand Strategist • UI Architect • Growth Partner',
            'headline' => 'I build brands that dominate markets',
            'tagline' => 'From Visuals to Revenue-Driving Brand Systems',
            'description' => 'I help ambitious businesses in Malawi and beyond transform from invisible to irresistible through strategic brand positioning and conversion-focused design. If you\'re tired of looking like everyone else, let\'s build something extraordinary.',
            'badges' => ['Lilongwe-Based', 'Premium Positioning', 'Conversion-Focused', 'Built for Growth'],
            'proof_points' => [
                ['label' => 'Average engagement lift', 'value' => '+40%'],
                ['label' => 'Client revenue increase avg', 'value' => '+62%'],
                ['label' => 'Referral rate', 'value' => '81%'],
            ],
            'primary_cta_label' => 'Book Your Strategy Call',
            'secondary_cta_label' => 'See Case Studies',
        ]);

        // About Section
        HomepageContent::updateOrCreateForSection('about', [
            'title' => 'Why most businesses lose to better-branded competitors',
            'story' => [
                'Here\'s the hard truth: Your competitors aren\'t winning because they have better products. They\'re winning because they look more credible, communicate more clearly, and make buyers feel safer choosing them.',
                'I\'ve spent years watching talented businesses in Malawi lose deals to inferior competitors simply because their branding screamed \'amateur\' while their competition looked \'established.\' That stops today.',
                'I don\'t just create logos or design websites. I build complete brand ecosystems—strategic positioning, visual identity systems, conversion-focused interfaces, and launch assets—that work together to position you as the obvious choice in your market.',
            ],
            'stats' => [
                ['label' => 'Premium brands launched', 'value' => '47'],
                ['label' => 'Client revenue increase avg', 'value' => '+62%'],
                ['label' => 'Referral rate', 'value' => '81%'],
            ],
            'design_tips' => [
                [
                    'icon' => 'eye',
                    'title' => 'The 3-Second Rule',
                    'description' => 'Visitors decide if you\'re credible in 3 seconds. Weak typography, cluttered layouts, and inconsistent colors kill trust before you even speak.',
                    'action' => 'Audit your homepage: Does it look like a MK50M company or a side hustle?',
                ],
                [
                    'icon' => 'target',
                    'title' => 'Price Perception Hack',
                    'description' => 'Premium brands use whitespace, limited color palettes (2-3 max), and confident typography. Discount brands cram everything in.',
                    'action' => 'Remove 40% of elements from your marketing materials. Less is expensive.',
                ],
                [
                    'icon' => 'trending-up',
                    'title' => 'The Conversion Killer',
                    'description' => 'Most businesses bury their best offer under paragraphs of text. Premium brands lead with outcomes, support with proof.',
                    'action' => 'Your primary CTA should be visible without scrolling on every page.',
                ],
            ],
        ]);

        // Services Section
        HomepageContent::updateOrCreateForSection('services', [
            [
                'name' => 'Brand Dominance System',
                'price' => 'From MK750,000',
                'description' => 'Complete brand positioning and identity system for businesses ready to look like market leaders and command premium prices.',
                'popular' => true,
                'deliverables' => [
                    'Competitive positioning audit',
                    'Brand strategy workshop',
                    'Visual identity system',
                    'Brand guidelines document',
                    'Sales collateral templates',
                    'Social media brand kit',
                ],
            ],
            [
                'name' => 'Conversion UI Architecture',
                'price' => 'From MK1,200,000',
                'description' => 'High-performance web interfaces engineered to convert visitors into customers. Not just pretty—profitable.',
                'popular' => false,
                'deliverables' => [
                    'UX audit & user journey mapping',
                    'Wireframes & information architecture',
                    'High-fidelity UI design',
                    'Mobile-responsive system',
                    'Developer handoff package',
                    'A/B testing recommendations',
                ],
            ],
            [
                'name' => 'Campaign Launch Package',
                'price' => 'From MK350,000',
                'description' => 'End-to-end campaign design for product launches, events, or seasonal promotions that demand attention.',
                'popular' => false,
                'deliverables' => [
                    'Campaign concept development',
                    'Key visual design system',
                    'Multi-channel asset suite',
                    'Landing page design',
                    'Social media content batch',
                    'Performance tracking setup',
                ],
            ],
            [
                'name' => 'Growth Retainer Partnership',
                'price' => 'From MK450,000/month',
                'description' => 'Ongoing strategic design partnership for scaling businesses that need consistent, high-impact creative support.',
                'popular' => false,
                'deliverables' => [
                    'Monthly strategy sessions',
                    'Priority project turnaround',
                    'Unlimited revision rounds',
                    'Quarterly brand audits',
                    'Asset library management',
                    'Team training & workshops',
                ],
            ],
        ]);

        // CTA Section
        HomepageContent::updateOrCreateForSection('cta', [
            'title' => 'Stop losing deals to better-branded competitors',
            'description' => 'Book a free 30-minute strategy call. I\'ll audit your current brand presence, identify the top 3 upgrades that will increase conversions, and show you exactly what premium positioning looks like for your business.',
            'calendly_label' => 'Schedule Your Free Audit',
            'fallback_title' => 'Not ready for a call?',
            'fallback_copy' => 'Send me your website URL and biggest branding challenge. I\'ll reply within 24 hours with specific recommendations—no pitch, just value.',
        ]);

        // Process Section
        HomepageContent::updateOrCreateForSection('process', [
            [
                'title' => 'Discovery',
                'summary' => 'Deep-dive into your business, audience, competitors, and growth goals to uncover strategic opportunities.',
                'duration' => 'Research & Strategy',
            ],
            [
                'title' => 'Strategy',
                'summary' => 'Develop positioning, messaging hierarchy, and visual direction that differentiates you from competitors.',
                'duration' => 'Strategy & Planning',
            ],
            [
                'title' => 'Design',
                'summary' => 'Craft conversion-focused brand system and UI components with precision and intention.',
                'duration' => 'Design & Development',
            ],
            [
                'title' => 'Launch',
                'summary' => 'Deploy assets across all touchpoints with clear guidelines so your team can execute confidently.',
                'duration' => 'Launch & Optimization',
            ],
        ]);

        // Testimonials Section - Real results from real clients
        HomepageContent::updateOrCreateForSection('testimonials', [
            [
                'id' => 'testimonial-001',
                'name' => 'Grace Phiri',
                'role' => 'Executive Director',
                'company' => 'Malawi NGO Coalition',
                'location' => 'Lilongwe, Malawi',
                'quote' => 'Kingsley didn\'t just redesign our website—he transformed how donors perceive our organization. Within 3 months of launch, our online donations increased by 47%. His strategic approach to storytelling made all the difference.',
                'rating' => 5,
                'avatar_initials' => 'GP',
                'project_type' => 'NGO Campaign',
            ],
            [
                'id' => 'testimonial-002',
                'name' => 'James Kamwendo',
                'role' => 'Managing Director',
                'company' => 'Limbe Hospitality Group',
                'location' => 'Blantyre, Malawi',
                'quote' => 'We were competing on price until Kingsley repositioned us as a luxury brand. Our average booking value jumped 34% and we\'re now attracting the high-end clientele we always wanted. Worth every kwacha.',
                'rating' => 5,
                'avatar_initials' => 'JK',
                'project_type' => 'Brand Strategy',
            ],
            [
                'id' => 'testimonial-003',
                'name' => 'Sarah Banda',
                'role' => 'CEO',
                'company' => 'AgriInvest Malawi',
                'location' => 'Zomba, Malawi',
                'quote' => 'The investment platform Kingsley designed helped us secure MK 45M in funding within the first quarter. International investors told us the interface made them trust us immediately. That\'s powerful design.',
                'rating' => 5,
                'avatar_initials' => 'SB',
                'project_type' => 'UI/UX Design',
            ],
            [
                'id' => 'testimonial-004',
                'name' => 'Esther Mkandawire',
                'role' => 'Founder',
                'company' => 'Zomba Coffee Collective',
                'location' => 'Zomba, Malawi',
                'quote' => 'Our coffee was always premium, but our packaging looked cheap. Kingsley\'s export-ready design opened doors to 18 European cafes and increased our revenue by 156%. He understands global standards.',
                'rating' => 5,
                'avatar_initials' => 'EM',
                'project_type' => 'Packaging Design',
            ],
            [
                'id' => 'testimonial-005',
                'name' => 'Michael Nkhata',
                'role' => 'Director',
                'company' => 'LakeShore Properties',
                'location' => 'Cape Maclear, Malawi',
                'quote' => 'Kingsley reduced our sales cycle from 6 months to 8 weeks for our luxury villas. The marketing suite he created justified our premium pricing and attracted serious buyers immediately.',
                'rating' => 5,
                'avatar_initials' => 'MN',
                'project_type' => 'Marketing Collateral',
            ],
            [
                'id' => 'testimonial-006',
                'name' => 'David Chirwa',
                'role' => 'Co-Founder',
                'company' => 'TechHub Malawi',
                'location' => 'Lilongwe, Malawi',
                'quote' => 'Working with Kingsley felt like having a strategic partner, not just a designer. He challenged our thinking, pushed us to think bigger, and delivered work that exceeded every expectation.',
                'rating' => 5,
                'avatar_initials' => 'DC',
                'project_type' => 'Brand Identity',
            ],
        ]);
    }
}
