<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import Notification from '@/Components/Notification.vue';
import { router } from '@inertiajs/vue3';
import SearchInput from '@/Components/SearchInput.vue';
import { ref, inject } from 'vue';

const props = defineProps({
    authors: Object,
    filters: Object,
    flash: {
        type: Object,
        default: () => ({
            success: null,
            error: null
        })
    }
});

const route = inject('route');


const search = ref(props.filters?.search || '');

const handleSearch = (value) => {
    router.get(
        route('authors.index'),
        { search: value },
        { preserveState: true, preserveScroll: true }
    );
};

const deleteAuthor = (author) => {
    if (confirm('Are you sure you want to delete this author?')) {
        router.delete(route('authors.destroy', author.id));
    }
};
</script>

<template>
    <Head title="Authors" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">Authors</h2>
                <Link
                    :href="route('authors.create')"
                    class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-orange-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Add New Author
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
                        <div class="mb-4">
                            <SearchInput
                                v-model="search"
                                @search="handleSearch"
                                placeholder="Search authors..."
                            />
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nationality</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="author in authors.data" :key="author.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">{{ author.name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ author.nationality }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <Link
                                                :href="route('authors.edit', author.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-4"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                @click="deleteAuthor(author)"
                                                class="text-red-600 hover:text-red-900"
                                            >
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="authors.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
