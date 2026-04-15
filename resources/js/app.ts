import { putConfig, withInertiaModal } from '@inertiaui/modal-vue';
import '../css/app.css';

import { initializeTheme } from '@/composables/useAppearance';
import AdminLayout from '@/layouts/AdminLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { configureEcho } from '@laravel/echo-vue';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import type { DefineComponent } from 'vue';

configureEcho({
    broadcaster: 'pusher',
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

putConfig({
  type: 'modal',
  navigate: false,
  useNativeDialog: true,
  appElement: '#app',
  modal: {
    closeButton: false,
    closeExplicitly: false,
    closeOnClickOutside: false,
    maxWidth: 'xl',
    paddingClasses: '0',
    panelClasses: '',
    position: 'center',
  },
  slideover: {
    closeButton: true,
    closeExplicitly: false,
    closeOnClickOutside: false,
    maxWidth: 'sm',
    paddingClasses: '0',
    position: 'left',
  },
})


createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;
            case name.startsWith('auth/'):
                return AuthLayout;
            case name.startsWith('settings/'):
                return [AdminLayout, SettingsLayout];
        }
    },
    withApp(app) {
        app.use(createPinia());
        withInertiaModal(app);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
