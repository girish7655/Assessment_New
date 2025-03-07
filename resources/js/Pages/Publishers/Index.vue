<script setup>
import { ref, inject } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SearchInput from '@/Components/SearchInput.vue';
import Notification from '@/Components/Notification.vue';

const route = inject('route');

const props = defineProps({
    publishers: Object,
    filters: Object,
    flash: Object,
    can: Object,
    isLibrarian: Boolean
});

const search = ref(props.filters?.search || '');

const handleSearch = (value) => {
    router.get(
        route('publishers.index'),
        { search: value },
        { preserveState: true, preserveScroll: true }
    );
};

const deletePublisher = (publisher) => {
    if (window.confirm('Are you sure you want to delete this publisher?')) {
        router.delete(route('publishers.destroy', publisher.id));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">
                    Publishers
                    <span v-if="isLibrarian" class="text-sm text-gray-500">
                    </span>
                </h2>
                <Link
                    v-if="can.create"
                    :href="route('publishers.create')"
                    class="px-4 py-2 bg-orange-800 text-white rounded-md hover:bg-orange-700 text-sm"
                >
                    Add Publisher
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

                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="mb-6">
                            <SearchInput
                                v-model="search"
                                @search="handleSearch"
                                placeholder="Search Publishers..."
                            />
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 bg-gray-50 text-left">Name</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left">Address</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left">Phone</th>
                                        <th v-if="!isLibrarian" class="px-6 py-3 bg-gray-50 text-left">Created By</th>
                                        <th class="px-6 py-3 bg-gray-50 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="publisher in publishers.data" :key="publisher.id">
                                        <td class="px-6 py-4">{{ publisher.name }}</td>
                                        <td class="px-6 py-4">{{ publisher.address }}</td>
                                        <td class="px-6 py-4">{{ publisher.phone }}</td>
                                        <td v-if="!isLibrarian" class="px-6 py-4">{{ publisher.creator.name }}</td>
                                        <td class="px-6 py-4 flex space-x-2">
                                            
                                            <Link
                                                v-if="publisher.can?.update"
                                                :href="route('publishers.edit', publisher.id)"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3"
                                            >
                                                Edit
                                            </Link>
                                            <button
                                                v-if="publisher.can?.delete"
                                                @click="deletePublisher(publisher)"
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
        </div>
    </AuthenticatedLayout>
</template>
