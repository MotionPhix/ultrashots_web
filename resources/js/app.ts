import './bootstrap';
import './echo'
import 'lenis/dist/lenis.css'
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import Lenis from 'lenis'

import { createPinia } from 'pinia'

import { renderApp } from '@inertiaui/modal-vue';

// ApexCharts
import VueApexCharts from 'vue3-apexcharts';
import { configureEcho } from '@laravel/echo-vue';

configureEcho({
    broadcaster: 'pusher',
});

const appName = import.meta.env.VITE_APP_NAME || 'Ultrahots';

const lenis = new Lenis()

lenis.on('scroll', (e) => {})

function raf(time: any) {
  lenis.raf(time)
  requestAnimationFrame(raf)
}

requestAnimationFrame(raf)

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob<DefineComponent>('./pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    createApp({ render: renderApp(App, props) }) // () => h(ModalRoot, () => h(App, props))
      .use(plugin)
      .use(ZiggyVue)
      .use(createPinia())
      .component('VueApexCharts', VueApexCharts)
      .mount(el);
  },
  progress: {
    color: '#4B5563',
  },
});
