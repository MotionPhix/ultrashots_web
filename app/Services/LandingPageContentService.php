<?php

namespace App\Services;

use App\Models\HomepageContent;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Arr;

class LandingPageContentService
{
    /**
     * Get complete landing page content with dynamic sections.
     */
    public function getContent(?User $owner = null): array
    {
        $hero = HomepageContent::getBySection('hero') ?? [];
        $about = HomepageContent::getBySection('about') ?? [];
        $services = HomepageContent::getBySection('services') ?? [];
        $process = HomepageContent::getBySection('process') ?? [];
        $cta = HomepageContent::getBySection('cta') ?? [];

        $ownerName = trim(
            collect([$owner?->first_name, $owner?->last_name])->filter()->implode(' ')
        );

        return [
            'content' => [
                'hero' => $hero,
                'about' => array_merge($about, [
                    'photo_url' => $owner?->avatar_url ?: '/assets/profile_400x400.jpg',
                    'owner_name' => $ownerName !== '' ? $ownerName : 'Kingsley Banda',
                    'owner_title' => $owner?->job_title ?: 'Brand strategist and UI systems designer',
                    'location' => $owner?->location ?: 'Lilongwe, Malawi',
                    'recent_work' => $this->randomRecentWork(), // Add random recent work
                ]),
                'clientLogos' => [],
                'services' => is_array($services) && isset($services[0]) ? $services : [],
                'caseStudies' => $this->caseStudies(),
                'testimonials' => $this->testimonials(),
                'process' => is_array($process) && isset($process[0]) ? $process : [],
                'cta' => $cta,
                'footer' => [
                    'email' => $owner?->email ?: 'hello@ultrashots.net',
                    'phone' => $owner?->phone_number ?: '+265 995 000 000',
                    'location' => $owner?->location ?: 'Lilongwe, Malawi',
                    'socials' => $this->socials($owner),
                ],
                'seo' => $this->seo($owner),
                'abTests' => [],
                'owner' => [
                    'name' => $ownerName !== '' ? $ownerName : 'Kingsley Banda',
                    'title' => $owner?->job_title ?: 'Brand strategist and UI systems designer',
                    'location' => $owner?->location ?: 'Lilongwe, Malawi',
                    'email' => $owner?->email ?: 'hello@ultrashots.net',
                ],
            ],
            'api' => [
                'caseStudies' => route('api.homepage.case-studies'),
                'testimonials' => route('api.homepage.testimonials'),
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function pagePayload(?User $owner = null): array
    {
        $ownerName = $owner
            ? trim(collect([$owner->first_name, $owner->last_name])->filter()->implode(' '))
            : 'Kingsley';

        // Get content from database with fallbacks
        $hero = HomepageContent::getBySection('hero') ?? [];
        $about = HomepageContent::getBySection('about') ?? [];
        $services = HomepageContent::getBySection('services') ?? [];
        $cta = HomepageContent::getBySection('cta') ?? [];
        $process = HomepageContent::getBySection('process') ?? [];

        return [
            'landing' => [
                'hero' => $hero,
                'about' => array_merge($about, [
                    'photo_url' => $owner?->avatar_url ?: '/assets/profile_400x400.jpg',
                    'owner_name' => $ownerName !== '' ? $ownerName : 'Kingsley Banda',
                    'owner_title' => $owner?->job_title ?: 'Brand strategist and UI systems designer',
                    'location' => $owner?->location ?: 'Lilongwe, Malawi',
                    'recent_work' => $this->randomRecentWork(), // Add random recent work
                ]),
                'clientLogos' => [],
                'services' => is_array($services) && isset($services[0]) ? $services : [],
                'caseStudies' => $this->caseStudies(),
                'testimonials' => $this->testimonials(),
                'process' => is_array($process) && isset($process[0]) ? $process : [],
                'cta' => $cta,
                'footer' => [
                    'email' => $owner?->email ?: 'hello@ultrashots.net',
                    'phone' => $owner?->phone_number ?: '+265 995 000 000',
                    'location' => $owner?->location ?: 'Lilongwe, Malawi',
                    'socials' => $this->socials($owner),
                ],
                'seo' => $this->seo($owner),
                'abTests' => [],
                'owner' => [
                    'name' => $ownerName !== '' ? $ownerName : 'Kingsley Banda',
                    'title' => $owner?->job_title ?: 'Brand strategist and UI systems designer',
                    'location' => $owner?->location ?: 'Lilongwe, Malawi',
                    'email' => $owner?->email ?: 'hello@ultrashots.net',
                ],
            ],
            'api' => [
                'caseStudies' => route('api.homepage.case-studies'),
                'testimonials' => route('api.homepage.testimonials'),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function caseStudies(): array
    {
        $featuredProjects = $this->featuredProjects();

        return collect($featuredProjects)
            ->take(8)
            ->map(function (Project $project): array {
                $customerName = $project->customer?->company_name ?: trim(
                    collect([$project->customer?->first_name, $project->customer?->last_name])->filter()->implode(' ')
                );

                $galleryImages = collect($project->gallery_images ?? [])->values()->all();

                return [
                    'id' => $project->uuid,
                    'slug' => $project->slug,
                    'title' => $project->name,
                    'client' => $customerName ?: 'Confidential Client',
                    'sector' => $project->category ?: 'Branding',
                    'location' => 'Malawi',
                    'year' => $project->created_at?->format('Y') ?? date('Y'),
                    'summary' => $project->short_description,
                    'description' => $project->description,
                    'teaser' => $project->short_description,
                    'outcome' => 'Improved brand perception and conversion rates',
                    'timeline' => '4-6 weeks',
                    'investment' => 'Custom quote',
                    'services' => ['Brand Strategy', 'UI Design', 'Web Development'],
                    'technologies' => $project->main_technologies ?? ['Figma', 'Vue', 'Laravel'],
                    'metrics' => $this->extractMetrics($project->results),
                    'poster_url' => $project->poster_url,
                    'image_set' => [
                        'poster' => $project->poster_url,
                        'before' => Arr::get($galleryImages, '0.url'),
                        'after' => Arr::get($galleryImages, '1.url') ?: $project->poster_url,
                    ],
                    'gallery_images' => $galleryImages,
                    'project_url' => route('projects.show', $project),
                    'alt' => "{$project->name} - Brand identity and UI design case study",
                ];
            })
            ->all();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function testimonials(): array
    {
        return HomepageContent::getBySection('testimonials') ?? [];
    }

    /**
     * @return array<int, Project>
     */
    private function featuredProjects(): array
    {
        return Project::query()
            ->with(['customer:id,first_name,last_name,company_name', 'media'])
            ->select([
                'id',
                'uuid',
                'name',
                'slug',
                'description',
                'short_description',
                'production_type',
                'category',
                'status',
                'technologies',
                'is_featured',
                'is_public',
                'customer_id',
                'poster_url',
                'results',
                'created_at',
            ])
            ->public()
            ->completed()
            ->featured()
            ->orderBy('sort_order')
            ->take(8)
            ->get()
            ->all();
    }

    /**
     * Get a random recent completed project for About section showcase.
     *
     * @return array<string, mixed>|null
     */
    private function randomRecentWork(): ?array
    {
        $project = Project::query()
            ->with(['customer:id,first_name,last_name,company_name'])
            ->select([
                'id',
                'uuid',
                'name',
                'slug',
                'short_description',
                'production_type',
                'category',
                'poster_url',
                'results',
                'created_at',
                'customer_id',
            ])
            ->public()
            ->completed()
            ->inRandomOrder() // Random selection each page load
            ->first();

        if (! $project) {
            return null;
        }

        $customerName = $project->customer?->company_name ?: trim(
            collect([$project->customer?->first_name, $project->customer?->last_name])->filter()->implode(' ')
        );

        // Extract first metric if available
        $metrics = $this->extractMetrics($project->results);
        $primaryMetric = $metrics[0] ?? null;

        return [
            'id' => $project->id,
            'slug' => $project->slug,
            'title' => $project->name,
            'client' => $customerName,
            'category' => $project->category ?: $project->production_type,
            'description' => $project->short_description,
            'poster_url' => $project->poster_url,
            'metric' => $primaryMetric,
            'url' => route('projects.show', $project),
        ];
    }

    /**
     * Extract metrics from project results HTML string.
     *
     * Parses patterns like: <h2>+47%</h2><p>Donor engagement rate</p>
     *
     * @return array<int, array<string, string>>
     */
    private function extractMetrics(?string $results): array
    {
        if (blank($results)) {
            return [
                ['label' => 'Engagement lift', 'value' => '+40%'],
                ['label' => 'Conversion rate', 'value' => '+25%'],
            ];
        }

        preg_match_all('/<h[1-3][^>]*>(.*?)<\/h[1-3]>\s*<p[^>]*>(.*?)<\/p>/is', $results, $matches);

        if (empty($matches[1])) {
            return [
                ['label' => 'Engagement lift', 'value' => '+40%'],
                ['label' => 'Conversion rate', 'value' => '+25%'],
            ];
        }

        return collect($matches[1])
            ->take(3)
            ->map(fn (string $value, int $i) => [
                'value' => strip_tags($value),
                'label' => strip_tags($matches[2][$i] ?? 'Improvement'),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function socials(?User $owner): array
    {
        $ownerSocials = $owner?->socials ?? [];

        $defaultSocials = [
            ['platform' => 'instagram', 'label' => 'Instagram', 'url' => 'https://instagram.com/ultrashots'],
            ['platform' => 'linkedin', 'label' => 'LinkedIn', 'url' => 'https://linkedin.com/in/kingsleybanda'],
            ['platform' => 'behance', 'label' => 'Behance', 'url' => 'https://behance.net/ultrashots'],
        ];

        return array_map(function (array $social) use ($ownerSocials): array {
            $platform = Arr::get($social, 'platform');

            if ($platform && isset($ownerSocials[$platform])) {
                return array_merge($social, ['url' => (string) $ownerSocials[$platform]]);
            }

            return $social;
        }, $defaultSocials);
    }

    /**
     * @return array<string, mixed>
     */
    private function seo(?User $owner): array
    {
        $ownerName = $owner
            ? trim(collect([$owner->first_name, $owner->last_name])->filter()->implode(' '))
            : 'Kingsley Banda';

        return [
            'title' => "{$ownerName} | Brand Strategist & UI Architect in Malawi",
            'description' => 'Premium branding, UI systems, and conversion-focused design for ambitious businesses in Lilongwe and beyond. Transform your brand from invisible to irresistible.',
            'keywords' => [
                'branding strategist Malawi',
                'graphic design Lilongwe',
                'UI UX designer Malawi',
                'brand identity designer Malawi',
                'premium branding for SMEs Malawi',
                'conversion-focused web design',
            ],
        ];
    }
}
