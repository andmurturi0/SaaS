<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const props = defineProps({
    auth: Object,
});

const searchQuery = ref('');

const performSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(route('shop.index'), { search: searchQuery.value });
    }
};

const isScrolled = ref(false);
const scrollProgress = ref(0);
const activeSection = ref('home');

const handleScroll = () => {
    const scrollY = window.scrollY;
    isScrolled.value = scrollY > 50;
    scrollProgress.value = Math.min(scrollY / 200, 1);
    
    // Only detect sections if we are on the home page
    if (route().current('home')) {
        const sections = ['home', 'brands', 'categories', 'about', 'contact'];
        for (const section of sections) {
            const el = document.getElementById(section);
            if (el) {
                const rect = el.getBoundingClientRect();
                if (rect.top <= 150 && rect.bottom >= 150) {
                    activeSection.value = section;
                    break;
                }
            }
        }
    }
};

const navLinks = [
    { name: 'Home', id: 'home', type: 'route' },
    { name: 'Shop', id: 'shop.index', type: 'route' },
    { name: 'Brands', id: 'brands', type: 'anchor' },
    { name: 'Categories', id: 'categories', type: 'anchor' },
    { name: 'About us', id: 'about', type: 'anchor' },
];

onMounted(() => {
    window.addEventListener('scroll', handleScroll);
});

onUnmounted(() => {
    window.removeEventListener('scroll', handleScroll);
});

const isLinkActive = (link) => {
    if (link.type === 'route') {
        return route().current(link.id);
    }
    if (link.type === 'anchor' && route().current('home')) {
        return activeSection.value === link.id;
    }
    return false;
};

const getHref = (link) => {
    if (link.type === 'route') return route(link.id);
    if (route().current('home')) return '#' + link.id;
    return route('home') + '#' + link.id;
};
</script>

<template>
    <nav 
        :class="[
            'fixed top-0 w-full z-50 transition-all duration-700 flex flex-col items-center px-10',
            isScrolled ? 'py-2 bg-black/90 backdrop-blur-xl border-b border-white/5' : 'py-10'
        ]"
    >
        <!-- Logo Section -->
        <div 
            class="w-full max-w-7xl mx-auto flex relative transition-all duration-300"
            :style="{ 
                height: (128 - scrollProgress * 96) + 'px',
                marginBottom: isScrolled ? '-2.5rem' : '1.5rem'
            }"
        >
            <Link :href="route('home')" class="absolute top-0 transition-all duration-100 ease-out"
                :style="{
                    left: (50 - scrollProgress * 50) + '%',
                    transform: `translateX(-${50 - scrollProgress * 50}%) scale(${1 - scrollProgress * 0.75})`,
                    transformOrigin: 'left top',
                    height: '128px'
                }"
            >
                <img src="/assets/images/logo-white.png" alt="Logo" class="h-full" />
            </Link>
        </div>

        <!-- Links Section -->
        <div 
            class="flex items-center justify-between w-full max-w-7xl mx-auto transition-all duration-500"
            :class="[isScrolled ? 'pl-40' : '']"
        >
            <!-- Navigation links -->
            <div class="flex items-center space-x-1 bg-zinc-900/40 rounded-full p-1 border border-white/5 backdrop-blur-sm">
                <template v-for="link in navLinks" :key="link.name">
                    <Link
                        v-if="link.type === 'route' || !route().current('home')"
                        :href="getHref(link)"
                        :class="[
                            'px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-all duration-500 relative z-10 whitespace-nowrap',
                            isLinkActive(link) ? 'text-black' : 'text-zinc-400 hover:text-white'
                        ]"
                    >
                        <span class="relative z-10">{{ link.name }}</span>
                        <div 
                            v-if="isLinkActive(link)" 
                            class="absolute inset-0 bg-white rounded-full z-0 transition-all duration-500 shadow-[0_0_15px_rgba(255,255,255,0.3)]"
                        ></div>
                    </Link>
                    <a 
                        v-else
                        :href="'#' + link.id"
                        :class="[
                            'px-5 py-2 rounded-full text-xs font-bold uppercase tracking-widest transition-all duration-500 relative z-10 whitespace-nowrap',
                            isLinkActive(link) ? 'text-black' : 'text-zinc-400 hover:text-white'
                        ]"
                    >
                        <span class="relative z-10">{{ link.name }}</span>
                        <div 
                            v-if="isLinkActive(link)" 
                            class="absolute inset-0 bg-white rounded-full z-0 transition-all duration-500 shadow-[0_0_15px_rgba(255,255,255,0.3)]"
                        ></div>
                    </a>
                </template>
            </div>

            <!-- Right Side: Search, Cart, Profile -->
            <div class="flex items-center space-x-5">
                <!-- Search -->
                <div class="relative flex items-center group">
                    <input 
                        type="text" 
                        v-model="searchQuery"
                        @keyup.enter="performSearch"
                        placeholder="Search..." 
                        class="bg-zinc-900/50 border border-white/5 rounded-full py-2 pl-10 pr-4 text-xs w-32 focus:w-44 transition-all duration-500 focus:ring-1 focus:ring-white/20 placeholder:text-zinc-600 text-white"
                    />
                    <svg class="w-4 h-4 absolute left-3.5 text-zinc-500 group-focus-within:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <!-- Cart -->
                <Link :href="route('cart.index')" class="p-2.5 hover:bg-zinc-800 rounded-full transition-all duration-300 relative group border border-transparent hover:border-white/5">
                    <svg class="w-5 h-5 text-zinc-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span v-if="$page.props.cart_count > 0" class="absolute top-1 right-1 bg-white text-black text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center shadow-lg">{{ $page.props.cart_count }}</span>
                </Link>

                <!-- Wishlist -->
                <Link :href="route('favorites.index')" :class="[route().current('favorites.index') ? 'bg-zinc-800 border-white/10' : 'border-transparent']" class="p-2.5 hover:bg-zinc-800 rounded-full transition-all duration-300 group border hover:border-white/5">
                    <svg class="w-5 h-5 text-zinc-400 group-hover:text-red-500 transition-colors" :class="{'text-red-500': route().current('favorites.index')}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </Link>

                <!-- Profile -->
                <div class="flex items-center">
                    <template v-if="auth.user">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center space-x-2 bg-zinc-900/50 border border-white/5 hover:bg-zinc-800 px-4 py-1.5 rounded-full transition-all duration-300">
                                    <span class="text-xs font-bold tracking-wider uppercase">{{ auth.user.name }}</span>
                                    <div class="w-6 h-6 bg-white text-black rounded-full flex items-center justify-center font-black text-[10px]">
                                        {{ auth.user.name.charAt(0) }}
                                    </div>
                                </button>
                            </template>
                            <template #content>
                                <DropdownLink v-if="auth.user.role === 'admin'" :href="route('dashboard')"> Dashboard </DropdownLink>
                                <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                <DropdownLink :href="route('orders.index')"> Orders </DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button"> Log Out </DropdownLink>
                            </template>
                        </Dropdown>
                    </template>
                    <template v-else>
                        <Link :href="route('login')" class="text-[10px] font-black tracking-wider uppercase bg-white text-black px-6 py-2 rounded-full hover:bg-zinc-200 transition-all">Login</Link>
                    </template>
                </div>
            </div>
        </div>
    </nav>
</template>
