<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, inject, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { bookRules } from '@/validation/bookRules';
import { useField, useForm as useVeeForm } from 'vee-validate';

const route = inject('route');

const props = defineProps({
    book: {
        type: Object,
        default: () => ({
            id: null,
            title: '',
            author_id: '',
            publisher_id: '',
            category_id: '',
            isbn: '',
            published_year: '',
            description: '',
            quantity: 1,
            available_quantity: 1,
        })
    },
    categories: {
        type: Array,
        required: true
    },
    publishers: {
        type: Array,
        required: true
    },
    authors: {
        type: Array,
        required: true
    },
    mode: {
        type: String,
        default: 'create'
    },
    errors: {
        type: Object,
        default: () => ({})
    }
});

// Watch for server-side validation errors
watch(() => props.errors, (newErrors) => {
    if (newErrors?.isbn) {
        setFieldError('isbn', newErrors.isbn);
    }
    if (newErrors?.title) {
        setFieldError('title', newErrors.title);
    }
}, { deep: true });

const processing = ref(false);

// Transform initial values for the form
const transformInitialValues = (values) => {
    return {
        ...values,
        author_id: values.author_id ? Number(values.author_id) : null,
        publisher_id: values.publisher_id ? Number(values.publisher_id) : null,
        category_id: values.category_id ? Number(values.category_id) : null,
        published_year: values.published_year ? String(values.published_year) : '', // Convert to string for input
    };
};

const { handleSubmit, resetForm, setFieldError } = useVeeForm({
    validationSchema: bookRules,
    initialValues: transformInitialValues(props.book)
});

const { value: title, errorMessage: titleError } = useField('title');
const { value: author_id, errorMessage: authorError } = useField('author_id');
const { value: publisher_id, errorMessage: publisherError } = useField('publisher_id');
const { value: category_id, errorMessage: categoryError } = useField('category_id');
const { value: isbn, errorMessage: isbnError } = useField('isbn');
const { value: published_year, errorMessage: publishedYearError } = useField('published_year');
const { value: description, errorMessage: descriptionError } = useField('description');

const submit = handleSubmit(async (values) => {
    processing.value = true;
    try {
        const formData = {
            ...values,
            published_year: values.published_year ? Number(values.published_year) : null,
            author_id: values.author_id ? Number(values.author_id) : null,
            publisher_id: values.publisher_id ? Number(values.publisher_id) : null,
            category_id: values.category_id ? Number(values.category_id) : null,
        };
        
        if (props.mode === 'create') {
            await router.post(route('books.store'), formData);
        } else {
            await router.put(route('books.update', props.book.id), formData);
        }
    } finally {
        processing.value = false;
    }
});
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <!-- Two Column Layout for larger screens -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Left Column -->
            <div class="space-y-6">
                <!-- Title Field -->
                <div>
                    <InputLabel for="title" value="Title *" class="text-sm font-medium text-gray-700" />
                    <TextInput
                        id="title"
                        v-model="title"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                        placeholder="Enter book title"
                    />
                    <InputError :message="titleError || errors?.title" class="mt-2" />
                </div>

                <!-- Author Dropdown -->
                <div>
                    <InputLabel for="author_id" value="Author *" />
                    <SelectInput
                        id="author_id"
                        v-model="author_id"
                        class="mt-1 block w-full"
                        required
                    >
                        <option value="">Select Author</option>
                        <option 
                            v-for="author in authors" 
                            :key="author.id" 
                            :value="author.id"
                            :selected="author.id === author_id"
                        >
                            {{ author.name }}
                        </option>
                    </SelectInput>
                    <InputError :message="authorError" class="mt-2" />
                </div>

                <!-- Publisher Dropdown -->
                <div>
                    <InputLabel for="publisher_id" value="Publisher *" />
                    <SelectInput
                        id="publisher_id"
                        v-model="publisher_id"
                        class="mt-1 block w-full"
                        required
                    >
                        <option value="">Select Publisher</option>
                        <option 
                            v-for="publisher in publishers" 
                            :key="publisher.id" 
                            :value="publisher.id"
                            :selected="publisher.id === publisher_id"
                        >
                            {{ publisher.name }}
                        </option>
                    </SelectInput>
                    <InputError :message="publisherError" class="mt-2" />
                </div>

                <!-- Category Dropdown -->
                <div>
                    <InputLabel for="category_id" value="Category *" />
                    <SelectInput
                        id="category_id"
                        v-model="category_id"
                        class="mt-1 block w-full"
                        required
                    >
                        <option value="">Select Category</option>
                        <option 
                            v-for="category in categories" 
                            :key="category.id" 
                            :value="category.id"
                            :selected="category.id === category_id"
                        >
                            {{ category.name }}
                        </option>
                    </SelectInput>
                    <InputError :message="categoryError" class="mt-2" />
                </div>

                <!-- ISBN Field -->
                <div>
                    <InputLabel for="isbn" value="ISBN *" class="text-sm font-medium text-gray-700" />
                    <TextInput
                        id="isbn"
                        v-model="isbn"
                        type="text"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                        placeholder="Enter ISBN"
                    />
                    <InputError :message="isbnError || errors?.isbn" class="mt-2" />
                </div>

                <!-- Published Year Field -->
                <div>
                    <InputLabel for="published_year" value="Published Year *" class="text-sm font-medium text-gray-700" />
                    <TextInput
                        id="published_year"
                        v-model="published_year"
                        type="number"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        required
                        placeholder="Enter published year"
                    />
                    <InputError :message="publishedYearError" class="mt-2" />
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Description Field -->
                <div>
                    <InputLabel for="description" value="Description *" class="text-sm font-medium text-gray-700" />
                    <textarea
                        id="description"
                        v-model="description"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        rows="4"
                        required
                        placeholder="Enter book description"
                    ></textarea>
                    <InputError :message="descriptionError" class="mt-2" />
                </div>

                
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end pt-6 border-t border-gray-200 mt-6">
            <Link
                :href="route('books.index')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150 mr-3"
            >
                Cancel
            </Link>
            <PrimaryButton
                :disabled="processing"
                :class="{ 'opacity-75 cursor-not-allowed': processing }"
            >
                {{ mode === 'create' ? 'Create Book' : 'Update Book' }}
            </PrimaryButton>
        </div>
    </form>
</template>
