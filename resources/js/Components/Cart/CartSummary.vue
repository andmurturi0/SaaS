<script setup>
import { ref } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import { 
    TicketIcon, 
    XMarkIcon, 
    ArrowRightIcon, 
    InformationCircleIcon 
} from '@heroicons/vue/24/outline';
import { CheckBadgeIcon } from '@heroicons/vue/24/solid';

const props = defineProps({
    summary: {
        type: Object,
        required: true,
    }
});

const emit = defineEmits(['apply-coupon', 'remove-coupon']);

const couponForm = useForm({
    code: '',
});

const formattedPrice = (price) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'EUR',
    }).format(price);
};

const handleApplyCoupon = () => {
    if (couponForm.code) {
        emit('apply-coupon', couponForm.code);
        couponForm.code = '';
    }
};
</script>

<template>
    <div class="bg-zinc-900 rounded-[3rem] p-8 lg:p-10 sticky top-40 border border-white/5 shadow-2xl">
        <h2 class="text-2xl font-black uppercase italic tracking-tighter text-white mb-8">Order Summary</h2>

        <!-- Coupon Input -->
        <div class="mb-10">
            <label for="coupon" class="block text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-3">Have a coupon code?</label>
            <div v-if="summary.coupon" class="flex items-center justify-between p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl">
                <div class="flex items-center">
                    <CheckBadgeIcon class="w-5 h-5 text-emerald-500 mr-3" />
                    <span class="text-sm font-black text-emerald-500 uppercase tracking-widest">{{ summary.coupon.code }}</span>
                </div>
                <button @click="emit('remove-coupon')" class="text-emerald-500 hover:text-white transition-colors">
                    <XMarkIcon class="w-5 h-5" />
                </button>
            </div>
            <div v-else class="relative">
                <input 
                    v-model="couponForm.code"
                    type="text" 
                    id="coupon" 
                    placeholder="Enter code"
                    class="block w-full px-6 py-4 bg-black border border-white/5 rounded-2xl text-xs font-bold uppercase tracking-widest text-white focus:ring-1 focus:ring-white/20 transition-all placeholder:text-zinc-700 placeholder:normal-case"
                    @keyup.enter="handleApplyCoupon"
                />
                <button 
                    @click="handleApplyCoupon"
                    class="absolute right-2 top-2 px-4 py-2 text-[10px] font-black uppercase tracking-widest text-black bg-white rounded-xl hover:bg-zinc-200 transition-all"
                >
                    Apply
                </button>
            </div>
        </div>

        <!-- Totals -->
        <div class="space-y-5 mb-10">
            <div class="flex justify-between text-xs font-bold uppercase tracking-widest">
                <span class="text-zinc-500">Subtotal</span>
                <span class="text-white">{{ formattedPrice(summary.subtotal) }}</span>
            </div>
            
            <div v-if="summary.discount > 0" class="flex justify-between text-xs font-bold uppercase tracking-widest">
                <span class="text-emerald-500">Discount</span>
                <span class="text-emerald-500">-{{ formattedPrice(summary.discount) }}</span>
            </div>

            <div class="flex justify-between text-xs font-bold uppercase tracking-widest">
                <span class="text-zinc-500 flex items-center">
                    Shipping
                    <InformationCircleIcon class="w-4 h-4 ml-2 text-zinc-700 cursor-help" title="Standard shipping rate" />
                </span>
                <span class="text-white">
                    {{ summary.shipping > 0 ? formattedPrice(summary.shipping) : 'FREE' }}
                </span>
            </div>

            <!-- Free Shipping Progress -->
            <div v-if="summary.shipping > 0" class="pt-4">
                <div class="flex justify-between text-[9px] font-black uppercase tracking-widest mb-2">
                    <span class="text-zinc-600">Spend {{ formattedPrice(summary.free_shipping_threshold - summary.subtotal) }} more for free shipping</span>
                    <span class="text-white">{{ Math.round(summary.free_shipping_progress) }}%</span>
                </div>
                <div class="w-full bg-black rounded-full h-1">
                    <div 
                        class="bg-white h-1 rounded-full transition-all duration-1000" 
                        :style="{ width: summary.free_shipping_progress + '%' }"
                    ></div>
                </div>
            </div>

            <div class="pt-6 border-t border-white/5 flex justify-between items-end">
                <span class="text-xl font-black uppercase italic tracking-tighter text-white">Total</span>
                <span class="text-3xl font-black uppercase italic tracking-tighter text-white">{{ formattedPrice(summary.total) }}</span>
            </div>
        </div>

        <!-- Checkout Button -->
        <Link 
            :href="route('checkout.index')"
            as="button"
            class="w-full flex items-center justify-center px-8 py-6 bg-white text-black rounded-2xl font-black uppercase tracking-widest text-xs hover:bg-zinc-200 transition-all duration-300 group shadow-2xl active:scale-95"
        >
            Checkout
            <ArrowRightIcon class="w-5 h-5 ml-3 transition-transform group-hover:translate-x-1" />
        </Link>

        <!-- Trust Badges -->
        <div class="mt-10">
            <div class="flex items-center justify-center space-x-6 grayscale opacity-20 invert transition-opacity hover:opacity-40">
                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg" alt="Visa" class="h-4" />
                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" class="h-6" />
                <img src="https://upload.wikimedia.org/wikipedia/commons/b/b5/PayPal.svg" alt="PayPal" class="h-4" />
                <img src="https://upload.wikimedia.org/wikipedia/commons/3/31/Apple_Pay_logo.svg" alt="Apple Pay" class="h-6" />
            </div>
            <p class="text-center text-[8px] font-black uppercase tracking-[0.2em] text-zinc-700 mt-6 leading-relaxed">
                🔒 256-bit SSL Secure Checkout<br>
                Free 30-day returns on all orders
            </p>
        </div>
    </div>
</template>
