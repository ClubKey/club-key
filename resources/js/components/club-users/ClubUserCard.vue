<script lang="ts" setup>
import { Card, CardContent, CardTitle } from '@/components/ui/card';
import { ClubUser } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    clubUser: ClubUser;
}>();
</script>

<template>
    <Link :href="route('members.show', clubUser.id)">
        <Card class="h-full transition hover:shadow-md">
            <CardContent class="flex flex-col gap-2">
                <CardTitle>{{ clubUser.user.name }}</CardTitle>
                <div class="text-sm text-wrap text-foreground">{{ clubUser.user.email }}</div>
                <dl class="flex justify-between font-semibold">
                    <dt>Visits this month:</dt>
                    <dd>{{ clubUser.visits_past_30_days }}</dd>
                </dl>
            </CardContent>
        </Card>
    </Link>
</template>
