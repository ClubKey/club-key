<script lang="ts" setup>
import type { HTMLAttributes } from 'vue';
import { cn } from '@/lib/utils';
import { type AlertVariants, alertVariants } from '.';
import { ArrowUpRight } from 'lucide-vue-next';

const props = defineProps<{
    class?: HTMLAttributes['class']
    variant?: AlertVariants['variant']
    link?: string | null
}>()
</script>

<template>
  <component
    :is="link ? 'a' : 'div'"
    :class="cn(alertVariants({ variant }), 'relative group', props.class,)"
    :href="link"
    data-slot="alert"
    role="alert"
    target="_blank"
  >
      <ArrowUpRight v-if="link" class="h-4 w-4 absolute group-hover:translate-x-1 group-hover:-translate-y-1 transition-all ease-in-out duration-200 top-2 right-3"/>
    <slot />
  </component>
</template>
