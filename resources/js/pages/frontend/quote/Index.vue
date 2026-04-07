<script setup lang="ts">
import { computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import Heading from '@/components/Heading.vue';
import { Field, FieldDescription, FieldGroup, FieldLabel } from '@/components/ui/field';
import { ArrowRight } from 'lucide-vue-next';

const props = defineProps<{
  projectTypes: Record<string, string>;
  budgetRanges: Record<string, string>;
  timelines: Record<string, string>;
  quotation?: any;
  quote_id?: number;
}>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    project_type: 'web_development',
    budget_range: '',
    timeline: 'standard',
    description: '',
    goals: '',
    target_audience: '',
    additional_info: '',
    reference_files: [] as File[],
});

const showQuotation = computed(() => props.quotation !== undefined && props.quotation !== null);

const isFormValid = computed(() => {
    return (
        form.name &&
        form.email &&
        form.project_type &&
        form.timeline &&
        form.description &&
        form.description.length >= 20
    );
});

const submitForm = () => {
    if (!isFormValid.value) return;

    form.post('/quote', {
        onSuccess: () => {
            form.reset();
        },
    });
};

const handleFileInput = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files) {
        form.reference_files = Array.from(input.files);
    }
};

const removeFile = (index: number) => {
    form.reference_files.splice(index, 1);
};

defineOptions({ layout: AppLayout, inheritAttrs: false });
</script>

<template>

    <Head title="Request a Quote" />

    <div class="min-h-screen bg-background">
        <section class="py-16">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-12">
                    <Heading title="Request a Quote"
                        description="Tell us about your project and we'll provide a detailed quotation" />
                </div>

                <!-- Form -->
                <FieldGroup v-if="!showQuotation" class="bg-card rounded-lg border p-8">
                    <!-- Name -->
                    <Field>
                        <FieldLabel for="name">Full Name *</FieldLabel>
                        <Input id="name" v-model="form.name" type="text" placeholder="Your name" required />
                    </Field>

                    <!-- Email -->
                    <Field>
                        <FieldLabel for="email">Email Address *</FieldLabel>
                        <Input id="email" v-model="form.email" type="email" placeholder="your@email.com" required />
                    </Field>

                    <!-- Phone & Company (2 columns on desktop) -->
                    <FieldGroup class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field>
                            <FieldLabel for="phone">Phone Number</FieldLabel>
                            <Input id="phone" v-model="form.phone" type="tel" placeholder="Your phone number" />
                        </Field>

                        <Field>
                            <FieldLabel for="company">Company Name</FieldLabel>
                            <Input id="company" v-model="form.company" type="text" placeholder="Your company" />
                        </Field>
                    </FieldGroup>

                    <!-- Project Type & Budget (2 columns on desktop) -->
                    <FieldGroup class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <Field>
                            <FieldLabel for="project_type">Project Type *</FieldLabel>
                            <Select v-model="form.project_type">
                                <SelectTrigger id="project_type">
                                    <SelectValue placeholder="Select project type" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(label, value) in projectTypes" :key="value" :value="value">
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </Field>

                        <Field>
                            <FieldLabel for="budget_range">Budget Range</FieldLabel>
                            <Select v-model="form.budget_range">
                                <SelectTrigger id="budget_range">
                                    <SelectValue placeholder="Select budget range" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem v-for="(label, value) in budgetRanges" :key="value" :value="value">
                                        {{ label }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </Field>
                    </FieldGroup>

                    <!-- Timeline -->
                    <Field>
                        <FieldLabel for="timeline">Timeline *</FieldLabel>
                        <Select v-model="form.timeline">
                            <SelectTrigger id="timeline">
                                <SelectValue placeholder="Select timeline" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem v-for="(label, value) in timelines" :key="value" :value="value">
                                    {{ label }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </Field>

                    <!-- Description -->
                    <Field>
                        <FieldLabel for="description">Project Description *</FieldLabel>
                        <textarea id="description" v-model="form.description"
                            placeholder="Tell us about your project, what problems it solves, and your vision..."
                            class="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 rounded-md min-h-32"
                            required maxlength="2000" />
                        <FieldDescription class="text-xs">
                            {{ form.description.length }}/2000 characters
                        </FieldDescription>
                    </Field>

                    <!-- Goals & Target Audience -->
                    <Field>
                        <FieldLabel for="goals">Project Goals</FieldLabel>
                        <textarea id="goals" v-model="form.goals"
                            placeholder="What are your main goals for this project?"
                            class="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 rounded-md min-h-24" />
                    </Field>

                    <Field>
                        <FieldLabel for="target_audience">Target Audience</FieldLabel>
                        <Input id="target_audience" v-model="form.target_audience" type="text"
                            placeholder="Who will be using this?" />
                    </Field>

                    <!-- Additional Info -->
                    <Field>
                        <FieldLabel for="additional_info">Additional Information</FieldLabel>
                        <textarea id="additional_info" v-model="form.additional_info"
                            placeholder="Any other details we should know?"
                            class="w-full px-3 py-2 border border-input bg-background text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 rounded-md min-h-24" />
                    </Field>

                    <!-- Reference Materials -->
                    <Field>
                        <FieldLabel for="reference_files">Reference Materials (Optional)</FieldLabel>
                        <div
                            class="border-2 border-dashed border-muted-foreground rounded-lg p-6 text-center cursor-pointer hover:border-primary transition-colors">
                            <input id="reference_files" type="file" multiple accept="image/*,.pdf"
                                @change="handleFileInput" class="hidden" />
                            <label for="reference_files" class="cursor-pointer block">
                                <div class="space-y-2">
                                    <div class="text-3xl">📎</div>
                                    <div class="font-semibold">Click to upload or drag and drop</div>
                                    <p class="text-sm text-muted-foreground">PNG, JPG, GIF, WebP or PDF (Max 10MB per
                                        file)</p>
                                </div>
                            </label>
                        </div>

                        <FieldDescription class="text-xs">
                            Upload logos, inspirations, wireframes, or other reference materials
                        </FieldDescription>

                        <!-- Uploaded Files List -->
                        <div v-if="form.reference_files.length > 0" class="space-y-2">
                            <p class="text-sm font-semibold">{{ form.reference_files.length }} file(s) selected:</p>
                            <div class="space-y-1">
                                <div v-for="(file, index) in form.reference_files" :key="index"
                                    class="flex items-center justify-between p-2 bg-muted rounded text-sm">
                                    <span>{{ file.name }}</span>
                                    <button type="button" @click="removeFile(index)"
                                        class="text-destructive hover:text-destructive/80">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        </div>
                    </Field>

                    <div class="flex">
                        <!-- Submit Button -->
                        <Button @click="submitForm" :disabled="!isFormValid || form.processing" class="h-12 ml-auto">
                            {{ form.processing ? 'Generating Quote...' : 'Get Quote Estimate' }}
                        </Button>
                    </div>
                </FieldGroup>

                <!-- Quotation Result -->
                <div v-if="showQuotation" class="space-y-8">
                    <div
                        class="bg-green-50 dark:bg-green-950/30 border border-green-200 dark:border-green-900 rounded-lg p-6">
                        <h2 class="text-2xl font-bold text-green-900 dark:text-green-100 mb-2">
                            Quote Generated!
                        </h2>
                        <p class="text-green-700 dark:text-green-200">
                            We've created a quotation based on your project details. Check the breakdown below.
                        </p>
                    </div>

                    <!-- Quotation Summary -->
                    <div class="bg-card border rounded-lg p-8 space-y-6">
                        <div>
                            <h3 class="text-sm font-semibold text-muted-foreground mb-4">
                                QUOTATION SUMMARY
                            </h3>

                            <!-- Price Breakdown -->
                            <div class="space-y-3 mb-6 pb-6 border-b">
                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">Labor Cost</span>
                                    <span class="font-semibold">
                                        ${{ props.quotation.labor_cost.toLocaleString() }}
                                    </span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">
                                        Features & Complexity
                                    </span>

                                    <span class="font-semibold">
                                        ${{ props.quotation.additional_cost.toLocaleString() }}
                                    </span>
                                </div>

                                <div class="flex justify-between">
                                    <span class="text-muted-foreground">
                                        Contingency (15%)
                                    </span>

                                    <span class="font-semibold">
                                        ${{ props.quotation.contingency.toLocaleString() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Total Price -->
                            <div class="flex justify-between items-end mb-8">
                                <span class="text-lg font-semibold">Total Estimate</span>
                                <div class="text-right">
                                    <div class="text-4xl font-bold text-primary">
                                        ${{ props.quotation.total_price.toLocaleString() }}
                                    </div>
                                    <p class="text-sm text-muted-foreground mt-1">
                                        {{ props.quotation.timeline_estimate.min_weeks }}-{{
                                          props.quotation.timeline_estimate.max_weeks
                                        }}
                                        weeks
                                    </p>
                                </div>
                            </div>

                            <!-- Timeline Details -->
                            <div class="bg-muted/50 rounded p-4">
                                <h4 class="font-semibold mb-3">Timeline Estimate</h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span>Estimated Duration</span>
                                        <span>{{ props.quotation.timeline_estimate.estimate_weeks }} weeks</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Optimistic</span>
                                        <span>{{ props.quotation.timeline_estimate.min_weeks }} weeks</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span>Conservative</span>
                                        <span>{{ props.quotation.timeline_estimate.max_weeks }} weeks</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div
                        class="bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-900 rounded-lg p-6">
                        <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">
                            What Happens Next?
                        </h3>
                        <ul class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                            <li class="flex gap-2">
                                <span>✓</span>
                                <span>We'll review your request within 24 hours</span>
                            </li>
                            <li class="flex gap-2">
                                <span>✓</span>
                                <span>You'll receive a detailed proposal via email</span>
                            </li>
                            <li class="flex gap-2">
                                <span>✓</span>
                                <span>We can schedule a call to discuss your project</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex gap-4">
                        <Button @click="form.reset()" variant="outline" class="h-11 ml-auto">
                            Another Quote
                        </Button>

                        <Link 
                          :as="Button" 
                          href="/get-in-touch" class="h-11">
                          More Info <ArrowRight />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </div>
</template>
