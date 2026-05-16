<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import Navbar from '@/Components/Navbar.vue';

const props = defineProps({
    heroProducts: Array,
    brands: Array,
    categories: Array,
    popularProducts: Array,
    newArrivals: Array,
    aboutUs: Object,
    auth: Object,
});

const toggleFavorite = (product) => {
    if (!props.auth.user) {
        router.visit(route('login'));
        return;
    }

    if (!product.id) return;

    router.post(route('favorites.toggle', product.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // product.is_favorited will be updated by the page reload if it's part of the props
            // If it's local state, we might need to manually update it or rely on re-fetching
        }
    });
};

const currentHeroIndex = ref(0);

const nextHero = () => {
    if (props.heroProducts && props.heroProducts.length > 0) {
        currentHeroIndex.value = (currentHeroIndex.value + 1) % props.heroProducts.length;
    }
};

onMounted(() => {
    const heroInterval = setInterval(nextHero, 5000);
    onUnmounted(() => {
        clearInterval(heroInterval);
    });
});
</script>

<template>
    <Head title="Home" />

    <div class="min-h-screen bg-black text-white font-sans overflow-x-hidden scroll-smooth selection:bg-white selection:text-black">
        <Navbar :auth="auth" />

        <!-- Main Content -->
        <main>
            <!-- Hero Section -->
            <section id="home" class="min-h-screen flex items-center justify-center pt-80 pb-20">
                <div class="relative w-full max-w-7xl mx-auto px-10">
                    <div v-if="heroProducts && heroProducts.length > 0" class="bg-[#1a1a1a] rounded-[2rem] p-16 flex flex-col relative overflow-hidden h-[600px] border border-white/5 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)]">
                        
                        <!-- Top Bar: Brand and Wishlist -->
                        <div class="flex justify-between items-start z-30 w-full mb-4">
                            <!-- Brands -->
                            <div class="flex flex-col space-y-4">
                                <template v-if="heroProducts[currentHeroIndex].brands && heroProducts[currentHeroIndex].brands.length > 0">
                                    <Link 
                                        v-for="brand in heroProducts[currentHeroIndex].brands" 
                                        :key="brand.id"
                                        :href="route('shop.index', { brand: brand.name })"
                                        class="group cursor-pointer transition-all w-16 h-10 flex items-center justify-start"
                                    >
                                        <img 
                                            :src="brand.logo" 
                                            class="h-full w-full object-contain opacity-80 group-hover:opacity-100 transition-opacity drop-shadow-md"
                                        />
                                    </Link>
                                </template>
                                <div v-else class="w-16 h-16 bg-white/5 backdrop-blur-md rounded-full flex items-center justify-center border border-white/10">
                                    <span class="text-[8px] font-black uppercase text-zinc-500">*BRAND*</span>
                                </div>
                            </div>

                            <!-- Wishlist -->
                            <button 
                                @click="toggleFavorite(heroProducts[currentHeroIndex])"
                                :class="[
                                    'w-12 h-12 flex items-center justify-center transition-all duration-300',
                                    heroProducts[currentHeroIndex].is_favorited ? 'text-red-500 scale-110' : 'text-zinc-400 hover:text-white'
                                ]"
                            >
                                <svg class="w-8 h-8" :fill="heroProducts[currentHeroIndex].is_favorited ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Main Content Area -->
                        <div class="flex-1 flex items-center relative">
                            <!-- Left Info: Price, Title, Category -->
                            <div class="w-1/3 space-y-4 z-20">
                                <Transition name="fade-up" mode="out-in">
                                    <div :key="currentHeroIndex" class="space-y-4">
                                        <!-- Price Box -->
                                        <div class="inline-block bg-white/10 backdrop-blur-xl border border-white/10 px-10 py-6 rounded-[2rem] shadow-2xl min-w-[150px] text-center">
                                            <span class="text-4xl font-black text-white tracking-tighter">
                                                {{ heroProducts[currentHeroIndex].price.toString().startsWith('*') ? heroProducts[currentHeroIndex].price : '€' + heroProducts[currentHeroIndex].price }}
                                            </span>
                                        </div>
                                        
                                        <!-- Title -->
                                        <div class="bg-white/5 backdrop-blur-sm border border-white/5 px-6 py-3 rounded-2xl inline-block">
                                            <h2 class="text-xl font-black text-white/90 uppercase tracking-tight italic">
                                                {{ heroProducts[currentHeroIndex].title }}
                                            </h2>
                                        </div>

                                        <!-- Category -->
                                        <div class="px-2">
                                            <p class="text-zinc-500 text-xs font-black uppercase tracking-[0.3em]">
                                                {{ heroProducts[currentHeroIndex].category }}
                                            </p>
                                        </div>
                                    </div>
                                </Transition>
                            </div>

                            <!-- Center: Product Image -->
                            <div class="flex-1 flex justify-center items-center relative h-full">
                                <!-- Background Glow -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-[450px] h-[450px] bg-white/5 rounded-full blur-[80px]"></div>
                                </div>
                                
                                <TransitionGroup name="hero-slide">
                                    <div 
                                        v-for="(product, index) in heroProducts" 
                                        :key="index"
                                        v-show="currentHeroIndex === index"
                                        class="absolute inset-0 flex flex-col items-center justify-center"
                                    >
                                        <template v-if="product.image">
                                            <img 
                                                :src="product.image" 
                                                :alt="product.title" 
                                                class="max-h-[400px] w-auto object-contain relative z-10 transform -rotate-[10deg] drop-shadow-[0_30px_30px_rgba(0,0,0,0.7)]" 
                                            />
                                        </template>
                                        <template v-else>
                                            <!-- Placeholder Image Box like in hero.png -->
                                            <div class="w-[450px] h-[350px] bg-zinc-900/50 border-2 border-white/10 rounded-[3rem] flex flex-col items-center justify-center space-y-6 relative z-10 shadow-2xl">
                                                <div class="absolute inset-4 border border-white/5 rounded-[2.5rem] pointer-events-none"></div>
                                                <svg class="w-24 h-24 text-white opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white opacity-40">*PHOTO/IMAGE OF THE PRODUKT*</span>
                                            </div>
                                        </template>
                                        <!-- Reflection/Shadow -->
                                        <div class="w-2/3 h-10 bg-black/60 blur-2xl rounded-[100%] mt-[-20px] opacity-80"></div>
                                    </div>
                                </TransitionGroup>
                            </div>
                        </div>

                        <!-- Bottom Right: Button -->
                        <div class="absolute bottom-16 right-16 z-30">
                            <button class="bg-white text-black px-10 py-4 rounded-2xl font-black uppercase tracking-tight text-xs hover:bg-zinc-200 transition-all hover:scale-105 active:scale-95 shadow-2xl">
                                Check the shop
                            </button>
                        </div>
                    </div>

                    <!-- Slider Dots (Outside) -->
                    <div class="flex items-center justify-center space-x-3 mt-8">
                        <button 
                            v-for="(dot, index) in heroProducts" 
                            :key="index"
                            @click="currentHeroIndex = index"
                            :class="[
                                'transition-all duration-500 rounded-full',
                                currentHeroIndex === index ? 'w-10 h-1.5 bg-white shadow-[0_0_15px_rgba(255,255,255,0.8)]' : 'w-1.5 h-1.5 bg-zinc-600 hover:bg-zinc-400'
                            ]"
                        ></button>
                    </div>
                </div>
            </section>

            <!-- Promo Banners Section -->
            <section class="py-10 px-10 max-w-7xl mx-auto space-y-6">
                <!-- New Releases Banner -->
                <Link 
                    href="/shop?sort=newest" 
                    class="relative block w-full h-52 rounded-[2.5rem] overflow-hidden group border border-white/5 hover:border-white/20 transition-all duration-700 shadow-2xl"
                >
                    <img 
                        src="/assets/images/promo/new-releases.jpg" 
                        class="absolute inset-0 w-full h-full object-cover grayscale-[0.5] group-hover:grayscale-0 group-hover:scale-110 transition-all duration-1000 opacity-60 group-hover:opacity-100"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/40 to-transparent"></div>
                    <div class="relative h-full flex flex-col justify-center px-16 z-20">
                        <h2 class="text-7xl font-black text-white italic uppercase tracking-tighter leading-none group-hover:translate-x-4 transition-transform duration-700">
                            New Releases
                        </h2>
                    </div>
                </Link>

                <!-- Explore Shop Banner -->
                <Link 
                    href="/shop" 
                    class="relative block w-full h-52 rounded-[2.5rem] overflow-hidden group border border-white/5 hover:border-white/20 transition-all duration-700 shadow-2xl"
                >
                    <img 
                        src="/assets/images/promo/explore-shop.jpg" 
                        class="absolute inset-0 w-full h-full object-cover grayscale-[0.5] group-hover:grayscale-0 group-hover:scale-110 transition-all duration-1000 opacity-60 group-hover:opacity-100"
                    />
                    <div class="absolute inset-0 bg-gradient-to-r from-black via-black/40 to-transparent"></div>
                    <div class="relative h-full flex flex-col justify-center px-16 z-20">
                        <h2 class="text-7xl font-black text-white italic uppercase tracking-tighter leading-none group-hover:translate-x-4 transition-transform duration-700 mb-2">
                            Explore the Best Shop
                        </h2>
                        <p class="text-zinc-400 font-bold uppercase tracking-widest text-[10px] group-hover:translate-x-6 transition-transform duration-1000 delay-75">
                            High quality, popular choices, unique style...
                        </p>
                    </div>
                </Link>
            </section>

            <section id="brands" class="py-32 px-10 max-w-7xl mx-auto text-center">
                <h2 class="text-6xl font-black mb-20 tracking-tighter text-white uppercase italic">Everything You Need</h2>
                <div class="flex justify-center items-center gap-12 flex-wrap">
                    <Link 
                        v-for="brand in brands" 
                        :key="brand.id"
                        :href="route('shop.index', { brand: brand.name })"
                        class="group cursor-pointer flex flex-col items-center transition-all duration-500 hover:-translate-y-4"
                    >
                        <div class="w-28 h-28 flex items-center justify-center transition-all duration-500 group-hover:scale-110 mb-6 bg-zinc-900/30 rounded-3xl border border-white/5 group-hover:border-white/10">
                            <img :src="brand.logo" class="h-12 w-20 object-contain opacity-40 group-hover:opacity-100 transition-all duration-500 grayscale group-hover:grayscale-0 drop-shadow-2xl" />
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-[0.4em] text-zinc-600 group-hover:text-white transition-colors">{{ brand.name }}</span>
                    </Link>
                </div>
            </section>

            <!-- Categories & Join Us Section -->
            <section id="categories" class="py-20 px-10 max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-12">
                    <h2 class="text-5xl font-black tracking-tight uppercase italic">Categories</h2>
                    <h2 class="text-5xl font-black tracking-tight uppercase italic text-zinc-800">Join Us</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Men Category -->
                    <Link 
                        :href="route('shop.index', { category: 'Men' })"
                        class="bg-zinc-900 rounded-[2rem] overflow-hidden relative group cursor-pointer border border-white/5 hover:border-white/20 transition-all duration-700 h-[500px] shadow-2xl"
                    >
                        <div class="p-8 relative z-20 h-full flex flex-col">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 mb-6 text-center border border-white/5">
                                <h3 class="text-4xl font-black italic tracking-tighter">Men</h3>
                            </div>
                            <div class="bg-zinc-800 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center space-x-3 w-fit group-hover:bg-white group-hover:text-black transition-all duration-500 group-hover:scale-105">
                                <span>CHECK NOW</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"/></svg>
                            </div>
                        </div>
                        <div class="absolute inset-0 z-10 overflow-hidden">
                            <img src="/assets/images/categories/men.png" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-110" />
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-15"></div>
                    </Link>

                    <!-- Women Category -->
                    <Link 
                        :href="route('shop.index', { category: 'Women' })"
                        class="bg-zinc-900 rounded-[2rem] overflow-hidden relative group cursor-pointer border border-white/5 hover:border-white/20 transition-all duration-700 h-[500px] shadow-2xl"
                    >
                        <div class="p-8 relative z-20 h-full flex flex-col">
                            <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 mb-6 text-center border border-white/5">
                                <h3 class="text-4xl font-black italic tracking-tighter">Women</h3>
                            </div>
                            <div class="bg-zinc-800 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest flex items-center space-x-3 w-fit group-hover:bg-white group-hover:text-black transition-all duration-500 group-hover:scale-105">
                                <span>CHECK NOW</span>
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"/></svg>
                            </div>
                        </div>
                        <div class="absolute inset-0 z-10 overflow-hidden">
                            <img src="/assets/images/categories/women.png" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-1000 group-hover:scale-110" />
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent z-15"></div>
                    </Link>

                    <!-- Join Us Column -->
                    <div class="bg-zinc-900 rounded-[2rem] overflow-hidden relative group border border-white/5 h-[500px] shadow-2xl flex flex-col p-10 justify-between">
                        <!-- Grainy Background -->
                        <div class="absolute inset-0 opacity-20 pointer-events-none">
                            <img src="/assets/images/joinus-bg.png" class="w-full h-full object-cover" />
                        </div>
                        
                        <div class="relative z-20 space-y-6">
                            <h2 class="text-4xl font-black italic uppercase leading-none tracking-tighter">Membership benefits</h2>
                            <ul class="space-y-4 text-zinc-400 font-bold text-xs uppercase tracking-tight">
                                <li class="flex items-start space-x-3">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full mt-1 shrink-0"></span>
                                    <span>Free Shipping: On every order, no minimum.</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full mt-1 shrink-0"></span>
                                    <span>Member Exclusive: Access to limited-edition drops.</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full mt-1 shrink-0"></span>
                                    <span>Early Access: Shop new arrivals before everyone else.</span>
                                </li>
                                <li class="flex items-start space-x-3">
                                    <span class="w-1.5 h-1.5 bg-white rounded-full mt-1 shrink-0"></span>
                                    <span>Birthday Reward: A special gift just for your day.</span>
                                </li>
                            </ul>
                        </div>

                        <div class="relative z-20 flex justify-between items-end">
                            <Link :href="route('register')" class="bg-white text-black px-10 py-4 rounded-full font-black uppercase tracking-widest text-[10px] hover:bg-zinc-200 transition-all shadow-2xl active:scale-95">
                                Register
                            </Link>
                            <img src="/assets/images/logo-white.png" class="h-6 opacity-80" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Popular Styles -->
            <section class="py-24 px-10 max-w-7xl mx-auto">
                <div class="flex justify-between items-end mb-20">
                    <div>
                        <h2 class="text-7xl font-black uppercase italic leading-none mb-6 tracking-tighter">Popular styles</h2>
                        <div class="h-2 w-64 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full w-1/3 bg-white animate-pulse"></div>
                        </div>
                    </div>
                    <div class="flex space-x-4">
                        <button class="w-14 h-14 border-2 border-white/5 rounded-full flex items-center justify-center hover:bg-white hover:text-black transition-all duration-500 shadow-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>
                        <button class="w-14 h-14 border-2 border-white/5 rounded-full flex items-center justify-center hover:bg-white hover:text-black transition-all duration-500 shadow-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>
                <div class="grid grid-cols-5 gap-10">
                    <div v-for="product in popularProducts" :key="product.id" class="space-y-8 group cursor-pointer relative">
                        <!-- Discount Badge -->
                        <div v-if="product.sale_price && parseFloat(product.sale_price) < parseFloat(product.price)" class="absolute top-6 left-6 z-20 bg-white text-black px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest shadow-2xl">
                            -{{ Math.round((1 - parseFloat(product.sale_price) / parseFloat(product.price)) * 100) }}%
                        </div>

                        <!-- Favorite Button -->
                        <button 
                            @click.stop="toggleFavorite(product)"
                            :class="[
                                'absolute top-6 right-6 z-20 transition-all duration-300',
                                product.is_favorited ? 'text-red-500 scale-110' : 'text-zinc-500 hover:text-white opacity-0 group-hover:opacity-100'
                            ]"
                        >
                            <svg class="w-6 h-6" :fill="product.is_favorited ? 'currentColor' : 'none'" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>

                        <div class="aspect-square bg-zinc-900 rounded-[3rem] p-12 flex items-center justify-center relative overflow-hidden border border-white/5 group-hover:border-white/20 transition-all duration-700 shadow-2xl">
                            <img src="/assets/images/popular-bg.png" class="absolute inset-0 w-full h-full object-cover opacity-10 group-hover:opacity-30 transition-all duration-700 scale-150 group-hover:rotate-12" />
                            <img :src="product.image" class="h-44 object-contain relative z-10 transition-all duration-1000 group-hover:scale-110 group-hover:-rotate-12 drop-shadow-[0_20px_20px_rgba(0,0,0,0.5)]" />
                        </div>
                        <div class="px-4 space-y-1">
                            <div class="flex justify-between items-start">
                                <h4 class="font-black text-2xl group-hover:text-white transition-colors text-white/90 italic uppercase tracking-tighter">{{ product.name }}</h4>
                                <span class="text-zinc-600 font-black text-[8px] uppercase tracking-widest italic text-right leading-tight mt-2">
                                    {{ product.brands?.map(b => b.name).join(', ') }}
                                </span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <p class="text-white/90 font-black text-xl tracking-widest">€{{ Math.round(product.sale_price || product.price) }}</p>
                                <p v-if="product.sale_price && parseFloat(product.sale_price) < parseFloat(product.price)" class="text-zinc-700 line-through font-black text-sm tracking-widest">€{{ Math.round(product.price) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About Us -->
            <section id="about" class="py-32 px-10 max-w-7xl mx-auto flex items-center gap-28">
                <!-- Left: Image Container -->
                <div class="flex-1 h-[550px] relative group flex items-center justify-center">
                    <!-- Outer Decorative Frame (visible always) -->
                    <div class="absolute inset-0 border-[3px] border-white/10 rounded-[4rem] pointer-events-none z-20"></div>
                    
                    <!-- Image Wrapper -->
                    <div class="w-[92%] h-[92%] rounded-[3rem] overflow-hidden flex items-center justify-center relative bg-zinc-900/50">
                        <!-- Instagram-style icon/placeholder when no image -->
                        <div v-if="!aboutUs?.image" class="absolute inset-0 flex flex-col items-center justify-center space-y-4 opacity-40">
                             <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                             </svg>
                             <span class="text-[10px] font-black uppercase tracking-[0.2em] text-white">*PHOTO/IMAGE OF THE PRODUKT*</span>
                        </div>

                        <img 
                            :src="aboutUs?.image || '/assets/images/joinus.jpg'" 
                            class="max-w-full max-h-full object-contain grayscale transition-all duration-[1.5s] group-hover:grayscale-0 group-hover:scale-105 relative z-10" 
                        />
                        
                        <!-- Gradient Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent z-15"></div>
                    </div>
                </div>

                <!-- Right: Content -->
                <div class="flex-1 space-y-10 relative">
                    <!-- Discover Badge -->
                    <div class="flex justify-end mb-4">
                        <div class="inline-flex items-center px-6 py-2 border border-white/20 rounded-full text-[10px] font-black uppercase tracking-[0.2em] bg-white/5 backdrop-blur-sm hover:bg-white hover:text-black transition-all cursor-default">
                            <span>Discover our story</span>
                        </div>
                    </div>

                    <!-- Titles -->
                    <div class="space-y-2">
                        <h2 class="text-7xl font-black uppercase leading-[0.9] tracking-tighter italic text-white">
                            {{ aboutUs?.title_line_1 || aboutUs?.title || '*WRITE YOUR' }}
                        </h2>
                        <h2 v-if="aboutUs?.title_line_2 || aboutUs?.subtitle" class="text-7xl font-black uppercase leading-[0.9] tracking-tighter italic text-zinc-700">
                            {{ aboutUs?.title_line_2 || aboutUs?.subtitle }}
                        </h2>
                        <h2 v-else class="text-7xl font-black uppercase leading-[0.9] tracking-tighter italic text-zinc-700">
                            STORY LINE*
                        </h2>
                    </div>

                    <!-- Description -->
                    <p class="text-white text-base font-bold italic tracking-tight uppercase max-w-lg">
                        {{ aboutUs?.description || '*WRITE YOUR STORY OF YOUR BRAND BE YOURSELF*' }}
                    </p>

                    <!-- Divider -->
                    <div class="w-full h-px bg-white/10"></div>

                    <!-- Stats -->
                    <div class="flex gap-16">
                        <div v-for="stat in (aboutUs?.stats || [{label: 'Years in treg', value: '*00*'}, {label: 'Pleasing Costumers', value: '*00*'}])" :key="stat.label">
                            <div class="text-5xl font-black mb-1 italic tracking-tighter text-white">{{ stat.value }}</div>
                            <div class="text-zinc-500 font-bold uppercase tracking-tight text-[11px]">{{ stat.label }}</div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <button class="bg-white text-black px-10 py-4 rounded-xl font-black uppercase tracking-tight text-xs hover:bg-zinc-200 transition-all shadow-2xl active:scale-95">
                        Learn More About Us
                    </button>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-zinc-900/30 mt-32 pt-32 pb-16 border-t border-white/5 relative overflow-hidden backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-10 relative z-10">
                <div class="grid grid-cols-4 gap-20 mb-32">
                    <div class="space-y-8">
                        <img src="/assets/images/logo-white.png" class="h-10" />
                        <p class="text-zinc-500 font-bold uppercase tracking-widest text-[10px] leading-relaxed">Crafting the future of footwear with passion and precision.</p>
                    </div>
                    <div>
                        <h4 class="font-black mb-10 text-zinc-700 uppercase text-[11px] tracking-[0.4em]">NAV</h4>
                        <ul class="space-y-4 text-xs font-black uppercase tracking-widest text-zinc-500">
                            <li><a href="#home" class="hover:text-white transition-all duration-300 flex items-center space-x-2 group"><span class="w-0 group-hover:w-4 h-0.5 bg-white transition-all"></span><span>Home</span></a></li>
                            <li><a href="#brands" class="hover:text-white transition-all duration-300 flex items-center space-x-2 group"><span class="w-0 group-hover:w-4 h-0.5 bg-white transition-all"></span><span>Brands</span></a></li>
                            <li><a href="#categories" class="hover:text-white transition-all duration-300 flex items-center space-x-2 group"><span class="w-0 group-hover:w-4 h-0.5 bg-white transition-all"></span><span>Categories</span></a></li>
                            <li><a href="#about" class="hover:text-white transition-all duration-300 flex items-center space-x-2 group"><span class="w-0 group-hover:w-4 h-0.5 bg-white transition-all"></span><span>About Us</span></a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-black mb-10 text-zinc-700 uppercase text-[11px] tracking-[0.4em]">HELP</h4>
                        <ul class="space-y-4 text-xs font-black uppercase tracking-widest text-zinc-500">
                            <li><a href="#" class="hover:text-white transition-all duration-300 flex items-center space-x-2 group"><span class="w-0 group-hover:w-4 h-0.5 bg-white transition-all"></span><span>Delivery</span></a></li>
                            <li><a href="#" class="hover:text-white transition-all duration-300 flex items-center space-x-2 group"><span class="w-0 group-hover:w-4 h-0.5 bg-white transition-all"></span><span>Returns</span></a></li>
                            <li><a href="#" class="hover:text-white transition-all duration-300 flex items-center space-x-2 group"><span class="w-0 group-hover:w-4 h-0.5 bg-white transition-all"></span><span>Contact</span></a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-black mb-10 text-zinc-700 uppercase text-[11px] tracking-[0.4em]">MEMBERSHIP</h4>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-8 leading-relaxed">Join our community and get 10% off your first order</p>
                        <div class="relative group">
                            <input type="email" placeholder="Sign up here" class="bg-black/50 border border-white/5 rounded-2xl py-4 px-6 w-full text-xs font-bold uppercase tracking-widest focus:ring-1 focus:ring-white/20 transition-all" />
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 group-hover:translate-x-2 transition-transform">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-center space-y-12">
                    <h3 class="text-6xl font-black italic uppercase tracking-tighter text-white/90">"Beyond Every Limit."</h3>
                    <div class="flex justify-center space-x-10">
                        <a href="#" class="w-14 h-14 rounded-full border-2 border-white/5 flex items-center justify-center hover:bg-white hover:text-black hover:border-white transition-all duration-500 group shadow-xl">
                            <span class="font-black text-xl group-hover:scale-125 transition-transform">F</span>
                        </a>
                        <a href="#" class="w-14 h-14 rounded-full border-2 border-white/5 flex items-center justify-center hover:bg-white hover:text-black hover:border-white transition-all duration-500 group shadow-xl">
                            <span class="font-black text-xl group-hover:scale-125 transition-transform">I</span>
                        </a>
                        <a href="#" class="w-14 h-14 rounded-full border-2 border-white/5 flex items-center justify-center hover:bg-white hover:text-black hover:border-white transition-all duration-500 group shadow-xl">
                            <span class="font-black text-xl group-hover:scale-125 transition-transform">X</span>
                        </a>
                    </div>
                    <div class="pt-10 border-t border-white/5">
                        <div class="text-zinc-700 text-[10px] font-black uppercase tracking-[0.5em]">
                            © 2026 And Murturi. All rights reserved. | Privacy Policy | Terms of Use
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Big background logo text in footer -->
            <div class="absolute bottom-[-20%] left-1/2 -translate-x-1/2 opacity-[0.03] select-none pointer-events-none w-full">
                <img src="/assets/images/logo-white.png" class="w-full grayscale transform scale-150" />
            </div>
        </footer>
    </div>
</template>

<style scoped>
html {
    scroll-behavior: smooth;
}

.font-sans {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
}

.outline-text {
    -webkit-text-stroke: 1.5px #18181b;
    color: transparent;
}

.hero-slide-enter-active,
.hero-slide-leave-active {
    transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
}

.hero-slide-enter-from {
    opacity: 0;
    transform: rotate(-30deg) translateX(200px) scale(0.8);
    filter: blur(20px);
}

.hero-slide-leave-to {
    opacity: 0;
    transform: rotate(20deg) translateX(-200px) scale(1.2);
    filter: blur(20px);
}

.fade-up-enter-active,
.fade-up-leave-active {
    transition: all 0.8s ease-out;
}

.fade-up-enter-from {
    opacity: 0;
    transform: translateY(40px);
}

.fade-up-leave-to {
    opacity: 0;
    transform: translateY(-40px);
}

/* Custom selection */
::selection {
    background: white;
    color: black;
}
</style>
