<script setup>
import { useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import { useField, useForm as useVeeForm } from 'vee-validate';
import { categoryRules } from '@/validation/categoryRules';
import { ref, inject } from 'vue';

const route = inject('route');

const props = defineProps({
    category: {
        type: Object,
        default: () => ({
            name: '',
            description: '',
        }),
    },
    categories: {
        type: Array,
        default: () => [],
    },
    mode: {
        type: String,
        default: 'create',
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
});

const { handleSubmit, resetForm } = useVeeForm({
    validationSchema: categoryRules,
    initialValues: props.category,
});

const { value: name, errorMessage: nameError } = useField('name');
const { value: description, errorMessage: descriptionError } = useField('description');

const processing = ref(false);

const generateSlug = () => {
    if (name.value) {
        slug.value = name.value
            .toLowerCase()
            .replace(/[^\w\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
};

const submit = handleSubmit(async (values) => {
    processing.value = true;
    try {
        if (props.mode === 'create') {
            await router.post(route('categories.store'), values);
        } else {
            await router.put(route('categories.update', props.category.id), values);
        }
    } finally {
        processing.value = false;
    }
});
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
        <!-- Name Field -->
        <div>
            <InputLabel for="name" value="Name *" />
            <TextInput
                id="name"
                v-model="name"
                type="text"
                class="mt-1 block w-full"
                required
                @input="generateSlug"
                autofocus
            />
            <InputError :message="nameError || errors?.name" class="mt-2" />
        </div>
        
        <!-- Description Field -->
        <div>
            <InputLabel for="description" value="Description" />
            <textarea
                id="description"
                v-model="description"
                rows="4"
                class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
            ></textarea>
            <InputError :message="descriptionError || errors?.description" class="mt-2" />
        </div>


        <!-- Submit Button -->
        <div class="flex items-center justify-end">
            <PrimaryButton
                :class="{ 'opacity-25': processing }"
                :disabled="processing"
            >
                {{ mode === 'create' ? 'Create Category' : 'Update Category' }}
            </PrimaryButton>
        </div>
    </form>
</template>