<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useRegisterForm } from '@/Composables/useRegisterForm';
import { useField, useForm } from 'vee-validate';
import { registerRules } from '@/validation/registerRules';
import { ref, watch, inject} from 'vue';

const route = inject('route');

defineProps({
    roles: {
        type: Array,
        required: true
    }
});

const { form, submit: submitForm } = useRegisterForm();
const { handleSubmit, resetForm } = useForm({
    validationSchema: registerRules,
    initialValues: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: ''
    }
});

const { value: name, errorMessage: nameError } = useField('name');
const { value: email, errorMessage: emailError } = useField('email');
const { value: password, errorMessage: passwordError } = useField('password');
const { value: password_confirmation, errorMessage: passwordConfirmationError } = useField('password_confirmation');
const { value: role_id, errorMessage: roleError } = useField('role_id');

const passwordRequirements = ref({
    minLength: false,
    hasUppercase: false,
    hasLowercase: false,
    hasNumber: false,
    hasSpecial: false
});

watch(password, (newPassword) => {
    if (!newPassword) {
        Object.keys(passwordRequirements.value).forEach(key => {
            passwordRequirements.value[key] = false;
        });
        return;
    }

    passwordRequirements.value = {
        minLength: newPassword.length >= 8,
        hasUppercase: /[A-Z]/.test(newPassword),
        hasLowercase: /[a-z]/.test(newPassword),
        hasNumber: /[0-9]/.test(newPassword),
        hasSpecial: /[!@#$%^&*(),.?":{}|<>]/.test(newPassword)
    };
});

const submit = handleSubmit(async (values) => {
    form.clearErrors();
    form.name = values.name;
    form.email = values.email;
    form.password = values.password;
    form.password_confirmation = values.password_confirmation;
    form.role_id = values.role_id;
    
    await submitForm();
});
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit" novalidate>
            <div>
                <InputLabel for="name" value="Name" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="name"
                    autocomplete="name"
                />
                <InputError :message="nameError || form.errors.name" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="email"
                    autocomplete="username"
                />
                <InputError :message="emailError || form.errors.email" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="password"
                    autocomplete="new-password"
                />
                <InputError :message="passwordError" class="mt-2" />
                
                <!-- Password requirements checklist -->
                <div class="mt-2 text-sm">
                    <p class="font-medium text-gray-700 mb-1">Password must contain:</p>
                    <ul class="space-y-1 text-gray-600">
                        <li :class="{ 'text-green-600': passwordRequirements.minLength }">
                            ✓ At least 8 characters
                        </li>
                        <li :class="{ 'text-green-600': passwordRequirements.hasUppercase }">
                            ✓ At least one uppercase letter
                        </li>
                        <li :class="{ 'text-green-600': passwordRequirements.hasLowercase }">
                            ✓ At least one lowercase letter
                        </li>
                        <li :class="{ 'text-green-600': passwordRequirements.hasNumber }">
                            ✓ At least one number
                        </li>
                        <li :class="{ 'text-green-600': passwordRequirements.hasSpecial }">
                            ✓ At least one special character
                        </li>
                    </ul>
                </div>
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="password_confirmation"
                    autocomplete="new-password"
                />
                <InputError :message="passwordConfirmationError" class="mt-2" />
            </div>

            <div class="mt-4">
                <InputLabel for="role" value="Role" />
                <select
                    id="role"
                    v-model="role_id"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
                >
                    <option value="">Select a role</option>
                    <option v-for="role in roles" :key="role.id" :value="role.id">
                        {{ role.name }}
                    </option>
                </select>
                <InputError :message="roleError || form.errors.role_id" class="mt-2" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link
                    :href="route('login')"
                    class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Already registered?
                </Link>

                <PrimaryButton class="ml-4" :disabled="form.processing">
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
