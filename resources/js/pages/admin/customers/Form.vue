<script setup lang="ts">
import { create as createRoute, index, store, update } from '@/routes/admin/customers';
import AdminLayout from '@/layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

interface CustomerFormData {
    uuid?: string;
    first_name?: string | null;
    last_name?: string | null;
    company_name?: string | null;
    email?: string | null;
    phone_number?: string | null;
    contact_person?: string | null;
    address?: string | null;
    city?: string | null;
    state?: string | null;
    zip_code?: string | null;
    country?: string | null;
    status?: string;
    notes?: string | null;
}

const props = defineProps<{
    customer: CustomerFormData | null;
    statuses: string[];
}>();

const form = useForm({
    first_name: props.customer?.first_name ?? '',
    last_name: props.customer?.last_name ?? '',
    company_name: props.customer?.company_name ?? '',
    email: props.customer?.email ?? '',
    phone_number: props.customer?.phone_number ?? '',
    contact_person: props.customer?.contact_person ?? '',
    address: props.customer?.address ?? '',
    city: props.customer?.city ?? '',
    state: props.customer?.state ?? '',
    zip_code: props.customer?.zip_code ?? '',
    country: props.customer?.country ?? '',
    status: props.customer?.status ?? 'active',
    notes: props.customer?.notes ?? '',
});

const submit = () => {
    if (props.customer?.uuid) {
        form.patch(update.url(props.customer.uuid), {
            preserveScroll: true,
        });

        return;
    }

    form.post(store.url(), {
        preserveScroll: true,
    });
};

defineOptions({ layout: AdminLayout });
</script>

<template>
    <Head :title="customer ? 'Edit Customer' : 'New Customer'" />

    <div class="flex flex-col gap-6 p-4">
        <section class="rounded-3xl border border-sidebar-border/70 bg-background p-6 dark:border-sidebar-border">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <p class="text-sm uppercase tracking-[0.3em] text-muted-foreground">Admin</p>
                    <h1 class="mt-2 text-3xl font-semibold">{{ customer ? 'Edit customer' : 'Create customer' }}</h1>
                </div>

                <Link :href="index.url()" class="rounded-full border border-input px-4 py-2 text-sm font-medium">Back to customers</Link>
            </div>
        </section>

        <form class="grid gap-6" @submit.prevent="submit">
            <section class="grid gap-6 rounded-3xl border border-sidebar-border/70 bg-card p-6 dark:border-sidebar-border lg:grid-cols-2">
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">First name</span>
                    <input v-model="form.first_name" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                    <span v-if="form.errors.first_name" class="text-xs text-red-600">{{ form.errors.first_name }}</span>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Last name</span>
                    <input v-model="form.last_name" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Company</span>
                    <input v-model="form.company_name" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Email</span>
                    <input v-model="form.email" type="email" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                    <span v-if="form.errors.email" class="text-xs text-red-600">{{ form.errors.email }}</span>
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Phone number</span>
                    <input v-model="form.phone_number" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Contact person</span>
                    <input v-model="form.contact_person" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Address</span>
                    <textarea v-model="form.address" rows="3" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">City</span>
                    <input v-model="form.city" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">State</span>
                    <input v-model="form.state" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">ZIP code</span>
                    <input v-model="form.zip_code" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm">
                    <span class="font-medium">Country</span>
                    <input v-model="form.country" type="text" class="rounded-2xl border border-input bg-background px-4 py-2.5" >
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Status</span>
                    <select v-model="form.status" class="rounded-2xl border border-input bg-background px-4 py-2.5">
                        <option v-for="status in statuses" :key="status" :value="status">
                            {{ status.replace('_', ' ') }}
                        </option>
                    </select>
                </label>
                <label class="grid gap-2 text-sm lg:col-span-2">
                    <span class="font-medium">Notes</span>
                    <textarea v-model="form.notes" rows="5" class="rounded-2xl border border-input bg-background px-4 py-2.5" />
                </label>
            </section>

            <div class="flex justify-end gap-3">
                <Link :href="props.customer?.uuid ? createRoute.url() : index.url()" class="rounded-full border border-input px-4 py-2.5 text-sm font-medium">
                    {{ customer ? 'Create new instead' : 'Cancel' }}
                </Link>
                <button type="submit" :disabled="form.processing" class="rounded-full bg-foreground px-5 py-2.5 text-sm font-medium text-background disabled:opacity-60">
                    {{ form.processing ? 'Saving...' : customer ? 'Save customer' : 'Create customer' }}
                </button>
            </div>
        </form>
    </div>
</template>