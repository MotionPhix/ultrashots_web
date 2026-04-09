import type { CaseStudy, Testimonial } from '@/types/home';
import { defineStore } from 'pinia';

interface ApiResponse<T> {
    data: T;
}

export const useHomepageContentStore = defineStore('homepage-content', {
    state: () => ({
        caseStudies: [] as CaseStudy[],
        testimonials: [] as Testimonial[],
        activeCaseStudyId: null as string | null,
        mobileNavigationOpen: false,
        isRefreshing: false,
        refreshError: null as string | null,
    }),
    getters: {
        activeCaseStudy(state): CaseStudy | null {
            return state.caseStudies.find((caseStudy) => caseStudy.id === state.activeCaseStudyId) ?? null;
        },
    },
    actions: {
        bootstrap(caseStudies: CaseStudy[], testimonials: Testimonial[]): void {
            this.caseStudies = caseStudies;
            this.testimonials = testimonials;

            if (this.activeCaseStudyId === null && caseStudies.length > 0) {
                this.activeCaseStudyId = caseStudies[0].id;
            }
        },
        async refreshDynamicContent(caseStudiesUrl: string, testimonialsUrl: string): Promise<void> {
            if (this.isRefreshing) {
                return;
            }

            this.isRefreshing = true;
            this.refreshError = null;

            try {
                const [caseStudyResponse, testimonialResponse] = await Promise.all([
                    this.fetchJson<CaseStudy[]>(caseStudiesUrl),
                    this.fetchJson<Testimonial[]>(testimonialsUrl),
                ]);

                if (caseStudyResponse !== null) {
                    this.caseStudies = caseStudyResponse;
                }

                if (testimonialResponse !== null) {
                    this.testimonials = testimonialResponse;
                }

                if (this.activeCaseStudyId === null || !this.caseStudies.some(({ id }) => id === this.activeCaseStudyId)) {
                    this.activeCaseStudyId = this.caseStudies[0]?.id ?? null;
                }
            } catch {
                this.refreshError = 'Dynamic homepage content could not be refreshed.';
            } finally {
                this.isRefreshing = false;
            }
        },
        openCaseStudy(caseStudyId: string): void {
            this.activeCaseStudyId = caseStudyId;
        },
        closeCaseStudy(): void {
            this.activeCaseStudyId = null;
        },
        toggleMobileNavigation(): void {
            this.mobileNavigationOpen = !this.mobileNavigationOpen;
        },
        closeMobileNavigation(): void {
            this.mobileNavigationOpen = false;
        },
        async fetchJson<T>(url: string): Promise<T | null> {
            const response = await fetch(url, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return null;
            }

            const payload = (await response.json()) as ApiResponse<T>;

            return payload.data;
        },
    },
});