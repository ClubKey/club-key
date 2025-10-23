<script lang="ts" setup>
import type { PrimitiveProps } from 'reka-ui';
import { Primitive } from 'reka-ui';
import type { HTMLAttributes } from 'vue';
import { badgeVariants, type BadgeVariants } from '.';
import { reactiveOmit } from '@vueuse/core';
import { cn } from '@/lib/utils';

const props = defineProps<
    PrimitiveProps & {
    variant?: BadgeVariants['variant']
    size?: BadgeVariants['size']
    class?: HTMLAttributes['class']
}
>();

const delegatedProps = reactiveOmit(props, 'class');
</script>

<template>
    <Primitive
        :class="cn(badgeVariants({ variant: props.variant, size: props.size }), props.class)"
        data-slot="badge"
        v-bind="delegatedProps"
    >
        <slot />
    </Primitive>
</template>
