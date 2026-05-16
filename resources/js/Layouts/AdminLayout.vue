<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import LoadingScreen from '@/Components/LoadingScreen.vue';
import Toast from '@/Components/Toast.vue';

const page = usePage();
const user = page.props.auth.user;

const navigation = [
    { name: 'Dashboard', href: route('dashboard'), icon: 'dashboard' },
    { name: 'Home Page', href: route('admin.home.edit'), icon: 'home' },
    { name: 'Products', href: route('admin.products.index'), icon: 'products' },
    { name: 'Orders', href: route('admin.orders.index'), icon: 'orders' },
    { name: 'Customers', href: route('admin.customers.index'), icon: 'customers' },
    { name: 'Analytics', href: route('admin.analytics.index'), icon: 'analytics' },
    { name: 'Inventory', href: route('admin.inventory.index'), icon: 'inventory' },
    { name: 'Settings', href: route('admin.settings.index'), icon: 'settings' },
];

const getIcon = (name) => {
    switch (name) {
        case 'dashboard': return 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z';
        case 'home': return 'M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z';
        case 'products': return 'M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z';
        case 'orders': return 'M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z M3 6h18 M16 10a4 4 0 0 1-8 0';
        case 'customers': return 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M9 7a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M23 21v-2a4 4 0 0 0-3-3.87 M16 3.13a4 4 0 0 1 0 7.75';
        case 'analytics': return 'M18 20V10 M12 20V4 M6 20v-6';
        case 'inventory': return 'M12 2L2 7l10 5 10-5-10-5z M2 17l10 5 10-5 M2 12l10 5 10-5';
        case 'messages': return 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z';
        case 'settings': return 'M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.1a2 2 0 0 1-1-1.72v-.51a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z';
        default: return '';
    }
};

const isUrl = (...urls) => {
    let currentUrl = page.url.substr(1);
    if (urls[0] === '') {
        return currentUrl === '';
    }
    return urls.filter((url) => currentUrl.startsWith(url)).length;
};
</script>

<template>
    <div class="min-h-screen font-sans flex text-white bg-admin-main">
        <LoadingScreen />
        <Toast />
        
        <!-- Sidebar -->
        <aside class="w-72 bg-admin-sidebar flex-shrink-0 flex flex-col h-screen sticky top-0 overflow-y-auto border-r border-white/5">
            <div class="p-10 mb-6">
                <Link href="/" class="flex items-center gap-3">
                    <img src="/assets/images/logo-white.png" alt="Shkeel Shoes" class="h-10 brightness-0 invert" />
                </Link>
            </div>

            <nav class="flex-1 px-6 space-y-2">
                <div v-for="item in navigation" :key="item.name">
                    <Link
                        :href="item.href"
                        class="group flex items-center px-4 py-4 text-sm font-medium rounded-2xl transition-all duration-300"
                        :class="[
                            isUrl(item.href.replace(page.props.app_url, ''))
                                ? 'bg-zinc-800/50 text-white shadow-[0_0_20px_rgba(255,255,255,0.05)] border border-white/5'
                                : 'text-zinc-500 hover:text-white hover:bg-white/5'
                        ]"
                    >
                        <svg
                            class="mr-4 flex-shrink-0 h-5 w-5 transition-colors duration-300"
                            :class="[
                                isUrl(item.href.replace(page.props.app_url, ''))
                                    ? 'text-admin-modern'
                                    : 'text-zinc-500 group-hover:text-white'
                            ]"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.5"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" :d="getIcon(item.icon)" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </div>
            </nav>

            <div class="p-6 border-t border-white/5">
                <div class="bg-zinc-900/50 rounded-3xl p-4 flex items-center gap-3 border border-white/5">
                    <div class="w-10 h-10 rounded-full bg-admin-modern flex items-center justify-center font-bold text-black uppercase">
                        {{ user.name.charAt(0) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-bold text-white truncate">{{ user.name }}</p>
                        <p class="text-xs text-zinc-500 truncate">{{ user.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col h-screen overflow-y-auto">
            <!-- Topbar -->
            <header class="h-24 flex items-center justify-between px-10 flex-shrink-0 sticky top-0 bg-admin-main/80 backdrop-blur-xl z-10 border-b border-white/5">
                <div class="flex-1 max-w-xl">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-zinc-500 group-focus-within:text-admin-modern transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input
                            type="text"
                            placeholder="Search everything..."
                            class="block w-full pl-12 pr-4 py-3 bg-zinc-900/50 border border-white/5 text-white placeholder-zinc-500 rounded-2xl focus:ring-2 focus:ring-admin-modern transition-all shadow-sm"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-6">
                    <button class="p-3 bg-zinc-900/50 border border-white/5 text-zinc-400 rounded-2xl shadow-sm hover:text-white hover:bg-zinc-800 transition-all relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="absolute top-3 right-3 w-2 h-2 bg-admin-modern rounded-full border-2 border-zinc-900"></span>
                    </button>

                    <button class="p-3 bg-zinc-900/50 border border-white/5 text-zinc-400 rounded-2xl shadow-sm hover:text-white hover:bg-zinc-800 transition-all">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </button>

                    <Link
                        :href="route('admin.products.create')"
                        class="bg-admin-modern hover:bg-admin-modern/90 text-black px-6 py-3 rounded-2xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-admin-modern/20"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Product
                    </Link>

                    <div class="h-10 w-[1px] bg-white/10 mx-2"></div>

                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-2 group">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center font-bold text-white overflow-hidden border-2 border-white/10 shadow-sm group-hover:border-admin-modern transition-all">
                                    <span v-if="!user.profile_photo_url">{{ user.name.charAt(0) }}</span>
                                    <img v-else :src="user.profile_photo_url" :alt="user.name" class="w-full h-full object-cover" />
                                </div>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <div class="p-10 flex-1">
                <slot />
            </div>
        </main>
    </div>
</template>

<style scoped>
/* Glassmorphism effects */
.bg-admin-sidebar {
    background: #0F0F10;
}
</style>
