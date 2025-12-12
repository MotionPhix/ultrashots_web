<script setup lang="ts">
import {
  IconFingerprint,
  IconDeviceGamepad2,
  IconLayoutCards,
  IconMovie,
} from '@tabler/icons-vue';
import { onMounted, ref } from 'vue';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

const expertiseRef = ref(null);

onMounted(() => {
  const expertiseSection = expertiseRef.value as any;

  // Animate the main expertise section
  gsap.from(expertiseSection, {
    opacity: 0,
    y: 50,
    duration: 0.8,
    scrollTrigger: {
      trigger: expertiseSection,
      start: 'top bottom-=100',
      toggleActions: 'play none none reverse'
    }
  });

  // Animate each expertise item
  gsap.utils.toArray(expertiseSection?.querySelectorAll('.expertise-item')).forEach((item: any, index) => {
    const icon = item.querySelector('.icon-container');
    const content = item.querySelector('.content');

    // Animate icon
    gsap.from(icon, {
      opacity: 0,
      scale: 0.5,
      duration: 0.6,
      delay: index * 0.2,
      scrollTrigger: {
        trigger: item,
        start: 'top bottom-=50',
        toggleActions: 'play none none reverse'
      }
    });

    // Animate content
    gsap.from(content, {
      opacity: 0,
      x: 30,
      duration: 0.6,
      delay: index * 0.2 + 0.2,
      scrollTrigger: {
        trigger: item,
        start: 'top bottom-=50',
        toggleActions: 'play none none reverse'
      }
    });
  });

  // Animate list items in the first expertise area
  const listItems = expertiseSection.querySelectorAll('.expertise-list li');

  gsap.from(listItems, {
    opacity: 0,
    y: 20,
    duration: 0.4,
    stagger: 0.1,
    scrollTrigger: {
      trigger: listItems[0],
      start: 'top bottom-=30',
      toggleActions: 'play none none reverse'
    }
  });
});
</script>

<template>
<div class="mt-10 sm:mt-14" ref="expertiseRef">

  <h2 class="mb-5 uppercase font-medium text-gray-800 dark:text-neutral-200">
    Area of Expertise
  </h2>

  <!-- Timeline -->
  <div>
    <!-- Item -->
    <div class="relative flex group gap-x-5 expertise-item">

      <!-- icon -->
      <div
        class="icon-container relative group-last:after:hidden after:absolute after:top-8 after:bottom-2 after:start-3 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">

        <div class="relative z-10 flex items-center justify-center size-6">

          <IconLayoutCards
            class="text-gray-600 shrink-0 size-6 dark:text-neutral-400" />

        </div>

      </div>
      <!-- End icon -->

      <!-- Right Content -->
      <div class="pb-8 grow group-last:pb-0 content">

        <p class="font-semibold text-gray-800 dark:text-neutral-200">
          Print Design
        </p>

        <p class="mt-1 text-gray-600 dark:text-neutral-400">
          Developing brochures, business cards, posters, and packaging design for both physical and digital distribution, e.g.
        </p>

        <ul class="list-disc ms-6 mt-3 space-y-1.5 expertise-list">
          <li class="text-gray-600 ps-1 dark:text-neutral-400">
            Folded pamphlets or booklets that provide detailed information about products, services, or events, often used for marketing purposes.
          </li>
          <li class="text-gray-600 ps-1 dark:text-neutral-400">
            Large-format prints designed to be displayed in public spaces, often used for advertising events, movies, or products.
          </li>
          <li class="text-gray-600 ps-1 dark:text-neutral-400">
            Creative design for product packaging, including boxes, bags, labels, and wrappers that visually appeal to consumers while conveying brand identity.
          </li>
          <li class="text-gray-600 ps-1 dark:text-neutral-400">
            Custom-designed invitations for weddings, parties, corporate events, and other occasions, often featuring elegant typography and imagery.
          </li>
          <li class="text-gray-600 ps-1 dark:text-neutral-400">
            Large outdoor advertising prints designed to catch the attention of drivers and pedestrians, typically located in high-traffic areas.
          </li>
        </ul>
      </div>
      <!-- End Right Content -->
    </div>
    <!-- End Item -->

    <!-- Item -->
    <div class="relative flex group gap-x-5 expertise-item">

      <!-- icon -->
      <div
        class="icon-container relative group-last:after:hidden after:absolute after:top-8 after:bottom-2 after:start-3 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
        <div
          class="relative z-10 flex items-center justify-center size-6">
          <IconFingerprint
            class="text-gray-600 shrink-0 size-6 dark:text-neutral-400"
            size="32" />
        </div>
      </div>
      <!-- End icon -->

      <!-- Right Content -->
      <div class="pb-8 grow group-last:pb-0 content">

        <p class="font-semibold text-gray-800 dark:text-neutral-200">
          Branding and Identity
        </p>

        <p class="mt-1 text-gray-600 dark:text-neutral-400">
          Crafting cohesive brand identities, including logos, color schemes, and typography,
          for companies to establish a strong market presence.
        </p>

        <!-- <div class="mt-3"> -->
          <!-- Card -->
          <!-- <a
            class="block border border-gray-200 rounded-lg hover:shadow-sm focus:outline-none dark:border-neutral-700"
            href="#">
            <div class="relative flex items-center overflow-hidden">
              <img
                class="absolute inset-0 object-cover w-32 h-full sm:w-48 rounded-s-lg"
                src="/assets/ux.PNG" alt="Blog Image">

              <div class="p-4 grow ms-32 sm:ms-48">
                <div class="flex flex-col justify-center min-h-24">
                  <h3 class="font-semibold text-gray-800 dark:text-neutral-300">
                    Studio by Mailchimp
                  </h3>
                  <p class="mt-1 text-gray-500 dark:text-neutral-500">
                    Produce professional, reliable streams easily leveraging Mailchimp's innovative broadcast studio.
                  </p>
                </div>
              </div>
            </div>
          </a> -->
          <!-- End Card -->
        <!-- </div> -->
      </div>
      <!-- End Right Content -->
    </div>
    <!-- End Item -->

    <!-- Item -->
    <div class="relative flex group gap-x-5 expertise-item">

      <!-- icon 36C5F0 2EB67D ECB22E E01E5A -->
      <div
        class="icon-container relative group-last:after:hidden after:absolute after:top-8 after:bottom-2 after:start-3 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">

        <div class="relative z-10 flex items-center justify-center size-6">
          <IconDeviceGamepad2
            class="text-gray-600 shrink-0 size-6 dark:text-neutral-400" size="32" />
        </div>
      </div>
      <!-- End icon -->

      <!-- Right Content -->
      <div class="pb-8 grow group-last:pb-0 content">

        <p class="font-semibold text-gray-800 dark:text-neutral-200">
          UI Design
        </p>

        <p class="mt-1 text-gray-600 dark:text-neutral-400">
          Specializing in creating intuitive and visually appealing interfaces for websites and mobile apps to enhance user experience.
        </p>
      </div>
      <!-- End Right Content -->
    </div>
    <!-- End Item -->

    <!-- Item -->
    <div class="relative flex group gap-x-5 expertise-item">
      <!-- icon -->
      <div
        class="icon-container relative group-last:after:hidden after:absolute after:top-8 after:bottom-2 after:start-3 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
        <div class="relative z-10 flex items-center justify-center size-6">
          <IconMovie
            class="text-gray-800 shrink-0 size-6 dark:text-neutral-200" size="24" />
        </div>
      </div>
      <!-- End icon -->

      <!-- Right Content -->
      <div class="pb-8 grow group-last:pb-0 content">
        <p class="font-semibold text-gray-800 dark:text-neutral-200">
          Motion Graphics
        </p>

        <p class="mt-1 text-gray-600 dark:text-neutral-400">
          Incorporating animated elements in web design, promotional materials, or UI elements to create dynamic and engaging user interactions.
        </p>

        <ul class="list-disc ms-6 mt-3 space-y-1.5 expertise-list">

          <li class="text-gray-600 ps-1 dark:text-neutral-400">
            I edit and composite animated elements with live-action footage or other media, ensuring seamless integration and a polished final product.
          </li>

          <li class="text-gray-600 ps-1 dark:text-neutral-400">
            I sync animations with audio elements, including voiceovers, sound effects, and music, to create a cohesive and immersive viewing experience.
          </li>

        </ul>

      </div>
      <!-- End Right Content -->
    </div>
    <!-- End Item -->
  </div>
  <!-- End Timeline -->
</div>
</template>
