<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PublisherForm from '@/Components/Publishers/PublisherForm.vue';
import Notification from '@/Components/Notification.vue';
import { inject } from 'vue';

const route = inject('route');  

defineProps({
    publisher: Object,
    flash: Object,
});
</script>

<template>
    <Head title="Edit Publisher" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">
                    Edit Publisher
                </h2>
                <Link
                    :href="route('publishers.index')"
                    class="inline-flex items-center px-4 py-2 bg-orange-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    Back to Publishers
                </Link>
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
                    <div class="p-6 bg-white border-b border-gray-200">
                        <PublisherForm
                            :publisher="publisher"
                            mode="edit"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
