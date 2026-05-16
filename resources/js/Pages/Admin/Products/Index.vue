<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    products: Array
});

const deleteProduct = (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        router.delete(route('admin.products.destroy', id));
    }
};
</script>

<template>
    <Head title="Admin - Products" />

    <AdminLayout>
        <div class="flex justify-between items-end mb-10">
            <div>
                <h1 class="text-4xl font-black italic uppercase tracking-tighter mb-2 text-white">Inventory</h1>
                <p class="text-zinc-500 font-bold uppercase tracking-widest text-xs">Manage your product catalog and stock levels</p>
            </div>
            <Link 
                :href="route('admin.products.create')"
                class="bg-admin-modern text-black px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-admin-modern/90 transition-all shadow-lg shadow-admin-modern/20"
            >
                Add New Product
            </Link>
        </div>

        <div class="bg-zinc-900/50 rounded-[2.5rem] overflow-hidden shadow-sm border border-white/5">
            <div class="p-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-separate border-spacing-y-4">
                        <thead>
                            <tr>
                                <th class="pb-4 px-6 font-black uppercase text-[10px] tracking-[0.2em] text-zinc-500">Product</th>
                                <th class="pb-4 px-6 font-black uppercase text-[10px] tracking-[0.2em] text-zinc-500">Brand & Category</th>
                                <th class="pb-4 px-6 font-black uppercase text-[10px] tracking-[0.2em] text-zinc-500">Price</th>
                                <th class="pb-4 px-6 font-black uppercase text-[10px] tracking-[0.2em] text-zinc-500">Stock Status</th>
                                <th class="pb-4 px-6 font-black uppercase text-[10px] tracking-[0.2em] text-zinc-500 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id" class="group transition-all">
                                <td class="py-4 px-6 bg-zinc-800/20 rounded-l-[1.5rem] group-hover:bg-zinc-800/40 group-hover:shadow-xl transition-all border-y border-l border-white/5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-xl bg-zinc-900 border border-white/5 p-2 flex items-center justify-center overflow-hidden">
                                            <img :src="product.image" :alt="product.name" class="w-full h-full object-contain group-hover:scale-110 transition-transform duration-500" />
                                        </div>
                                        <div>
                                            <p class="font-black text-white uppercase tracking-tight leading-none mb-1 group-hover:text-admin-modern transition-colors">{{ product.name }}</p>
                                            <p class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">ID: #{{ product.id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 bg-zinc-800/20 group-hover:bg-zinc-800/40 group-hover:shadow-xl transition-all border-y border-white/5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-zinc-300">
                                            {{ product.brands?.length ? product.brands.map(b => b.name).join(', ') : 'No Brand' }}
                                        </span>
                                        <span class="text-[10px] font-bold text-zinc-500 uppercase tracking-widest">{{ product.category?.name || 'Uncategorized' }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 bg-zinc-800/20 group-hover:bg-zinc-800/40 group-hover:shadow-xl transition-all border-y border-white/5">
                                    <div class="flex flex-col">
                                        <span class="font-black text-lg italic uppercase tracking-tighter text-white">
                                            €{{ (product.sale_price && parseFloat(product.sale_price) > 0 && parseFloat(product.sale_price) < parseFloat(product.price)) ? parseFloat(product.sale_price).toFixed(2) : parseFloat(product.price).toFixed(2) }}
                                        </span>
                                        <span v-if="product.sale_price && parseFloat(product.sale_price) > 0 && parseFloat(product.sale_price) < parseFloat(product.price)" class="text-[10px] text-zinc-600 line-through font-bold">€{{ parseFloat(product.price).toFixed(2) }}</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 bg-zinc-800/20 group-hover:bg-zinc-800/40 group-hover:shadow-xl transition-all border-y border-white/5">
                                    <div class="flex items-center gap-2">
                                        <div 
                                            class="w-2 h-2 rounded-full"
                                            :class="[product.stock > 10 ? 'bg-admin-modern shadow-[0_0_8px_rgba(159,232,112,0.5)]' : product.stock > 0 ? 'bg-orange-400 shadow-[0_0_8px_rgba(251,146,60,0.5)]' : 'bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]']"
                                        ></div>
                                        <span class="text-xs font-black uppercase tracking-widest" :class="[product.stock > 0 ? 'text-zinc-400' : 'text-red-400']">
                                            {{ product.stock > 0 ? `${product.stock} in stock` : 'Out of stock' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 bg-zinc-800/20 rounded-r-[1.5rem] group-hover:bg-zinc-800/40 group-hover:shadow-xl transition-all border-y border-r border-white/5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link 
                                            :href="route('admin.products.edit', product.id)"
                                            class="p-3 bg-zinc-900/50 text-zinc-400 hover:text-white hover:bg-zinc-800 rounded-xl shadow-sm transition-all border border-white/5"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </Link>
                                        <button 
                                            @click="deleteProduct(product.id)"
                                            class="p-3 bg-zinc-900/50 text-zinc-400 hover:text-red-400 hover:bg-zinc-800 rounded-xl shadow-sm transition-all border border-white/5"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="products.length === 0">
                                <td colspan="5" class="py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-zinc-800 rounded-full flex items-center justify-center mb-4 border border-white/5">
                                            <svg class="h-10 w-10 text-zinc-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <p class="text-zinc-500 font-black uppercase tracking-widest text-sm">No products found in your inventory</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

