<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Field, FieldGroup } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import AuthBase from '@/layouts/AuthLayout.vue';
import { login } from '@/routes';
import { store } from '@/routes/register';
import { Form, Head } from '@inertiajs/vue3';
</script>

<template>
    <AuthBase
        title="Create an account"
        description="Enter your details below to create your account"
    >
        <Head title="Register" />

        <Form
            v-bind="store.form()"
            :reset-on-success="['password', 'password_confirmation']"
            v-slot="{ errors, processing }"
            class="flex flex-col gap-6"
        >
            <FieldGroup>
                <FieldGroup class="grid grid-cols-2">
                    <Field>
                        <Label for="first_name">First Name</Label>
                        <Input
                            id="first_name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="first_name"
                            name="first_name"
                            placeholder="First name"
                        />
                        <InputError :message="errors.first_name" />
                    </Field>

                    <Field>
                        <Label for="last_name">Last Name</Label>
                        <Input
                            id="last_name"
                            type="text"
                            required
                            autofocus
                            :tabindex="1"
                            autocomplete="last_name"
                            name="last_name"
                            placeholder="Last name"
                        />
                        <InputError :message="errors.last_name" />
                    </Field>
                </FieldGroup>

                <Field>
                    <Label for="email">Email address</Label>
                    <Input
                        id="email"
                        type="email"
                        required
                        :tabindex="2"
                        autocomplete="email"
                        name="email"
                        placeholder="email@example.com"
                    />
                    <InputError :message="errors.email" />
                </Field>

                <Field>
                    <Label for="password">Password</Label>
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        name="password"
                        placeholder="Password"
                    />
                    <InputError :message="errors.password" />
                </Field>

                <Field>
                    <Label for="password_confirmation">Confirm password</Label>
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        name="password_confirmation"
                        placeholder="Confirm password"
                    />
                    <InputError :message="errors.password_confirmation" />
                </Field>

                <div class="mt-2 flex items-center">
                    <div class="text-center text-sm text-muted-foreground">
                        Already have an account?
                        <TextLink
                            :href="login()"
                            class="underline underline-offset-4"
                            :tabindex="6">
                            Log in
                        </TextLink>
                    </div>

                    <Button
                        type="submit"
                        class="ml-auto h-10"
                        tabindex="5"
                        :disabled="processing"
                        data-test="register-user-button">
                        <Spinner v-if="processing" />
                        {{ processing ? 'Creating...' : 'Create account' }}
                    </Button>
                </div>
            </FieldGroup>
        </Form>
    </AuthBase>
</template>
