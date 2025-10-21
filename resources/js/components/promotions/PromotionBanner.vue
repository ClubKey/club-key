<script lang="ts" setup>
import { Button } from '@/components/ui/button';
import type { Promotion } from '@/types';
import { Link } from '@inertiajs/vue3';
import { Megaphone } from 'lucide-vue-next';

defineProps<{ promotion: Promotion }>();
</script>

<template>
    <div class="flex flex-col gap-3 rounded-xl border border-primary/30 bg-primary/10 p-4 text-foreground shadow-sm">
        <div class="flex items-center gap-2 text-primary">
            <Megaphone aria-hidden="true" class="size-5" />
            <span class="text-xs font-semibold uppercase tracking-wide">Current Promotion</span>
        </div>
        <div class="flex flex-col gap-1">
            <h3 class="text-lg font-semibold leading-tight text-foreground">{{ promotion.name }}</h3>
            <p class="text-sm text-muted-foreground">{{ promotion.description }}</p>
        </div>
        <div v-if="promotion.link" class="flex">
            <Button as-child size="sm" variant="secondary" class="w-fit">
                <Link :href="promotion.link" rel="noopener noreferrer" target="_blank">Learn more</Link>
            </Button>
        </div>
    </div>
</template>
