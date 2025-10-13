<script lang="ts" setup>
import { Badge } from '@/components/ui/badge';
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
            <CardContent class="flex h-full flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0">
                            <CardTitle class="truncate">{{ clubUser.user.name }}</CardTitle>
                            <div class="text-sm text-muted-foreground">{{ clubUser.user.email }}</div>
                            <div v-if="clubUser.user.phone" class="text-sm text-muted-foreground">
                                {{ clubUser.user.phone }}
                            </div>
                        </div>
                        <Badge :variant="clubUser.role === 'admin' ? 'default' : 'secondary'">
                            {{ clubUser.role === 'admin' ? 'Admin' : 'Member' }}
                        </Badge>
                    </div>
                </div>
                <dl class="grid gap-3 text-sm sm:grid-cols-3">
                    <div class="rounded-lg border bg-muted/30 p-3 text-center">
                        <dt class="text-xs font-medium uppercase text-muted-foreground">Total visits</dt>
                        <dd class="mt-1 text-lg font-semibold">{{ clubUser.total_visits }}</dd>
                    </div>
                    <div class="rounded-lg border bg-muted/30 p-3 text-center">
                        <dt class="text-xs font-medium uppercase text-muted-foreground">Last 7 days</dt>
                        <dd class="mt-1 text-lg font-semibold">{{ clubUser.visits_last_week }}</dd>
                    </div>
                    <div class="rounded-lg border bg-muted/30 p-3 text-center">
                        <dt class="text-xs font-medium uppercase text-muted-foreground">Last 30 days</dt>
                        <dd class="mt-1 text-lg font-semibold">{{ clubUser.visits_past_30_days }}</dd>
                    </div>
                </dl>
            </CardContent>
        </Card>
    </Link>
</template>
