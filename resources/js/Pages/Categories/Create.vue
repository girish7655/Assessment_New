<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import CategoryForm from '@/Components/Categories/CategoryForm.vue';
import Notification from '@/Components/Notification.vue';
import { inject } from 'vue';

const route = inject('route');

defineProps({
    categories: Array,
    errors: Object,
    flash: Object,
});
</script>

<template>
    <Head title="Create Category" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">
                    Create Category
                </h2>
                <Link
                    :href="route('categories.index')"
                    class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-orange-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Back to Categories
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                        <div class="max-w-2xl mx-auto">
                            <CategoryForm 
                                mode="create"
                                :categories="categories"
                                :errors="errors"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>