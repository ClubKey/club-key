<script lang="ts" setup>
import { Alert, AlertDescription, AlertTitle } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { usePage } from '@inertiajs/vue3';
import { AlertCircle, Info, Rocket, TriangleAlert, X } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import confetti from 'canvas-confetti';

const page = usePage();

const messageType = computed(() => {
    if (page.props.flash.success) return 'success';
    if (page.props.flash.error) return 'error';
    if (page.props.flash.warning) return 'warning';
    if (page.props.flash.info) return 'info';
    return '';
});

const shouldShow = computed(() => {
    return page.props.flash.success || page.props.flash.error || page.props.flash.warning || page.props.flash.info;
});

const successMessage = computed(() => page.props.flash.success ?? '');

const hideMessage = () => {
    // Clear the flash message from the session
    // This prevents the message from showing again on page refresh
    if (page.props.flash) {
        page.props.flash.success = null;
        page.props.flash.error = null;
        page.props.flash.warning = null;
        page.props.flash.info = null;
    }

    hasLaunchedConfetti.value = false;
};

const message = computed(() => {
    return page.props.flash.success || page.props.flash.error || page.props.flash.warning || page.props.flash.info;
});

const title = computed(() => {
    switch (messageType.value) {
        case 'success':
            return 'Success';
        case 'error':
            return 'Error';
        case 'warning':
            return 'Warning';
        case 'info':
            return 'Info';
        default:
            return '';
    }
});

const isVisitSuccess = computed(() => successMessage.value.toLowerCase().includes('thanks for visiting us'));

const hasLaunchedConfetti = ref(false);

const launchConfetti = async () => {
    if (typeof window === 'undefined' || hasLaunchedConfetti.value) {
        return;
    }

    hasLaunchedConfetti.value = true;

    const duration = 2500;
    const animationEnd = Date.now() + duration;

    const frame = () => {
        confetti({
            particleCount: 6,
            startVelocity: 40,
            spread: 360,
            ticks: 90,
            gravity: 0.9,
            origin: {
                x: Math.random(),
                y: Math.random() * 0.3
            }
        });

        if (Date.now() < animationEnd) {
            requestAnimationFrame(frame);
        }
    };

    frame();
};

watch(
    () => isVisitSuccess.value,
    (value) => {
        if (value) {
            void launchConfetti();
        }
    },
    { immediate: true }
);
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="isVisitSuccess && shouldShow"
             class="fixed inset-0 z-[1000] flex h-screen w-full flex-col items-center justify-center">
            <div class="flex max-h-screen flex-col items-center bg-background/80 backdrop-blur">
                <div class="mx-4 max-w-2xl rounded-3xl bg-background p-8 text-center shadow-2xl">
                    <p class="text-sm font-semibold tracking-[0.4em] text-primary uppercase">Check-In Complete</p>
                    <h2 class="mt-4 text-4xl font-bold text-balance sm:text-5xl">Great job! You've checked in!</h2>
                    <h3 class="mt-4 text-2xl font-bold text-balance sm:text-3xl">
                        Make sure to show your card to the cashier to claim your rewards.
                    </h3>
                    <p class="mt-4 text-lg text-balance text-muted-foreground">
                        {{ successMessage }}
                    </p>
                    <Button class="mt-6" size="lg" @click="hideMessage">Celebrate!</Button>
                </div>
            </div>
        </div>
    </Transition>

    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="transform translate-y-2 opacity-0"
        enter-to-class="transform translate-y-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="transform translate-y-0 opacity-100"
        leave-to-class="transform translate-y-2 opacity-0"
    >
        <Alert
            v-if="!isVisitSuccess && shouldShow"
            :variant="messageType === 'error' ? 'destructive' : 'default'"
            class="fixed right-0 bottom-0 z-50 m-4 w-[90vw] md:m-8 md:max-w-lg"
        >
            <Rocket v-if="messageType === 'success'" class="size-4" />
            <Info v-else-if="messageType === 'info'" class="size-4" />
            <AlertCircle v-else-if="messageType === 'error'" class="size-4" />
            <TriangleAlert v-else-if="messageType === 'warning'" class="size-4" />
            <AlertTitle>{{ title }}</AlertTitle>
            <AlertDescription>
                {{ message }}
            </AlertDescription>

            <Button class="absolute top-0 right-0 m-2 size-6" size="icon" variant="outline" @click="hideMessage">
                <X name="x" />
            </Button>
        </Alert>
    </Transition>
</template>
