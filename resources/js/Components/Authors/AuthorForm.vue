<script setup>
import { useForm, router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useField, useForm as useVeeForm } from 'vee-validate';
import { authorRules } from '@/validation/authorRules';
import { ref, onMounted, inject } from 'vue';

// Get yesterday's date as the maximum allowed date
const maxDate = new Date();
maxDate.setDate(maxDate.getDate() - 1);
const maxDateString = maxDate.toISOString().split('T')[0];

const route = inject('route');

const props = defineProps({
    author: {
        type: Object,
        default: () => ({
            name: '',
            birth_date: '',
            nationality: '',
        }),
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

// Format the date to YYYY-MM-DD for the input field
const formatDate = (date) => {
    if (!date) return '';
    try {
        if (typeof date === 'string') {
            // If it's already in YYYY-MM-DD format, return as is
            if (date.match(/^\d{4}-\d{2}-\d{2}$/)) return date;
            
            // Otherwise, try to parse and format
            const parsed = new Date(date);
            if (!isNaN(parsed)) {
                return parsed.toISOString().split('T')[0];
            }
        }
        if (date instanceof Date && !isNaN(date)) {
            return date.toISOString().split('T')[0];
        }
    } catch (e) {
        console.error('Date parsing error:', e);
    }
    return '';
};

const initialValues = {
    ...props.author,
    birth_date: formatDate(props.author.birth_date),
};

const { handleSubmit, resetForm } = useVeeForm({
    validationSchema: authorRules,
    initialValues,
});

const { value: name, errorMessage: nameError } = useField('name');
const { value: birth_date, errorMessage: birthDateError } = useField('birth_date');
const { value: nationality, errorMessage: nationalityError } = useField('nationality');

const processing = ref(false);

const submit = handleSubmit(async (values) => {
    processing.value = true;
    try {
        const formData = {
            ...values,
            birth_date: values.birth_date || null,
        };

        if (props.mode === 'create') {
            await router.post(route('authors.store'), formData);
        } else {
            await router.put(route('authors.update', props.author.id), formData);
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
                autofocus
            />
            <InputError :message="nameError || (errors && errors.name)" class="mt-2" />
        </div>

        <!-- Birth Date Field -->
        <div>
            <InputLabel for="birth_date" value="Birth Date" />
            <TextInput
                id="birth_date"
                v-model="birth_date"
                type="date"
                :max="maxDateString"
                class="mt-1 block w-full"
                :value="birth_date || ''" 
            />
            <InputError :message="birthDateError || (errors && errors.birth_date)" class="mt-2" />
        </div>

        <!-- Nationality Field -->
        <div>
            <InputLabel for="nationality" value="Nationality" />
            <TextInput
                id="nationality"
                v-model="nationality"
                type="text"
                class="mt-1 block w-full"
            />
            <InputError :message="nationalityError || (errors && errors.nationality)" class="mt-2" />
        </div>
        
        <!-- Submit Button -->
        <div class="flex items-center justify-end">
            <PrimaryButton 
                class="ml-4" 
                :class="{ 'opacity-25': processing }" 
                :disabled="processing"
            >
                {{ mode === 'create' ? 'Create Author' : 'Update Author' }}
            </PrimaryButton>
        </div>
    </form>
</template>
