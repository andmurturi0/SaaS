<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import MainLayout from '@/Layouts/MainLayout.vue';
import { useCartStore } from '@/Stores/useCartStore';

const props = defineProps({
    products: Object,
    categories: Array,
    brands: Array,
    filters: Object,
    auth: Object
});

const cartStore = useCartStore();

const activeFilters = ref({
    search: props.filters.search || '',
    category: props.filters.category || '',
    brand: props.filters.brand || '',
    min_price: props.filters.min_price || '',
    max_price: props.filters.max_price || '',
    sort: props.filters.sort || 'newest'
});

const applyFilters = () => {
    router.get(route('shop.index'), activeFilters.value, {
        preserveState: true,
        preserveScroll: true,
    });
};

watch(() => activeFilters.value.sort, applyFilters);

const toggleFavorite = (product) => {
    if (!props.auth.user) {
        router.visit(route('login'));
        return;
    }

    router.post(route('favorites.toggle', product.id), {}, {
        preserveScroll: true,
        onSuccess: () => {
            // product.is_favorited will be updated by the page reload
        }
    });
};
</script>

<template>
    <MainLayout :auth="auth" title="Shop">
        <div class="flex gap-12">
            <!-- Sidebar Filters -->
            <aside class="w-72 flex-shrink-0 space-y-12">
                <div>
                    <h3 class="text-2xl font-black uppercase italic tracking-tighter mb-6">Filters</h3>
                    
                    <!-- Categories -->
                    <div class="space-y-4 mb-10">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Categories</h4>
                        <div class="space-y-2">
                            <button 
                                @click="activeFilters.category = ''; applyFilters()"
                                :class="activeFilters.category === '' ? 'text-white' : 'text-zinc-600 hover:text-zinc-400'"
                                class="block text-sm font-bold uppercase transition-colors text-left"
                            >
                                All Categories
                            </button>
                            <button 
                                v-for="category in categories" 
                                :key="category.id"
                                @click="activeFilters.category = category.name; applyFilters()"
                                :class="activeFilters.category === category.name ? 'text-white' : 'text-zinc-600 hover:text-zinc-400'"
                                class="block text-sm font-bold uppercase transition-colors text-left"
                            >
                                {{ category.name }}
                            </button>
                        </div>
                    </div>

                    <!-- Brands -->
                    <div class="space-y-4 mb-10">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Brands</h4>
                        <div class="grid grid-cols-2 gap-3">
                            <button 
                                v-for="brand in brands" 
                                :key="brand.id"
                                @click="activeFilters.brand = brand.name; applyFilters()"
                                :class="activeFilters.brand === brand.name ? 'border-white bg-white/10' : 'border-white/5 hover:border-white/20'"
                                class="border p-4 rounded-2xl transition-all group"
                            >
                                <img :src="brand.logo" class="h-6 w-full object-contain opacity-40 group-hover:opacity-100 transition-opacity grayscale group-hover:grayscale-0" />
                            </button>
                        </div>
                        <button 
                            @click="activeFilters.brand = ''; applyFilters()"
                            class="text-[10px] font-black uppercase tracking-widest text-zinc-600 hover:text-white"
                        >
                            Clear Brands
                        </button>
                    </div>

                    <!-- Price Range -->
                    <div class="space-y-4">
                        <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Price Range</h4>
                        <div class="flex gap-4">
                            <input 
                                type="number" 
                                v-model="activeFilters.min_price" 
                                placeholder="Min"
                                class="w-full bg-zinc-900 border-white/5 rounded-xl text-xs font-bold focus:ring-white/20 text-white"
                            />
                            <input 
                                type="number" 
                                v-model="activeFilters.max_price" 
                                placeholder="Max"
                                class="w-full bg-zinc-900 border-white/5 rounded-xl text-xs font-bold focus:ring-white/20 text-white"
                            />
                        </div>
                        <button 
                            @click="applyFilters"
                            class="w-full bg-white text-black py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-zinc-200 transition-all"
                        >
                            Apply Price
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Product Grid -->
            <div class="flex-1">
                <!-- Top Bar -->
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <h2 class="text-6xl font-black uppercase italic tracking-tighter leading-none mb-2">Shop All</h2>
                        <p class="text-zinc-500 font-bold uppercase tracking-widest text-[10px]">{{ products.total }} Results Found</p>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <span class="text-[10px] font-black uppercase tracking-widest text-zinc-500">Sort by:</span>
                        <select 
                            v-model="activeFilters.sort" 
                            class="bg-black border-none text-[10px] font-black uppercase tracking-widest focus:ring-0 cursor-pointer text-white p-0"
                        >
                            <option value="featured">Featured</option>
                            <option value="newest">Newest</option>
                            <option value="price_low">Price: Low to High</option>
                            <option value="price_high">Price: High to Low</option>
                        </select>
                    </div>
                </div>

                <!-- Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-10">
                    <div v-for="product in products.data" :key="product.id" class="group cursor-pointer relative space-y-6">
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

                        <Link :href="route('shop.show', product.id)" class="block">
                            <div class="aspect-square bg-zinc-900 rounded-[2.5rem] p-10 flex items-center justify-center relative overflow-hidden border border-white/5 group-hover:border-white/20 transition-all duration-700 shadow-2xl">
                                <img src="/assets/images/popular-bg.png" class="absolute inset-0 w-full h-full object-cover opacity-5 group-hover:opacity-20 transition-all duration-700 scale-150 group-hover:rotate-12" />
                                <img :src="product.image" class="h-40 object-contain relative z-10 transition-all duration-1000 group-hover:scale-110 group-hover:-rotate-12 drop-shadow-[0_20px_20px_rgba(0,0,0,0.5)]" />
                            </div>
                            <div class="px-2 mt-6 space-y-1">
                                <div class="flex justify-between items-start">
                                    <h4 class="font-black text-xl text-white italic uppercase tracking-tighter leading-tight max-w-[70%]">{{ product.name }}</h4>
                                    <span class="text-zinc-600 font-black text-[8px] uppercase tracking-widest italic text-right leading-tight max-w-[30%]">
                                        {{ product.brands?.map(b => b.name).join(', ') }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <p class="text-white/60 font-black text-lg tracking-widest">€{{ Math.round(product.sale_price || product.price) }}</p>
                                    <p v-if="product.sale_price && parseFloat(product.sale_price) < parseFloat(product.price)" class="text-zinc-700 line-through font-black text-sm tracking-widest">€{{ Math.round(product.price) }}</p>
                                </div>
                            </div>
                        </Link>
                        
                        <!-- Add to Cart Button -->
                        <button 
                            @click="cartStore.addItem(product.id)"
                            class="w-full mt-2 bg-white text-black py-3 rounded-xl font-black uppercase text-[10px] tracking-widest hover:bg-zinc-200 transition-all opacity-0 group-hover:opacity-100"
                        >
                            Add to Cart
                        </button>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="products.links.length > 3" class="mt-20 flex justify-center space-x-2">
                    <template v-for="(link, k) in products.links" :key="k">
                        <div v-if="link.url === null" class="px-4 py-2 text-zinc-600 text-[10px] font-black uppercase" v-html="link.label"></div>
                        <Link v-else 
                            :href="link.url" 
                            class="px-4 py-2 rounded-xl text-[10px] font-black uppercase transition-all"
                            :class="link.active ? 'bg-white text-black' : 'text-zinc-500 hover:text-white hover:bg-white/5'"
                            v-html="link.label"
                        />
                    </template>
                </div>

                <div v-if="products.data.length === 0" class="py-40 text-center">
                    <h3 class="text-4xl font-black uppercase italic tracking-tighter text-zinc-800">No products found matching your filters</h3>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
