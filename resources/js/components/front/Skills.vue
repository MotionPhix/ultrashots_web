<script setup lang="ts">
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { onMounted, ref } from 'vue';

gsap.registerPlugin(ScrollTrigger);

const skillsRef = ref(null);

onMounted(() => {
  const skillsSection = skillsRef.value as any;

  // Animate the main skills section
  gsap.from(skillsSection, {
    opacity: 0,
    y: 50,
    duration: 0.8,
    scrollTrigger: {
      trigger: skillsSection,
      start: 'top bottom-=20',
      toggleActions: 'play none none reverse'
    }
  });

  // Animate each skill category
  gsap.utils.toArray(skillsSection?.querySelectorAll('dl')).forEach((dl: any, index) => {
    gsap.from(dl, {
      opacity: 0,
      x: -30,
      duration: 0.6,
      delay: index * 0.2,
      scrollTrigger: {
        trigger: dl,
        start: 'top bottom-=50',
        toggleActions: 'play none none reverse'
      }
    });
  });

  // Animate individual skills
  gsap.utils.toArray(skillsSection.querySelectorAll('li')).forEach((li: any, index) => {
    gsap.from(li, {
      opacity: 0,
      scale: 0.8,
      duration: 0.4,
      delay: 0.1 * index,
      scrollTrigger: {
        trigger: li,
        start: 'top bottom-=30',
        toggleActions: 'play none none reverse'
      }
    });
  });
});
</script>

<template>
<div class="my-10 sm:my-14" ref="skillsRef">

  <h2 class="mb-5 uppercase font-medium text-gray-800 dark:text-neutral-200">
    Skills
  </h2>

  <!-- List -->
  <div class="space-y-3">

    <dl class="flex flex-col gap-1 sm:flex-row">

      <dt class="min-w-40">
        <span class="block text-gray-500 dark:text-neutral-500">
          Software I use:
        </span>
      </dt>

      <dd>

        <ul>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandFigma size="16" class="mr-1" />
            Figma
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandAdobePhotoshop size="16" class="mr-1" />
            Adobe Photoshop
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandAdobeIllustrator size="16" class="mr-1" />
            Adobe Illustrator
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandAdobeIndesign size="16" class="mr-1" />
            Adobe Indesign
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandAdobeAfterEffect size="16" class="mr-1" />
            Adobe After Effects
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandCoreos size="16" class="mr-1" />
            CorelDraw
          </li>

        </ul>

      </dd>

    </dl>

    <dl class="flex flex-col gap-1 sm:flex-row">
      <dt class="min-w-40">
        <span class="block text-gray-500 dark:text-neutral-500">
          Coding Stack:
        </span>
      </dt>

      <dd>
        <ul>
          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandHtml5 stroke="2" class="mr-1" size="16" />
            HTML
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandCss3 stroke="2" class="mr-1" size="16" />
            CSS
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandTailwind stroke="2" class="mr-1" size="16" />
            Tailwind CSS
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandVue stroke="2" class="mr-1" size="16" />
            Vue
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            <IconBrandLaravel stroke="2" class="mr-1" size="16" />
            Laravel
          </li>
        </ul>
      </dd>
    </dl>

    <dl class="flex flex-col gap-1 sm:flex-row">

      <dt class="min-w-40">
        <span class="block text-gray-500 dark:text-neutral-500">
          Design Expertise:
        </span>
      </dt>

      <dd>

        <ul>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            UI/UX Design
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            Design Systems
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            Custom Illustrations
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            Responsive Design
          </li>

        </ul>

      </dd>

    </dl>

    <dl class="flex flex-col gap-1 sm:flex-row">

      <dt class="min-w-40">
        <span class="block text-gray-500 dark:text-neutral-500">
          Soft Skills:
        </span>
      </dt>

      <dd>

        <ul>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            Strong communication
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            Problem-solving
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            Attention to detail
          </li>

          <li class="me-1 after:content-[','] inline-flex items-center text-gray-800 dark:text-neutral-200">
            Time management
          </li>

        </ul>

      </dd>

    </dl>

  </div>
  <!-- End List -->
</div>
</template>
