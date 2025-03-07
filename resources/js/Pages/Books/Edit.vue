<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import BookForm from '@/Components/Books/BookForm.vue';
import Notification from '@/Components/Notification.vue';
import { inject } from 'vue';

const route = inject('route');

defineProps({
    book: Object,
    categories: Array,
    publishers: Array,
    authors: Array,
    errors: Object,
    flash: Object,
});
</script>

<template>
    <Head title="Edit Book" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">
                    Edit Book
                </h2>
                <Link
                    :href="route('books.index')"
                    class="mt-3 sm:mt-0 inline-flex items-center px-4 py-2 bg-orange-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Back to Books
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="flash?.success || errors?.title || errors?.isbn" class="mb-4">
                    <Notification
                        v-if="flash?.success"
                        type="success"
                        :message="flash.success"
                        :duration="3000"
                    />
                    <Notification
                        v-if="errors?.title || errors?.isbn"
                        type="error"
                        :message="errors?.title || errors?.isbn"
                        :duration="3000"
                    />
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="max-w-2xl mx-auto">
                            <div class="mb-6">
                                <h3 class="text-lg font-medium text-gray-900">
                                    Book Details
                                </h3>
                                <p class="mt-1 text-sm text-gray-600">
                                    Update the book information below. All fields marked with * are required.
                                </p>
                            </div>

                            <!-- Book Form -->
                            <div class="mt-6">
                                <BookForm 
                                    mode="edit"
                                    :book="book"
                                    :categories="categories"
                                    :publishers="publishers"
                                    :authors="authors"
                                    :errors="$page.props.errors"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

