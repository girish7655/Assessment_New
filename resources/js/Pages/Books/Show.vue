<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { inject, ref } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import Notification from '@/Components/Notification.vue';

const route = inject('route');

const props = defineProps({
    book: {
        type: Object,
        required: true
    },
    can: {
        type: Object,
        required: true
    },
    auth: {
        type: Object,
        required: true
    },
    hasCheckedOutBook: {
        type: Boolean,
        required: true
    },
    flash: {
        type: Object,
        default: () => ({
            success: null,
            error: null
        })
    },
    userReview: {
        type: Object,
        default: null
    }
});

const showReviewModal = ref(false);
const processing = ref(false);

const form = useForm({
    rating: props.userReview?.rating || 5,
    message: props.userReview?.message || '',
});

const ratingOptions = [
    { value: 5, label: '⭐⭐⭐⭐⭐ Excellent' },
    { value: 4, label: '⭐⭐⭐⭐ Very Good' },
    { value: 3, label: '⭐⭐⭐ Good' },
    { value: 2, label: '⭐⭐ Fair' },
    { value: 1, label: '⭐ Poor' },
];

const checkoutBook = () => {
    if (confirm('Are you sure you want to checkout this book?')) {
        router.post(route('books.checkout', props.book.id));
    }
};

const returnBook = () => {
    if (confirm('Are you sure you want to return this book?')) {
        router.post(route('books.return', props.book.id));
    }
};

const openReviewModal = () => {
    showReviewModal.value = true;
};

const closeReviewModal = () => {
    showReviewModal.value = false;
    form.reset();
    form.clearErrors();
};

const submitReview = () => {
    if (processing.value) return;
    
    processing.value = true;
    
    const url = props.userReview
        ? route('books.reviews.update', [props.book.id, props.userReview.id])
        : route('books.reviews.store', props.book.id);
    
    const method = props.userReview ? 'put' : 'post';

    form[method](url, {
        preserveScroll: true,
        onSuccess: () => {
            closeReviewModal();
            processing.value = false;
        },
        onError: () => {
            processing.value = false;
        }
    });
};

const deleteReview = () => {
    if (!props.userReview) return;
    confirmDeleteReview(props.userReview.id);
};

const editReview = (review) => {
    form.rating = review.rating;
    form.message = review.message;
    showReviewModal.value = true;
};

const confirmDeleteReview = (reviewId) => {
    if (!confirm('Are you sure you want to delete this review?')) return;

    router.delete(route('books.reviews.destroy', [props.book.id, reviewId]), {
        preserveScroll: true,
        onSuccess: () => {
            if (showReviewModal.value) {
                closeReviewModal();
            }
        }
    });
};
</script>

<template>
    <Head :title="book.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">
                    Book Details
                </h2>
                <div class="flex space-x-2">
                    <Link
                        :href="route('books.index')"
                        class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700 text-sm"
                    >
                        Back to Books
                    </Link>
                    <Link
                        v-if="can.update"
                        :href="route('books.edit', book.id)"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm"
                    >
                        Edit Book
                    </Link>
                    <!-- Add Checkout Button for customers -->
                    <button
                        v-if="auth.user.role === 'customer' && book.status === 'available'"
                        @click="checkoutBook"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm"
                    >
                        Checkout Book
                    </button>

                    <button
                        v-if="auth.user.role === 'librarian' && book.status === 'checked_out'"
                        @click="returnBook"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm"
                    >
                        Mark as Returned
                    </button>

                    <button
                        v-if="auth.user.role === 'customer' && hasCheckedOutBook && book.status === 'available'"
                        @click="openReviewModal"
                        class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm"
                    >
                        {{ userReview ? 'Edit Review' : 'Add Review' }}
                    </button>
                </div>
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
                    <div class="p-6">
                        <!-- Book Details -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Title</label>
                                        <p class="mt-1">{{ book.title }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">ISBN</label>
                                        <p class="mt-1">{{ book.isbn }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Published Year</label>
                                        <p class="mt-1">{{ book.published_year }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Status</label>
                                        <p class="mt-1">
                                            <span
                                                :class="{
                                                    'px-2 py-1 rounded text-sm': true,
                                                    'bg-green-100 text-green-800': book.status === 'available',
                                                    'bg-red-100 text-red-800': book.status === 'checked_out'
                                                }"
                                            >
                                                {{ book.status === 'available' ? 'Available' : 'Checked Out' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h3 class="text-lg font-semibold mb-4">Additional Details</h3>
                                <div class="space-y-4">
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Author</label>
                                        <p class="mt-1">{{ book.author?.name }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Publisher</label>
                                        <p class="mt-1">{{ book.publisher?.name }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-sm font-medium text-gray-500">Category</label>
                                        <p class="mt-1">{{ book.category?.name }}</p>
                                    </div>
                                    
                                    <div>
                                        <label class="text-lg font-semibold text-gray-900">Rating & Reviews</label>
                                        <div class="mt-2">
                                            <div class="flex items-center mb-4">
                                                <span class="text-3xl font-bold">{{ book.reviews_avg_rating ? Number(book.reviews_avg_rating).toFixed(1) : '0.0' }}</span>
                                                <span class="ml-2 text-2xl text-yellow-400">⭐</span>
                                                <span class="ml-2 text-gray-600">({{ book.reviews_count }} reviews)</span>
                                            </div>
                                            
                                            <!-- Add/Edit Review Button -->
                                            <button
                                                v-if="auth.user.role === 'customer' && hasCheckedOutBook && !userReview"
                                                @click="openReviewModal"
                                                class="px-4 py-2 bg-orange-600 text-white rounded-md hover:bg-orange-700 text-sm"
                                            >
                                                Add Review
                                            </button>
                                        </div>

                                        <!-- Reviews List -->
                                        <div v-if="book.reviews && book.reviews.length > 0" class="mt-6 space-y-6 border-t border-gray-200 pt-6">
                                            <div 
                                                v-for="review in book.reviews" 
                                                :key="review.id" 
                                                class="border-b border-gray-100 last:border-b-0 pb-6 last:pb-0"
                                            >
                                                <div class="flex justify-between items-start">
                                                    <div class="space-y-2">
                                                        <div class="flex items-center gap-2">
                                                            <span class="font-medium text-gray-900">{{ review.user.name }}</span>
                                                            <span class="text-yellow-400 text-sm">{{ '⭐'.repeat(review.rating) }}</span>
                                                        </div>
                                                        <p class="text-gray-600 text-sm">{{ review.message }}</p>
                                                        <p class="text-gray-400 text-xs">
                                                            {{ new Date(review.created_at).toLocaleDateString() }}
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Edit/Delete buttons - Only show for the review owner -->
                                                    <div v-if="auth.user.id === review.user_id" class="flex gap-3 ml-4">
                                                        <button
                                                            @click="() => editReview(review)"
                                                            class="text-sm text-blue-600 hover:text-blue-800"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            @click="() => confirmDeleteReview(review.id)"
                                                            class="text-sm text-red-600 hover:text-red-800"
                                                        >
                                                            Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- No Reviews Message -->
                                        <div v-else class="mt-4 text-center py-6 text-gray-500 bg-gray-50 rounded-lg">
                                            No reviews yet for this book.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold mb-4">Description</h3>
                            <p class="text-gray-600 whitespace-pre-line">{{ book.description }}</p>
                        </div>

                        <!-- Created By Information -->
                        <div class="mt-8 pt-8 border-t border-gray-200">
                            <div class="flex items-center text-sm text-gray-500">
                                <span>Created by {{ book.creator.name }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ new Date(book.created_at).toLocaleDateString() }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Review Modal -->
    <Modal :show="showReviewModal" @close="closeReviewModal">
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900">
                {{ userReview ? 'Edit Review' : 'Add Review' }}
            </h2>

            <form @submit.prevent="submitReview" class="mt-6">
                <!-- Rating -->
                <div class="mb-6">
                    <InputLabel for="rating" value="Rating" />
                    <select
                        id="rating"
                        v-model="form.rating"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                    >
                        <option v-for="option in ratingOptions" 
                                :key="option.value" 
                                :value="option.value"
                        >
                            {{ option.label }}
                        </option>
                    </select>
                    <InputError :message="form.errors.rating" class="mt-2" />
                </div>

                <!-- Review Message -->
                <div class="mb-6">
                    <InputLabel for="message" value="Review" />
                    <textarea
                        id="message"
                        v-model="form.message"
                        rows="4"
                        class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        placeholder="Share your thoughts about this book..."
                        required
                    ></textarea>
                    <InputError :message="form.errors.message" class="mt-2" />
                </div>

                <!-- Modal Actions -->
                <div class="mt-6 flex justify-end space-x-2">
                    <SecondaryButton @click="closeReviewModal">
                        Cancel
                    </SecondaryButton>

                    <PrimaryButton
                        :class="{ 'opacity-25': processing }"
                        :disabled="processing"
                    >
                        {{ userReview ? 'Update Review' : 'Submit Review' }}
                    </PrimaryButton>

                    <button
                        v-if="userReview"
                        type="button"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        @click="deleteReview"
                    >
                        Delete Review
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
