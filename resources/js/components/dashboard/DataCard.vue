<script lang="ts" setup>
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import { DashboardDatum } from '@/types';

defineProps<{ datum: DashboardDatum }>();
</script>

<template>
    <Card>
        <CardContent class="flex flex-col gap-4">
            <div class="text-4xl font-semibold">{{ datum.value }}</div>
            <CardTitle>{{ datum.heading }}</CardTitle>
            <p v-if="datum.subheading" class="text-sm text-muted-foreground">{{ datum.subheading }}</p>
        </CardContent>
    </Card>
</template>
