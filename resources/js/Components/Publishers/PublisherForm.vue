<script setup>
import { ref, inject } from 'vue';
import { router } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useField, useForm as useVeeForm } from 'vee-validate';
import { publisherRules } from '@/validation/publisherRules';

const route = inject('route');

const props = defineProps({
    publisher: {
        type: Object,
        default: () => ({
            name: '',
            address: '',
            phone: ''
        })
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

const { handleSubmit, resetForm } = useVeeForm({
    validationSchema: publisherRules,
    initialValues: props.publisher
});

const { value: name, errorMessage: nameError } = useField('name');
const { value: address, errorMessage: addressError } = useField('address');
const { value: phone, errorMessage: phoneError } = useField('phone');

const processing = ref(false);

const submit = handleSubmit(async (values) => {
    processing.value = true;
    try {
        if (props.mode === 'create') {
            await router.post(route('publishers.store'), values);
        } else {
            await router.put(route('publishers.update', props.publisher.id), values);
        }
    } finally {
        processing.value = false;
    }
});
</script>

<template>
    <form @submit.prevent="submit" class="space-y-6">
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

        <div>
            <InputLabel for="address" value="Address" />
            <textarea
                id="address"
                v-model="address"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                rows="3"
            />
            <InputError :message="addressError || errors?.address" class="mt-2" />
        </div>

        <div>
            <InputLabel for="phone" value="Phone" />
            <TextInput
                id="phone"
                v-model="phone"
                type="text"
                class="mt-1 block w-full"
                placeholder="+1 (555) 123-4567"
            />
            <InputError :message="phoneError || errors?.phone" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <PrimaryButton :class="{ 'opacity-25': processing }" :disabled="processing">
                {{ mode === 'create' ? 'Create Publisher' : 'Update Publisher' }}
            </PrimaryButton>
        </div>
    </form>
</template>
