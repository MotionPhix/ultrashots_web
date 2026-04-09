export interface LandingMetric {
    label: string;
    value: string;
    detail?: string;
}

export interface HeroPanel {
    label: string;
    title: string;
    description: string;
}

export interface LandingHero {
    eyebrow: string;
    headline: string;
    tagline: string;
    rotating_words: string[];
    description: string;
    badges: string[];
    proof_points: LandingMetric[];
    primary_cta_label: string;
    primary_cta_anchor: string;
    secondary_cta_label: string;
    secondary_cta_anchor: string;
    mockup_panels: HeroPanel[];
}

export interface AboutStat {
    label: string;
    value: string;
}

export interface LandingAbout {
    title: string;
    story: string[];
    photo_alt: string;
    stats: AboutStat[];
    photo_url?: string;
    owner_name?: string;
    owner_title?: string;
    location?: string;
    design_tips?: Array<{
        icon: string;
        title: string;
        description: string;
        action: string;
    }>;
    recent_work?: {
        id: number;
        slug: string;
        title: string;
        client: string;
        category: string;
        description: string;
        poster_url?: string | null;
        metric?: {
            label: string;
            value: string;
        } | null;
        url: string;
    } | null;
}

export interface ClientLogo {
    name: string;
    tag: string;
}

export interface ServiceTier {
    name: string;
    price: string;
    description: string;
    popular?: boolean;
    deliverables: string[];
}

export interface CaseStudyImageSet {
    poster?: string | null;
    before?: string | null;
    after?: string | null;
}

export interface CaseStudySnapshot {
    title: string;
    summary: string;
}

export interface CaseStudy {
    id: string;
    slug: string;
    title: string;
    client: string;
    sector: string;
    location: string;
    year: string;
    summary: string;
    description: string;
    teaser: string;
    outcome: string;
    timeline: string;
    investment: string;
    services: string[];
    technologies: string[];
    metrics: LandingMetric[];
    before: CaseStudySnapshot;
    after: CaseStudySnapshot;
    image_set: CaseStudyImageSet;
    poster_url?: string | null;
    project_url?: string | null;
    gallery_images?: Array<{ url?: string | null }>;
    video_url?: string | null;
    video_label?: string | null;
    alt: string;
    category?: string; // Optional field for project categorization
    short_description?: string; // Optional shorter description
}

export interface Testimonial {
    id: string;
    name: string;
    role: string;
    company: string;
    location: string;
    quote: string;
    rating: number;
    avatar_initials: string;
    project_type?: string; // Optional field for case study categorization
}

export interface ProcessStep {
    title: string;
    summary: string;
    duration: string; // Flexible label (e.g., "Week 1", "2-3 weeks", "Ongoing")
}

export interface LandingCta {
    title: string;
    description: string;
    calendly_label: string;
    fallback_title: string;
    fallback_copy: string;
}

export interface FooterSocial {
    platform: string;
    label: string;
    url: string;
}

export interface LandingFooter {
    email: string;
    phone: string;
    location: string;
    newsletter_title: string;
    newsletter_copy: string;
    socials: FooterSocial[];
}

export interface LandingSeo {
    title: string;
    description: string;
    keywords: string[];
    schema_price_range: string;
}

export interface AbTestVariant {
    name: string;
    cta: string;
    angle: string;
}

export interface LandingOwner {
    name: string;
    title: string;
    location: string;
    email: string;
}

export interface LandingContent {
    hero: LandingHero;
    about: LandingAbout;
    clientLogos: ClientLogo[];
    services: ServiceTier[];
    caseStudies: CaseStudy[];
    testimonials: Testimonial[];
    process: ProcessStep[];
    cta: LandingCta;
    footer: LandingFooter;
    seo: LandingSeo;
    abTests: AbTestVariant[];
    owner: LandingOwner;
}

export interface LandingApiRoutes {
    caseStudies: string;
    testimonials: string;
}