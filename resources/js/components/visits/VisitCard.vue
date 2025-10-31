<script lang="ts" setup>
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Visit } from '@/types';
import { CalendarDays, MapPin } from 'lucide-vue-next';

defineProps<{
    visit: Visit;
}>();

function formatDate(dateString: string) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
    });
}
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2 text-base">
                <CalendarDays class="h-4 w-4" />
                <span>{{ formatDate(visit.created_at) }}</span>
            </CardTitle>
        </CardHeader>
        <CardContent>
            <CardDescription v-if="visit.club_location" class="flex items-start gap-2">
                <MapPin class="mt-0.5 h-4 w-4 flex-shrink-0" />
                <div>
                    <div class="font-medium text-foreground">{{ visit.club_location.name }}</div>
                    <div class="text-sm">
                        {{ visit.club_location.street_address }}, {{ visit.club_location.city }},
                        {{ visit.club_location.state }} {{ visit.club_location.zip }}
                    </div>
                </div>
            </CardDescription>
            <CardDescription v-else class="flex items-start gap-2">
                <MapPin class="mt-0.5 h-4 w-4 flex-shrink-0" />
                <span>General Visit</span>
            </CardDescription>
        </CardContent>
    </Card>
</template>
