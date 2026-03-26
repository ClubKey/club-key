<script lang="ts" setup>

import VisitCard from '@/components/visits/VisitCard.vue';
import { Paginator, Visit } from '@/types';
import { InfiniteScroll } from '@inertiajs/vue3';

defineProps<{
    visits?: Paginator<Visit> | null;
}>();
</script>

<template>
    <InfiniteScroll v-if="visits?.data" :buffer="100" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3" data="visits">
        <VisitCard v-for="visit in visits.data" :key="visit.id" :visit />
    </InfiniteScroll>
</template>
