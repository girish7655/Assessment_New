<script setup>
import { formatDistanceToNow } from 'date-fns';

defineProps({
    activities: Array
});

const getActivityIcon = (activity) => {
    // Return appropriate icon based on activity type
    switch (activity.subject_type) {
        case 'App\\Models\\Book':
            return 'book';
        case 'App\\Models\\Category':
            return 'folder';
        default:
            return 'activity';
    }
};
</script>

<template>
    <div class="flow-root">
        <ul role="list" class="-mb-8">
            <li v-for="(activity, index) in activities" :key="index">
                <div class="relative pb-8">
                    <span v-if="index !== activities.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                    <div class="relative flex space-x-3">
                        <div>
                            <span class="h-8 w-8 rounded-full bg-gray-400 flex items-center justify-center ring-8 ring-white">
                                <!-- Icon based on activity type -->
                            </span>
                        </div>
                        <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                            <div>
                                <p class="text-sm text-gray-500">{{ activity.description }}</p>
                            </div>
                            <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                {{ formatDistanceToNow(new Date(activity.created_at), { addSuffix: true }) }}
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>