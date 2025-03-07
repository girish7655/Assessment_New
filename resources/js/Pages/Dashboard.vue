<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import StatCard from '@/Components/Dashboard/StatCard.vue';
import CategoryChart from '@/Components/Dashboard/CategoryChart.vue';

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-orange-800 leading-tight">Dashboard</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Global Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <StatCard
                        title="Total Books"
                        :value="stats.total_books"
                        icon="book"
                        color="blue"
                    />
                    <StatCard v-if="$page.props.auth.user.role === 'librarian'"
                        title="Total Categories"
                        :value="stats.total_categories"
                        icon="folder"
                        color="green"
                    />
                    <StatCard v-if="$page.props.auth.user.role === 'librarian'"
                        title="Total Users"
                        :value="stats.total_users"
                        icon="users"
                        color="purple"
                    />
                </div>

                <!-- Librarian Stats Grid -->
                <div v-if="$page.props.auth.user.role === 'librarian'" class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                    <StatCard
                        title="My Books"
                        :value="stats.my_books"
                        icon="book-open"
                        color="indigo"
                    />
                    <StatCard
                        title="My Categories"
                        :value="stats.my_categories"
                        icon="folder-plus"
                        color="yellow"
                    />
                    <StatCard
                        title="My Authors"
                        :value="stats.my_authors"
                        icon="users-plus"
                        color="pink"
                    />
                    <StatCard
                        title="My Publishers"
                        :value="stats.my_publishers"
                        icon="building"
                        color="teal"
                    />
                </div>

                <!-- Charts Section -->
                <div v-if="stats.popular_categories" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold mb-4">Popular Categories</h3>
                        <CategoryChart :categories="stats.popular_categories" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
