<script setup>
import { ref, inject } from 'vue';
import { Link } from '@inertiajs/vue3';

const route = inject('route');


const isOpen = ref(true);
const isMobileMenuOpen = ref(false);

const toggleSidebar = () => {
    isOpen.value = !isOpen.value;
};

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

defineProps({
    auth: {
        type: Object,
        required: true
    }
});

const menuItems = [
    { 
        name: 'Dashboard', 
        icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6', 
        route: 'dashboard' 
    },
    { 
        name: 'Books', 
        icon: 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253', 
        route: 'books.index' 
    },
    { 
        name: 'Authors',
        icon: 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        route: 'authors.index',
        requiresLibrarian: true
    },
    {
        name: 'Categories',
        icon: 'M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 010 3.75H5.625a1.875 1.875 0 010-3.75z',
        route: 'categories.index',
        requiresLibrarian: true
    },
    {
        name: 'Publishers',
        icon: 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0012 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75z',
        route: 'publishers.index',
        requiresLibrarian: true
    }
];
</script>

<template>
    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button @click="toggleMobileMenu" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Mobile Sidebar -->
    <div v-show="isMobileMenuOpen" 
         class="fixed inset-0 z-40 lg:hidden"
         @click="isMobileMenuOpen = false">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        
        <div class="fixed inset-y-0 left-0 flex flex-col w-64 bg-white border-r border-gray-200">
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                <span class="text-lg font-semibold">Menu</span>
                <button @click="isMobileMenuOpen = false" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 px-2 py-4 space-y-1">
                <template v-for="item in menuItems" :key="item.name">
                    <Link v-if="!item.requiresLibrarian || (auth.user && auth.user.role === 'librarian')"
                          :href="route(item.route)"
                          :class="[
                              route().current(item.route) 
                                  ? 'bg-gray-100 text-gray-900' 
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                              'group flex items-center px-2 py-2 text-base font-medium rounded-md'
                          ]">
                        <svg class="mr-3 h-6 w-6" 
                             :class="route().current(item.route) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500'"
                             fill="none" 
                             viewBox="0 0 24 24" 
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </template>
            </nav>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:flex">
        <div :class="[isOpen ? 'w-64' : 'w-20', 'transition-width duration-300 ease-in-out fixed inset-y-0 left-0 z-30']">
            <div class="flex flex-col h-full bg-white border-r border-gray-200">
                <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
                    <span v-show="isOpen" class="text-lg font-semibold">Menu</span>
                    <button @click="toggleSidebar" class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="isOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                        </svg>
                    </button>
                </div>
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <template v-for="item in menuItems" :key="item.name">
                        <Link v-if="!item.requiresLibrarian || (auth.user && auth.user.role === 'librarian')"
                              :href="route(item.route)"
                              :class="[
                                  route().current(item.route) 
                                      ? 'bg-gray-100 text-gray-900' 
                                      : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                  'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                              ]">
                            <svg class="mr-3 h-6 w-6" 
                                 :class="route().current(item.route) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500'"
                                 fill="none" 
                                 viewBox="0 0 24 24" 
                                 stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="item.icon" />
                            </svg>
                            <span v-show="isOpen">{{ item.name }}</span>
                        </Link>
                    </template>
                </nav>
            </div>
        </div>
    </div>
</template>

<style scoped>
.transition-width {
    transition-property: width;
}
</style>
