<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Notification from '@/Components/Notification.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { ref, inject } from 'vue';

const route = inject('route');

const props = defineProps({
    categories: Object,
    filters: Object,
    flash: {
        type: Object,
        default: () => ({
            success: null,
            error: null
        })
    }
});

const search = ref(props.filters?.search || '');

const handleSearch = (value) => {
    router.get(
        route('categories.index'),
        { search: value },
        { preserveState: true, preserveScroll: true }
    );
};

const deleteCategory = (category) => {
    if (window.confirm('Are you sure you want to delete this category?')) {
        router.delete(route('categories.destroy', category.id));
    }
};
</script>

<template>
    <Head title="Categories" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">Categories</h2>
                <Link
                    :href="route('categories.create')"
                    class="px-4 py-2 bg-orange-800 text-white rounded-md hover:bg-orange-700 text-sm"
                >
                    Add Category
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Notifications -->
                <div v-if="flash?.success || flash?.error" class="mb-4">
                    <Notification
                        v-if="flash?.success"
                        type="success"
                        :message="flash.success"
                        :duration="3000"
                    />
                    <Notification
                        v-if="flash?.error"
                        type="error"
                        :message="flash.error"
                        :duration="3000"
                    />
                </div>
                
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <SearchInput
                                v-model="search"
                                @search="handleSearch"
                                placeholder="Search categories..."
                            />
                        </div>

                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Name</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Description</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="category in categories.data" :key="category.id">
                                    <td class="px-6 py-4">{{ category.name }}</td>
                                    <td class="px-6 py-4">{{ category.description }}</td>
                                    <td class="px-6 py-4">
                                        <Link
                                            :href="route('categories.edit', category.id)"
                                            class="text-indigo-600 hover:text-indigo-900 mr-3"
                                        >
                                            Edit
                                        </Link>
                                        <button
                                            @click="deleteCategory(category)"
                                            class="text-red-600 hover:text-red-900"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
