<script lang="ts" setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import type { Promotion } from '@/types';
import { Megaphone } from 'lucide-vue-next';

defineProps<{ promotion: Promotion }>();
</script>

<template>
    <!--    If the promotion has a link, add a button to the end of the alert.-->
    <Alert :link="promotion.link" variant="default">
        <Megaphone aria-hidden="true" class="size-5" />
        <AlertTitle>{{ promotion.name }}</AlertTitle>

        <AlertDescription>{{ promotion.description }}</AlertDescription>
    </Alert>
</template>
