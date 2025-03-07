<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import PublisherForm from '@/Components/Publishers/PublisherForm.vue';
import { inject } from 'vue';

const route = inject('route');

</script>

<template>
    <Head title="Create Publisher" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-orange-800 leading-tight">
                    Create Publisher
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
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <PublisherForm mode="create" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
