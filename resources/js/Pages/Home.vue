<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';
import { 
    ChevronRight, 
    ArrowRight, 
    ShoppingBag, 
    Heart, 
    Zap, 
    Star, 
    Mail,
    Facebook,
    Instagram,
    Twitter
} from 'lucide-vue-next';

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
    router.post(route('favorites.toggle', product.id), {}, { preserveScroll: true });
};

const currentHeroIndex = ref(0);
const nextHero = () => {
    if (props.heroProducts?.length > 0) {
        currentHeroIndex.value = (currentHeroIndex.value + 1) % props.heroProducts.length;
    }
};

onMounted(() => {
    const heroInterval = setInterval(nextHero, 5000);
    onUnmounted(() => clearInterval(heroInterval));
});
</script>

<template>
    <Head title="Premium Footwear & Exclusive Drops" />

    <div class="min-h-screen bg-black text-white font-sans overflow-x-hidden scroll-smooth selection:bg-white selection:text-black">
        <Navbar :auth="auth" />

        <main>
            <!-- Hero Section -->
            <section id="home" class="min-h-[80vh] sm:min-h-screen flex items-center justify-center pt-28 sm:pt-36 lg:pt-44 pb-12 sm:pb-20">
                <div class="w-full max-w-7xl mx-auto px-4 sm:px-8">
                    <div v-if="heroProducts?.length > 0" class="bg-[#111111] rounded-[2.5rem] lg:rounded-[3.5rem] flex flex-col relative overflow-hidden h-[450px] sm:h-[600px] lg:h-[680px] border border-white/5 shadow-[0_50px_100px_-20px_rgba(0,0,0,0.5)] group transition-all duration-700">
                        
                        <!-- Header: Brand & Wishlist -->
                        <div class="absolute top-8 left-8 sm:top-14 sm:left-14 z-30">
                            <div class="flex items-center gap-3 sm:gap-5">
                                <template v-if="heroProducts[currentHeroIndex].brands?.length > 0">
                                    <template v-for="(brand, bIdx) in heroProducts[currentHeroIndex].brands" :key="brand.id">
                                        <div class="w-16 h-8 sm:w-24 sm:h-12 flex items-center justify-center">
                                            <img :src="brand.logo" class="max-w-full max-h-full object-contain opacity-90 brightness-200" />
                                        </div>
                                        <!-- Add an 'x' separator between brands if there's more than one -->
                                        <span v-if="bIdx < heroProducts[currentHeroIndex].brands.length - 1" class="text-white/20 font-black italic text-xs sm:text-sm shrink-0">x</span>
                                    </template>
                                </template>
                            </div>
                        </div>

                        <div class="absolute top-8 right-8 sm:top-14 sm:right-14 z-30">
                            <button 
                                @click="toggleFavorite(heroProducts[currentHeroIndex])"
                                :class="['transition-all duration-500 hover:scale-110 active:scale-90', heroProducts[currentHeroIndex].is_favorited ? 'text-rose-500' : 'text-white']"
                            >
                                <Heart class="w-7 h-7 sm:w-9 sm:h-9" :fill="heroProducts[currentHeroIndex].is_favorited ? 'currentColor' : 'none'" stroke-width="1.5" />
                            </button>
                        </div>

                        <!-- Main Grid Layout -->
                        <div class="relative h-full w-full grid grid-cols-1 lg:grid-cols-12 items-center px-8 sm:px-14 lg:px-20">
                            
                            <!-- Information Block -->
                            <div class="lg:col-span-4 z-20 space-y-10 sm:space-y-12">
                                <Transition name="fade-up" mode="out-in">
                                    <div :key="currentHeroIndex" class="space-y-4">
                                        <h3 class="text-6xl sm:text-7xl lg:text-8xl font-black text-white italic tracking-tighter leading-none">
                                            €{{ Math.round(heroProducts[currentHeroIndex].price) }}
                                        </h3>
                                        <div class="space-y-2">
                                            <h2 class="text-xl sm:text-2xl lg:text-3xl font-black text-white uppercase italic tracking-tight leading-tight">
                                                {{ heroProducts[currentHeroIndex].title }}
                                            </h2>
                                            <p class="text-zinc-500 text-[10px] sm:text-xs font-black uppercase tracking-[0.4em] opacity-60">
                                                {{ heroProducts[currentHeroIndex].category }} Edition
                                            </p>
                                        </div>
                                    </div>
                                </Transition>
                            </div>

                            <!-- Image Container -->
                            <div class="lg:col-span-8 relative h-full flex items-center justify-center lg:justify-end">
                                <!-- Background Glow -->
                                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                    <div class="w-[300px] h-[300px] lg:w-[500px] lg:h-[500px] bg-white/[0.02] rounded-full blur-[100px]"></div>
                                </div>

                                <TransitionGroup name="hero-slide">
                                    <div 
                                        v-for="(product, index) in heroProducts" 
                                        :key="index"
                                        v-show="currentHeroIndex === index"
                                        class="absolute inset-0 flex flex-col items-center justify-center lg:items-end lg:pr-20"
                                    >
                                        <div class="relative group/img">
                                            <img 
                                                :src="product.image" 
                                                class="max-h-[260px] sm:max-h-[420px] lg:max-h-[580px] w-auto object-contain relative z-10 transform -rotate-[15deg] group-hover/img:-rotate-[8deg] transition-transform duration-1000 drop-shadow-[0_40px_80px_rgba(0,0,0,0.8)]" 
                                            />
                                            <!-- Ground Shadow -->
                                            <div class="absolute bottom-10 left-1/2 -translate-x-1/2 w-4/5 h-8 bg-black/80 blur-3xl rounded-[100%] opacity-90 scale-x-110"></div>
                                        </div>
                                    </div>
                                </TransitionGroup>
                            </div>
                        </div>

                        <!-- Action Button: Bottom Right -->
                        <div class="absolute bottom-10 right-10 sm:bottom-14 sm:right-14 z-30">
                            <Link 
                                :href="route('shop.index')" 
                                class="bg-white text-black px-10 py-4 sm:px-14 sm:py-5 rounded-full font-black uppercase tracking-widest text-[10px] sm:text-xs hover:bg-admin-modern transition-all shadow-[0_20px_40px_rgba(255,255,255,0.1)] active:scale-95 hover:-translate-y-1"
                            >
                                Check the shop
                            </Link>
                        </div>
                    </div>

                    <!-- Refined Slider Pagination -->
                    <div class="flex items-center justify-center space-x-3 mt-10 lg:mt-14">
                        <button 
                            v-for="(dot, index) in heroProducts" 
                            :key="index"
                            @click="currentHeroIndex = index"
                            :class="['transition-all duration-700 rounded-full', currentHeroIndex === index ? 'w-14 h-1 bg-white' : 'w-2.5 h-1 bg-zinc-800 hover:bg-zinc-600']"
                        ></button>
                    </div>
                </div>
            </section>

            <!-- Promo Grid -->
            <section class="py-12 px-4 sm:px-8 lg:px-12 max-w-[1920px] mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">
                <Link href="/shop?sort=newest" class="relative group h-64 sm:h-80 rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl">
                    <img src="/assets/images/promo/new-releases.jpg" class="absolute inset-0 w-full h-full object-cover grayscale opacity-40 group-hover:opacity-80 group-hover:scale-110 transition-all duration-[2s]" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                    <div class="relative h-full flex flex-col justify-end p-10 z-20">
                        <h3 class="text-4xl sm:text-6xl font-black text-white italic uppercase tracking-tighter leading-none group-hover:translate-x-2 transition-transform duration-700">New Arrivals</h3>
                    </div>
                </Link>
                <Link href="/shop" class="relative group h-64 sm:h-80 rounded-[2.5rem] overflow-hidden border border-white/5 shadow-2xl">
                    <img src="/assets/images/promo/explore-shop.jpg" class="absolute inset-0 w-full h-full object-cover grayscale opacity-40 group-hover:opacity-80 group-hover:scale-110 transition-all duration-[2s]" />
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                    <div class="relative h-full flex flex-col justify-end p-10 z-20">
                        <h3 class="text-4xl sm:text-6xl font-black text-white italic uppercase tracking-tighter leading-none group-hover:translate-x-2 transition-transform duration-700">Best Sellers</h3>
                        <p class="text-zinc-400 font-bold uppercase tracking-[0.2em] text-[10px] mt-2 opacity-0 group-hover:opacity-100 transition-all duration-700 translate-y-4 group-hover:translate-y-0">The most wanted silhouettes of the season</p>
                    </div>
                </Link>
            </section>

            <!-- Brand Marquee -->
            <section id="brands" class="py-24 lg:py-40 px-4 sm:px-8 lg:px-12 overflow-hidden">
                <div class="max-w-[1920px] mx-auto text-center mb-16 sm:mb-24">
                    <h2 class="text-5xl sm:text-7xl lg:text-8xl font-black uppercase italic tracking-tighter text-zinc-900 absolute left-1/2 -translate-x-1/2 -translate-y-1/2 select-none pointer-events-none opacity-20">PARTNERSHIPS</h2>
                    <h2 class="text-3xl sm:text-5xl font-black tracking-tighter text-white uppercase italic relative z-10">Global Footwear Partners</h2>
                </div>
                <div class="flex justify-center items-center gap-8 sm:gap-16 lg:gap-24 flex-wrap max-w-7xl mx-auto">
                    <Link 
                        v-for="brand in brands" 
                        :key="brand.id"
                        :href="route('shop.index', { brand: brand.name })"
                        class="group flex flex-col items-center gap-6"
                    >
                        <div class="w-24 h-24 sm:w-32 sm:h-32 flex items-center justify-center bg-zinc-900/40 rounded-[2rem] border border-white/5 group-hover:border-white/20 group-hover:bg-zinc-800 transition-all duration-500 group-hover:-translate-y-3 p-6 sm:p-8">
                            <img :src="brand.logo" class="w-full h-full object-contain opacity-30 grayscale group-hover:opacity-100 group-hover:grayscale-0 transition-all duration-700" />
                        </div>
                        <span class="text-[9px] font-black uppercase tracking-[0.4em] text-zinc-700 group-hover:text-white transition-colors">{{ brand.name }}</span>
                    </Link>
                </div>
            </section>

            <!-- Categories -->
            <section id="categories" class="py-20 lg:py-32 px-4 sm:px-8 lg:px-12 max-w-[1920px] mx-auto">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-10">
                    <Link :href="route('shop.index', { category: 'Men' })" class="relative group h-[450px] sm:h-[600px] rounded-[2.5rem] overflow-hidden shadow-2xl border border-white/5">
                        <img src="/assets/images/categories/men.png" class="absolute inset-0 w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-[1.5s]" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                        <div class="relative h-full flex flex-col justify-end p-10 z-20">
                            <h3 class="text-5xl font-black text-white italic uppercase tracking-tighter leading-none mb-4">Men</h3>
                            <div class="inline-flex items-center gap-3 bg-white text-black px-6 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest w-fit group-hover:bg-admin-modern transition-colors">
                                Explore <ArrowRight class="w-3.5 h-3.5" />
                            </div>
                        </div>
                    </Link>
                    <Link :href="route('shop.index', { category: 'Women' })" class="relative group h-[450px] sm:h-[600px] rounded-[2.5rem] overflow-hidden shadow-2xl border border-white/5">
                        <img src="/assets/images/categories/women.png" class="absolute inset-0 w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-110 transition-all duration-[1.5s]" />
                        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/20 to-transparent"></div>
                        <div class="relative h-full flex flex-col justify-end p-10 z-20">
                            <h3 class="text-5xl font-black text-white italic uppercase tracking-tighter leading-none mb-4">Women</h3>
                            <div class="inline-flex items-center gap-3 bg-white text-black px-6 py-3 rounded-xl text-[9px] font-black uppercase tracking-widest w-fit group-hover:bg-admin-modern transition-colors">
                                Explore <ArrowRight class="w-3.5 h-3.5" />
                            </div>
                        </div>
                    </Link>
                    <div class="bg-zinc-900 rounded-[2.5rem] p-10 sm:p-12 flex flex-col justify-between border border-white/5 shadow-2xl relative overflow-hidden h-[450px] sm:h-[600px]">
                        <img src="/assets/images/joinus-bg.png" class="absolute inset-0 w-full h-full object-cover opacity-10 pointer-events-none" />
                        <div class="relative z-10">
                            <h3 class="text-4xl font-black text-white italic uppercase tracking-tighter leading-tight mb-8">Elevate your experience</h3>
                            <ul class="space-y-6">
                                <li v-for="benefit in ['Free Worldwide Shipping', 'Early Drop Access', 'Member-Only Pricing', 'Birthday Exclusives']" :key="benefit" class="flex items-center gap-4">
                                    <div class="w-1.5 h-1.5 bg-admin-modern rounded-full shadow-[0_0_10px_rgba(159,232,112,0.5)]"></div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-zinc-400">{{ benefit }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="relative z-10 flex items-center justify-between">
                            <Link :href="route('register')" class="bg-white text-black px-10 py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-admin-modern transition-all shadow-xl active:scale-95">Register Now</Link>
                            <img src="/assets/images/logo-white.png" class="h-6 opacity-30" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Popular Styles -->
            <section class="py-20 lg:py-32 px-4 sm:px-8 lg:px-12 max-w-[1920px] mx-auto overflow-hidden">
                <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-12 sm:mb-20 gap-8">
                    <div>
                        <h2 class="text-5xl sm:text-7xl font-black uppercase italic tracking-tighter leading-none mb-6">Popular styles</h2>
                        <div class="w-40 sm:w-64 h-1.5 bg-zinc-900 rounded-full overflow-hidden">
                            <div class="w-1/3 h-full bg-admin-modern shadow-[0_0_15px_rgba(159,232,112,0.4)] animate-pulse"></div>
                        </div>
                    </div>
                    <Link :href="route('shop.index')" class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-600 hover:text-white transition-colors flex items-center gap-3">
                        View Entire Catalog <ArrowRight class="w-4 h-4" />
                    </Link>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 lg:gap-10">
                    <div v-for="product in popularProducts" :key="product.id" class="group relative flex flex-col">
                        <Link :href="route('shop.show', product.id)" class="flex-1 flex flex-col">
                            <div class="aspect-square bg-zinc-900 rounded-[2rem] lg:rounded-[3rem] p-10 flex items-center justify-center relative overflow-hidden border border-white/5 group-hover:border-white/20 transition-all duration-700 shadow-2xl">
                                <img src="/assets/images/popular-bg.png" class="absolute inset-0 w-full h-full object-cover opacity-5 group-hover:opacity-10 transition-all duration-[2s] scale-150 group-hover:rotate-12" />
                                <img :src="product.image" class="h-40 w-auto object-contain relative z-10 transition-all duration-1000 group-hover:scale-110 group-hover:-rotate-12 drop-shadow-[0_30px_30px_rgba(0,0,0,0.6)]" />
                                <div v-if="product.sale_price" class="absolute top-6 left-6 bg-admin-modern text-black px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest shadow-xl">OFFER</div>
                            </div>
                            <div class="mt-8 px-2 flex-1">
                                <div class="flex justify-between items-start gap-4">
                                    <h4 class="font-black text-xl text-white italic uppercase tracking-tighter leading-tight">{{ product.name }}</h4>
                                    <span class="text-[8px] font-black uppercase tracking-widest text-zinc-700 border border-white/10 px-2 py-1 rounded-md">{{ product.brands?.[0]?.name || 'PREMIUM' }}</span>
                                </div>
                                <div class="mt-2 flex items-center gap-3">
                                    <p class="text-white font-black text-xl italic tracking-widest">€{{ Math.round(product.sale_price || product.price) }}</p>
                                    <p v-if="product.sale_price" class="text-zinc-800 line-through font-bold text-xs tracking-widest">€{{ Math.round(product.price) }}</p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </div>
            </section>

            <!-- About Us / Heritage Section -->
            <section id="about" class="py-24 lg:py-44 px-4 sm:px-8 lg:px-12 max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-16 lg:gap-32">
                <div class="flex-1 w-full max-w-xl lg:max-w-none h-[400px] sm:h-[600px] relative group">
                    <!-- Invisible Frame (Removed border, kept structure) -->
                    <div class="absolute inset-0 rounded-[3rem] lg:rounded-[4.5rem] transform translate-x-5 translate-y-5 transition-transform duration-1000"></div>
                    <div class="absolute inset-0 bg-[#0F0F0F] rounded-[3rem] lg:rounded-[4.5rem] overflow-hidden shadow-[0_40px_100px_rgba(0,0,0,0.6)] relative z-10 flex items-center justify-center">
                        <template v-if="aboutUs?.image">
                            <img :src="aboutUs.image" class="w-full h-full object-cover object-center grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 group-hover:scale-110 transition-all duration-[2.5s]" />
                        </template>
                        <template v-else>
                            <div class="flex flex-col items-center justify-center space-y-4 opacity-[0.03]">
                                <ShoppingBag class="w-24 h-24 text-white" />
                            </div>
                        </template>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent pointer-events-none"></div>
                    </div>
                </div>

                <div class="flex-1 space-y-12 lg:space-y-16">
                    <div class="space-y-6">
                        <div class="inline-flex items-center gap-4 bg-zinc-900/50 border border-white/5 px-8 py-3 rounded-full text-[10px] font-black uppercase tracking-[0.4em] text-zinc-500 select-none">
                            <Star class="w-3 h-3 text-admin-modern" />
                            Our Heritage
                        </div>
                        <h2 class="text-4xl sm:text-6xl font-black uppercase italic tracking-tighter leading-[0.85] text-white">
                            {{ aboutUs?.title_line_1 || 'Unapologetic' }} <br />
                            <span class="text-zinc-800">{{ aboutUs?.title_line_2 || 'Performance' }}</span>
                        </h2>
                        <p class="text-zinc-400 text-lg sm:text-xl font-bold italic tracking-tight uppercase leading-relaxed max-w-lg opacity-80">
                            {{ aboutUs?.description || 'Crafting the highest quality silhouettes for the modern urban explorer. We believe in style without compromise.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-16 border-y border-white/5 py-14">
                        <div v-for="stat in (aboutUs?.stats || [{label: 'Global Outlets', value: '12+'}, {label: 'Silhouettes', value: '250+'}])" :key="stat.label">
                            <div class="text-5xl sm:text-6xl font-black text-white italic tracking-tighter mb-2">{{ stat.value }}</div>
                            <div class="text-zinc-600 font-black uppercase tracking-[0.2em] text-[10px]">{{ stat.label }}</div>
                        </div>
                    </div>

                    <Link :href="route('shop.index')" class="inline-flex bg-white text-black px-14 py-6 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-admin-modern transition-all shadow-[0_20px_40px_rgba(255,255,255,0.05)] active:scale-95 hover:-translate-y-1">Explore History</Link>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-zinc-900/30 border-t border-white/5 pt-24 sm:pt-32 pb-12 relative overflow-hidden backdrop-blur-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 lg:px-12 relative z-10">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-16 lg:gap-20 mb-24 lg:mb-32">
                    <div class="space-y-8">
                        <img src="/assets/images/logo-white.png" class="h-8 w-auto object-contain opacity-80" />
                        <p class="text-zinc-600 font-bold uppercase tracking-widest text-[10px] leading-relaxed max-w-[200px]">Redefining the culture of premium footwear since 2026.</p>
                        <div class="flex items-center gap-6">
                            <Instagram class="w-5 h-5 text-zinc-600 hover:text-white transition-colors cursor-pointer" />
                            <Twitter class="w-5 h-5 text-zinc-600 hover:text-white transition-colors cursor-pointer" />
                            <Facebook class="w-5 h-5 text-zinc-600 hover:text-white transition-colors cursor-pointer" />
                        </div>
                    </div>
                    <div>
                        <h4 class="font-black mb-8 text-zinc-800 uppercase text-[10px] tracking-[0.4em]">NAVIGATION</h4>
                        <ul class="space-y-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                            <li><Link href="/" class="hover:text-white transition-all">Storefront</Link></li>
                            <li><Link href="/shop" class="hover:text-white transition-all">New Arrivals</Link></li>
                            <li><Link href="/shop" class="hover:text-white transition-all">Release Calendar</Link></li>
                            <li><Link href="/shop" class="hover:text-white transition-all">Sustainability</Link></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-black mb-8 text-zinc-800 uppercase text-[10px] tracking-[0.4em]">SUPPORT</h4>
                        <ul class="space-y-4 text-[10px] font-black uppercase tracking-widest text-zinc-500">
                            <li><a href="#" class="hover:text-white transition-all">Help Center</a></li>
                            <li><a href="#" class="hover:text-white transition-all">Shipping & Returns</a></li>
                            <li><a href="#" class="hover:text-white transition-all">Order Tracking</a></li>
                            <li><a href="#" class="hover:text-white transition-all">Store Locator</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-black mb-8 text-zinc-800 uppercase text-[10px] tracking-[0.4em]">ELITE MEMBERSHIP</h4>
                        <p class="text-[10px] font-black uppercase tracking-widest text-zinc-600 mb-6 leading-relaxed">Join for exclusive early access to high-demand silhouettes.</p>
                        <div class="relative">
                            <input type="email" placeholder="EMAIL ADDRESS" class="bg-black/50 border border-white/5 rounded-xl py-4 px-6 w-full text-[10px] font-black uppercase tracking-widest focus:ring-1 focus:ring-white/20 transition-all outline-none" />
                            <button class="absolute right-4 top-1/2 -translate-y-1/2 hover:translate-x-1 transition-transform">
                                <ArrowRight class="w-4 h-4 text-white" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="text-center pt-12 border-t border-white/5">
                    <p class="text-zinc-800 text-[9px] font-black uppercase tracking-[0.5em]">
                        © 2026 Projektii Global. All Rights Reserved. Designed by And Murturi.
                    </p>
                </div>
            </div>
            
            <!-- Background Decorative Logo -->
            <div class="absolute bottom-[-10%] left-1/2 -translate-x-1/2 opacity-[0.02] select-none pointer-events-none w-full">
                <img src="/assets/images/logo-white.png" class="w-full grayscale transform scale-125" />
            </div>
        </footer>
    </div>
</template>

<style scoped>
.hero-slide-enter-active,
.hero-slide-leave-active {
    transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}
.hero-slide-enter-from {
    opacity: 0;
    transform: rotate(-20deg) translateX(100px) scale(0.9);
    filter: blur(20px);
}
.hero-slide-leave-to {
    opacity: 0;
    transform: rotate(10deg) translateX(-100px) scale(1.1);
    filter: blur(20px);
}
.fade-up-enter-active,
.fade-up-leave-active {
    transition: all 0.6s ease-out;
}
.fade-up-enter-from {
    opacity: 0;
    transform: translateY(20px);
}
.fade-up-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>
