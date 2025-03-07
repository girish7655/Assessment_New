import { useForm } from '@inertiajs/vue3';

export function useRegisterForm() {
    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: null,
    });

    const submit = () => {
        form.post(route('register'), {
            preserveScroll: true,
        });
    };

    return {
        form,
        submit
    };
}
