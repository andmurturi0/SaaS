<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    stats: Array,
    recentProducts: Array,
    weeklySales: Array,
    growthRate: String
});
</script>

<template>
    <Head title="Admin Dashboard" />

    <AdminLayout>
        <!-- Hero Section -->
        <div class="relative w-full h-80 rounded-[2.5rem] overflow-hidden mb-10 bg-black group shadow-2xl">
            <img 
                src="/assets/images/hero-bg-standard.png" 
                alt="New Drop" 
                class="absolute inset-0 w-full h-full object-cover opacity-60 group-hover:scale-105 transition-transform duration-700"
            />
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/40 to-transparent"></div>
            
            <div class="relative h-full flex flex-col justify-center px-12 max-w-2xl">
                <span class="inline-block px-4 py-1.5 bg-admin-modern text-black text-xs font-black uppercase tracking-widest rounded-full mb-6 self-start">
                    Limited Edition
                </span>
                <h1 class="text-6xl font-black text-white italic uppercase tracking-tighter leading-none mb-4">
                    New Drop <br /> Collection
                </h1>
                <p class="text-zinc-400 text-lg font-medium mb-8 max-w-md">
                    Explore the latest arrivals and exclusive collaborations for this season.
                </p>
                <div class="flex items-center gap-4">
                    <Link 
                        :href="route('admin.products.index')"
                        class="bg-white text-black px-10 py-4 rounded-2xl font-black uppercase tracking-widest text-sm hover:bg-zinc-200 transition-all"
                    >
                        View Collection
                    </Link>
                </div>
            </div>

            <!-- Floating Sneaker Image -->
            <div class="absolute right-12 top-1/2 -translate-y-1/2 w-96 h-96 pointer-events-none">
                <div class="relative w-full h-full">
                    <img 
                        v-if="recentProducts.length > 0"
                        :src="recentProducts[0].image" 
                        alt="Featured Sneaker" 
                        class="w-full h-full object-contain drop-shadow-[0_35px_35px_rgba(0,0,0,0.5)] animate-float"
                    />
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <div v-for="stat in stats" :key="stat.name" class="bg-zinc-900/50 p-8 rounded-[2rem] shadow-sm hover:shadow-[0_0_30px_rgba(255,255,255,0.05)] transition-all duration-500 border border-white/5 group">
                <div class="flex justify-between items-start mb-6">
                    <div class="p-4 bg-zinc-800/50 rounded-2xl group-hover:bg-admin-modern transition-colors duration-500">
                        <svg class="h-6 w-6 text-white group-hover:text-black transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="stat.icon" />
                        </svg>
                    </div>
                    <span 
                        class="text-xs font-black px-3 py-1 rounded-full flex items-center gap-1"
                        :class="[stat.trend === 'up' ? 'text-green-400 bg-green-400/10' : 'text-red-400 bg-red-400/10']"
                    >
                        {{ stat.change }}
                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path v-if="stat.trend === 'up'" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 15l7-7 7 7" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </div>
                <div>
                    <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest mb-1">{{ stat.name }}</p>
                    <h3 class="text-3xl font-black text-white italic uppercase tracking-tighter">{{ stat.value }}</h3>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Products -->
            <div class="lg:col-span-2 bg-zinc-900/50 rounded-[2.5rem] p-10 shadow-sm border border-white/5">
                <div class="flex justify-between items-center mb-10">
                    <h2 class="text-2xl font-black italic uppercase tracking-tighter text-white">Recent Products</h2>
                    <Link :href="route('admin.products.index')" class="text-sm font-bold text-zinc-500 hover:text-white transition-colors">View All</Link>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="product in recentProducts" :key="product.id" class="group relative bg-zinc-800/30 rounded-[2rem] p-6 hover:bg-zinc-800/50 hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 border border-white/5">
                        <div class="aspect-square rounded-2xl bg-zinc-900/50 overflow-hidden mb-6 flex items-center justify-center p-6 border border-white/5">
                            <img :src="product.image" :alt="product.name" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-700" />
                        </div>
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-black text-lg text-white uppercase tracking-tight leading-tight mb-1">{{ product.name }}</h3>
                                <p class="text-zinc-500 text-sm font-bold uppercase tracking-widest">{{ product.stock }}</p>
                            </div>
                            <p class="font-black text-xl text-white italic uppercase tracking-tighter">{{ product.price }}</p>
                        </div>
                        <div class="absolute top-8 right-8 opacity-0 group-hover:opacity-100 transition-opacity">
                            <Link :href="route('admin.products.edit', product.id)" class="inline-block p-3 bg-admin-modern text-black rounded-xl shadow-lg">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analytics / Activity -->
            <div class="bg-black rounded-[2.5rem] p-10 shadow-2xl text-white">
                <h2 class="text-2xl font-black italic uppercase tracking-tighter mb-10">Sales Analytics</h2>
                
                <div class="space-y-8">
                    <div class="relative h-64 flex items-end justify-between gap-2 px-4">
                        <div v-for="sale in weeklySales" :key="sale.day" class="w-full bg-zinc-800 rounded-t-xl relative group transition-all hover:bg-admin-modern duration-500" :style="`height: ${sale.height}%` ">
                            <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-white text-black px-2 py-1 rounded-lg text-[10px] font-black opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                €{{ sale.amount }}
                            </div>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-7 text-center">
                        <span v-for="sale in weeklySales" :key="sale.day" class="text-zinc-500 text-[10px] font-black uppercase tracking-widest">{{ sale.day }}</span>
                    </div>

                    <div class="pt-10 border-t border-white/10">
                        <div class="flex items-center justify-between mb-6">
                            <p class="text-zinc-400 text-xs font-black uppercase tracking-widest">Growth Rate</p>
                            <span class="text-admin-modern font-black text-xl">{{ growthRate }}</span>
                        </div>
                        <div class="w-full h-2 bg-zinc-900 rounded-full overflow-hidden">
                            <div 
                                class="h-full rounded-full shadow-[0_0_10px_rgba(159,232,112,0.5)] transition-all duration-1000"
                                :class="[parseFloat(growthRate) >= 0 ? 'bg-admin-modern' : 'bg-red-500']"
                                :style="{ width: Math.min(Math.abs(parseFloat(growthRate)), 100) + '%' }"
                            ></div>
                        </div>
                    </div>

                    <button class="w-full py-4 bg-zinc-900 hover:bg-zinc-800 text-white rounded-2xl font-black uppercase tracking-widest text-xs transition-all border border-white/5 mt-4">
                        Download Report
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(5deg); }
    100% { transform: translateY(0px) rotate(0deg); }
}

.animate-float {
    animation: float 6s ease-in-out infinite;
}
</style>
