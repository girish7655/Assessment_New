<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'success'
    },
    message: {
        type: String,
        required: true
    },
    duration: {
        type: Number,
        default: 3000
    }
});

const isVisible = ref(true);

const backgroundColor = computed(() => {
    return {
        'success': 'bg-green-50',
        'error': 'bg-red-50'
    }[props.type];
});

const textColor = computed(() => {
    return {
        'success': 'text-green-800',
        'error': 'text-red-800'
    }[props.type];
});

const iconColor = computed(() => {
    return {
        'success': 'text-green-400',
        'error': 'text-red-400'
    }[props.type];
});

onMounted(() => {
    setTimeout(() => {
        isVisible.value = false;
    }, props.duration);
});
</script>

<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-300"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-show="isVisible" :class="[backgroundColor, 'rounded-md p-4 mb-4']">
            <div class="flex">
                <div class="flex-shrink-0">
                    <!-- Success Icon -->
                    <svg v-if="type === 'success'" class="h-5 w-5" :class="iconColor" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <!-- Error Icon -->
                    <svg v-if="type === 'error'" class="h-5 w-5" :class="iconColor" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p :class="[textColor, 'text-sm font-medium']">
                        {{ message }}
                    </p>
                </div>
                <!-- Close button -->
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button
                            @click="isVisible = false"
                            :class="[textColor, 'inline-flex rounded-md p-1.5 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600']"
                        >
                            <span class="sr-only">Dismiss</span>
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </Transition>
</template>
