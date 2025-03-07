<script setup>
defineProps({
    title: String,
    value: Number,
    icon: String,
    color: {
        type: String,
        default: 'blue'
    }
});

const colorClasses = {
    blue: 'bg-blue-500',
    green: 'bg-green-500',
    purple: 'bg-purple-500',
    orange: 'bg-orange-500'
};
</script>

<template>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="flex items-center">
                <div :class="[colorClasses[color], 'p-3 rounded-full']">
                    <svg class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <!-- Add different icon paths based on the icon prop -->
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-gray-500 text-sm">{{ title }}</p>
                    <p class="text-2xl font-semibold text-gray-700">{{ value }}</p>
                </div>
            </div>
        </div>
    </div>
</template>