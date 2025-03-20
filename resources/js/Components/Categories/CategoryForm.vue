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
        default: () => ({}),
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    mode: {
        type: String,
        default: 'create'
    }
});

const { handleSubmit, resetForm } = useVeeForm({
    validationSchema: categoryRules,
    initialValues: props.category,
});

const { value: name, errorMessage: nameError } = useField('name');
const { value: description, errorMessage: descriptionError } = useField('description');

const processing = ref(false);

const submit = handleSubmit(async (values) => {
    processing.value = true;
    try {
        if (props.mode === 'create') {
            await router.post(route('categories.store'), values);
        } else {
            await router.put(route('categories.update', props.category.id), values);
        }
    } catch (error) {
        // Handle error if needed
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
            <InputError :message="nameError || errors?.name" class="mt-2" />
        </div>

        <!-- Description Field -->
        <div>
            <InputLabel for="description" value="Description" />
            <TextArea
                id="description"
                v-model="description"
                class="mt-1 block w-full"
                rows="3"
            />
            <InputError :message="descriptionError || errors?.description" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">
                {{ mode === 'create' ? 'Create Category' : 'Update Category' }}
            </PrimaryButton>
        </div>
    </form>
</template>
