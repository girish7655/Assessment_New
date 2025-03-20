<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import BookForm from '@/Components/Books/BookForm.vue';
import { router } from '@inertiajs/vue3';
import { ref, watch, inject } from 'vue';
import Notification from '@/Components/Notification.vue';

const route = inject('route');

const props = defineProps({
    books: Object,
    filters: Object,
    flash: Object,
    categories: Array,
    authors: Array,
    publishers: Array,
});

const search = ref(props.filters?.search || '');
const selectedRating = ref(props.filters?.rating || '');
const selectedStatus = ref(props.filters?.status || '');
const sortBy = ref(props.filters?.sortBy || 'title');

const ratingOptions = [
    { value: '', label: 'All Ratings' },
    { value: '5', label: '5 Stars' },
    { value: '4', label: '4 Stars & Up' },
    { value: '3', label: '3 Stars & Up' },
    { value: '2', label: '2 Stars & Up' },
    { value: '1', label: '1 Star & Up' },
];

const statusOptions = [
    { value: '', label: 'All Books' },
    { value: 'available', label: 'Available Books' },
    { value: 'checked_out', label: 'Checked Out Books' },
];

const sortOptions = [
    { value: 'title', label: 'Title' },
    { value: 'rating', label: 'Rating' },
    { value: 'created_at', label: 'Date Added' },
];

const applyFilters = () => {
    router.get(
        route('books.index'),
        {
            search: search.value,
            rating: selectedRating.value,
            status: selectedStatus.value,
            sortBy: sortBy.value,
        },
        { 
            preserveState: true, 
            preserveScroll: true,
            replace: true 
        }
    );
};

watch([search, selectedRating, selectedStatus, sortBy], () => {
    applyFilters();
});

const deleteBook = (book) => {
    if (confirm('Are you sure you want to delete this book?')) {
        router.delete(route('books.destroy', book.id));
    }
};
</script>

<template>
    <Head title="Books" />
    
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">
                    Books
                </h2>
                <Link
                    v-if="$page.props.auth.user.role === 'librarian'"
                    :href="route('books.create')"
                    class="px-4 py-2 bg-orange-800 text-white rounded-md hover:bg-orange-700 text-sm"
                >
                    Add Book
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
                        <!-- Search and Filters -->
                        <div class="flex flex-wrap items-end gap-4 mb-6">
                            <!-- Search Bar -->
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Search
                                </label>
                                <SearchInput
                                    v-model="search"
                                    placeholder="Search books by title, author, or ISBN..."
                                    class="w-full"
                                />
                            </div>

                            <!-- Rating Filter -->
                            <div class="w-40">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Rating
                                </label>
                                <select
                                    v-model="selectedRating"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option v-for="option in ratingOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div class="w-40">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Status
                                </label>
                                <select
                                    v-model="selectedStatus"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Sort By -->
                            <div class="w-40">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Sort By
                                </label>
                                <select
                                    v-model="sortBy"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                >
                                    <option v-for="option in sortOptions" :key="option.value" :value="option.value">
                                        {{ option.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Books Table -->
                        <div class="mt-6 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left">Title</th>
                                        <th class="px-6 py-3 text-left">Author</th>
                                        <th class="px-6 py-3 text-left">ISBN</th>
                                        <th class="px-6 py-3 text-left">Status</th>
                                        <th class="px-6 py-3 text-left">Rating</th>
                                        <th class="px-6 py-3 text-left">Created By</th>
                                        <th class="px-6 py-3 text-left">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="book in books.data" :key="book.id">
                                        <td class="px-6 py-4">{{ book.title }}</td>
                                        <td class="px-6 py-4">{{ book.author?.name }}</td>
                                        <td class="px-6 py-4">{{ book.isbn }}</td>
                                        <td class="px-6 py-4">
                                            <span
                                                :class="{
                                                    'px-3 py-2 rounded-md text-sm font-medium whitespace-nowrap inline-block': true,
                                                    'bg-green-100 text-green-800': book.status === 'available',
                                                    'bg-red-100 text-red-800': book.status === 'checked_out'
                                                }"
                                            >
                                                {{ book.status === 'available' ? 'Available' : 'Checked Out' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <span>{{ book.reviews_avg_rating ? Number(book.reviews_avg_rating).toFixed(1) : '0.0' }}</span>
                                                <span class="ml-1 text-yellow-400">⭐</span>
                                                <span class="ml-1 text-gray-500 text-sm">({{ book.reviews_count }})</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">{{ book.creator.name }}</td>
                                        <td class="px-6 py-4">
                                            <div class="flex space-x-2">
                                                <Link
                                                    :href="route('books.show', book.id)"
                                                    class="text-blue-600 hover:text-blue-900"
                                                >
                                                    View
                                                </Link>
                                                
                                                <Link
                                                    v-if="$page.props.auth.user.role === 'librarian' && 
                                                          $page.props.auth.user.id === book.created_by"
                                                    :href="route('books.edit', book.id)"
                                                    class="text-indigo-600 hover:text-indigo-900"
                                                >
                                                    Edit
                                                </Link>
                                                
                                                <button
                                                    v-if="$page.props.auth.user.role === 'librarian' && 
                                                          $page.props.auth.user.id === book.created_by && book.status === 'available'"
                                                    @click="deleteBook(book)"
                                                    class="text-red-600 hover:text-red-900"
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <Pagination :links="books.links" class="mt-6" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
