<script setup>
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';

const route = inject('route');

defineProps({
    auth: Object,
});
</script>

<template>
    <nav>
        <ul class="space-y-1">
            <li>
                <Link 
                    :href="route('dashboard')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"
                    :class="{ 'bg-gray-100': route().current('dashboard') }"
                >
                    <span>Dashboard</span>
                </Link>
            </li>
            <li>
                <Link 
                    :href="route('books.index')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"
                    :class="{ 'bg-gray-100': route().current('books.*') }"
                >
                    <span>Books</span>
                </Link>
            </li>
            <!-- Authors link - only visible to librarians -->
            <li v-if="auth.user.role === 'librarian'">
                <Link 
                    :href="route('authors.index')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"
                    :class="{ 'bg-gray-100': route().current('authors.*') }"
                >
                    <span>Authors</span>
                </Link>
            </li>

            <li v-if="auth.user.role === 'librarian'">
                <Link 
                    :href="route('categories.index')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"
                    :class="{ 'bg-gray-100': route().current('categories.*') }"
                >
                    <span>Authors</span>
                </Link>
            </li>

            <li v-if="auth.user.role === 'librarian'">
                <Link 
                    :href="route('publishers.index')"
                    class="flex items-center px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg"
                    :class="{ 'bg-gray-100': route().current('publishers.*') }"
                >
                    <span>Authors</span>
                </Link>
            </li>
        </ul>
    </nav>
</template>
