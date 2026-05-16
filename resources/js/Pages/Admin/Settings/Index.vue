<script setup>
import { ref } from 'vue';
import { Head, usePage, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    settings: Object,
    team: Array
});

const activeTab = ref('store');
const user = usePage().props.auth.user;

const form = useForm({
    store_name: props.settings.store.name,
    currency: props.settings.store.currency,
    language: props.settings.store.language,
    timezone: props.settings.store.timezone,
});

const saveSettings = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
    });
};

const tabs = [
    { id: 'store', name: 'Store', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { id: 'profile', name: 'Profile', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z' },
    { id: 'appearance', name: 'Appearance', icon: 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01' },
    { id: 'payment', name: 'Payment', icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z' },
    { id: 'team', name: 'Team', icon: 'M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2m16-10a4 4 0 11-8 0 4 4 0 018 0z' },
    { id: 'security', name: 'Security', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' }
];

const toggles = ref({
    email_orders: props.settings.notifications.email_orders,
    email_stock: props.settings.notifications.email_stock,
    marketing: props.settings.notifications.marketing
});
</script>

<template>
    <Head title="Admin Settings" />

    <AdminLayout>
        <div class="space-y-10 animate-in fade-in duration-1000">
            <!-- Header -->
            <div>
                <h1 class="text-4xl font-black text-white tracking-tighter uppercase">Settings</h1>
                <p class="text-zinc-500 mt-2 font-medium">Configure your store, manage your team, and personalize your dashboard.</p>
            </div>

            <div class="flex flex-col lg:flex-row gap-10">
                <!-- Navigation Sidebar -->
                <aside class="lg:w-72 shrink-0">
                    <nav class="space-y-2">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="[activeTab === tab.id ? 'bg-admin-modern text-black shadow-lg shadow-admin-modern/20 font-black' : 'text-zinc-500 hover:text-white hover:bg-white/5']"
                            class="w-full flex items-center gap-4 px-6 py-4 rounded-2xl text-xs uppercase tracking-widest transition-all"
                        >
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="tab.icon" /></svg>
                            {{ tab.name }}
                        </button>
                    </nav>
                </aside>

                <!-- Content Panel -->
                <main class="flex-1">
                    <div class="bg-zinc-900/40 backdrop-blur-md border border-white/5 p-10 rounded-[3rem] shadow-2xl relative overflow-hidden">
                        <!-- Store Settings -->
                        <div v-if="activeTab === 'store'" class="space-y-8 animate-in slide-in-from-right-4 duration-500">
                            <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-8">Store Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Store Name</label>
                                    <input type="text" v-model="form.store_name" class="w-full bg-white/5 border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-admin-modern transition-all" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Currency</label>
                                    <select v-model="form.currency" class="w-full bg-zinc-900 border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-admin-modern transition-all">
                                        <option>USD</option>
                                        <option>EUR</option>
                                        <option>ALL</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Language</label>
                                    <select v-model="form.language" class="w-full bg-zinc-900 border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-admin-modern transition-all">
                                        <option>English</option>
                                        <option>Albanian</option>
                                    </select>
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Timezone</label>
                                    <select v-model="form.timezone" class="w-full bg-zinc-900 border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-admin-modern transition-all">
                                        <option>UTC+1</option>
                                        <option>UTC+0</option>
                                    </select>
                                </div>
                            </div>

                            <div class="pt-8 border-t border-white/5">
                                <button 
                                    @click="saveSettings"
                                    :disabled="form.processing"
                                    class="bg-admin-modern text-black px-10 py-4 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-admin-modern/20 hover:scale-105 transition-all disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
                            </div>
                        </div>

                        <!-- Profile Settings -->
                        <div v-if="activeTab === 'profile'" class="space-y-8 animate-in slide-in-from-right-4 duration-500">
                            <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-8">Personal Account</h3>
                            
                            <div class="flex items-center gap-8 mb-10">
                                <div class="w-24 h-24 rounded-[2rem] bg-zinc-800 flex items-center justify-center font-black text-3xl text-white border-2 border-white/10 relative group cursor-pointer overflow-hidden">
                                    <span v-if="!user.profile_photo_url">{{ user.name.charAt(0) }}</span>
                                    <img v-else :src="user.profile_photo_url" class="w-full h-full object-cover" />
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-xl font-black text-white leading-tight">{{ user.name }}</p>
                                    <p class="text-zinc-500">{{ user.email }}</p>
                                    <p class="text-[10px] font-black text-admin-modern uppercase tracking-widest mt-2">Super Admin Access</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Full Name</label>
                                    <input type="text" :value="user.name" class="w-full bg-white/5 border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-admin-modern transition-all" />
                                </div>
                                <div class="space-y-2">
                                    <label class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Email Address</label>
                                    <input type="email" :value="user.email" class="w-full bg-white/5 border-white/10 rounded-2xl py-4 px-6 text-white focus:ring-2 focus:ring-admin-modern transition-all" />
                                </div>
                            </div>

                            <div class="pt-8 border-t border-white/5">
                                <button class="bg-admin-modern text-black px-10 py-4 rounded-2xl font-black uppercase tracking-widest shadow-lg shadow-admin-modern/20 hover:scale-105 transition-all">
                                    Update Profile
                                </button>
                            </div>
                        </div>

                        <!-- Team Management -->
                        <div v-if="activeTab === 'team'" class="space-y-8 animate-in slide-in-from-right-4 duration-500">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="text-2xl font-black text-white uppercase tracking-tighter">Team Members</h3>
                                <button class="bg-white/5 hover:bg-white/10 text-white px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border border-white/5 transition-all">
                                    Invite Staff
                                </button>
                            </div>

                            <div class="space-y-4">
                                <div v-for="member in team" :key="member.id" class="flex items-center justify-between p-6 bg-white/[0.02] border border-white/5 rounded-3xl group">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-zinc-800 flex items-center justify-center font-black text-white border border-white/10 group-hover:border-admin-modern/50 transition-all">
                                            {{ member.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-white">{{ member.name }}</p>
                                            <p class="text-xs text-zinc-500">{{ member.email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] font-black text-zinc-400 uppercase tracking-widest bg-white/5 px-3 py-1 rounded-lg border border-white/5">
                                            {{ member.role }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Appearance -->
                        <div v-if="activeTab === 'appearance'" class="space-y-10 animate-in slide-in-from-right-4 duration-500">
                            <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-8">Interface Settings</h3>
                            
                            <div class="space-y-6">
                                <div class="flex items-center justify-between p-6 bg-white/[0.02] border border-white/5 rounded-3xl">
                                    <div>
                                        <p class="text-sm font-bold text-white">Dark Mode</p>
                                        <p class="text-xs text-zinc-500 mt-1">Enable a darker interface for low light environments.</p>
                                    </div>
                                    <div class="w-14 h-8 bg-admin-modern rounded-full p-1 relative cursor-pointer">
                                        <div class="absolute right-1 top-1 w-6 h-6 bg-black rounded-full shadow-lg transition-all"></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between p-6 bg-white/[0.02] border border-white/5 rounded-3xl">
                                    <div>
                                        <p class="text-sm font-bold text-white">Glassmorphism Effects</p>
                                        <p class="text-xs text-zinc-500 mt-1">Enable transparency and blur on dashboard cards.</p>
                                    </div>
                                    <div class="w-14 h-8 bg-admin-modern rounded-full p-1 relative cursor-pointer">
                                        <div class="absolute right-1 top-1 w-6 h-6 bg-black rounded-full shadow-lg transition-all"></div>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest ml-1">Accent Color</p>
                                    <div class="flex gap-4">
                                        <div v-for="color in ['#9FE870', '#38bdf8', '#a855f7', '#f43f5e', '#fbbf24']" :key="color"
                                            :style="{ backgroundColor: color }"
                                            :class="[color === '#9FE870' ? 'ring-4 ring-white/20' : '']"
                                            class="w-10 h-10 rounded-xl cursor-pointer hover:scale-110 transition-all border border-black/20"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Security -->
                        <div v-if="activeTab === 'security'" class="space-y-10 animate-in slide-in-from-right-4 duration-500">
                            <h3 class="text-2xl font-black text-white uppercase tracking-tighter mb-8">Security & Access</h3>
                            
                            <div class="space-y-8">
                                <div class="bg-white/[0.02] border border-white/5 p-8 rounded-3xl space-y-6">
                                    <div class="flex items-center gap-4 text-amber-400">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                        <p class="text-sm font-bold">Two-Factor Authentication is disabled</p>
                                    </div>
                                    <p class="text-xs text-zinc-500 font-medium leading-relaxed">Add an extra layer of security to your account by requiring a code from your mobile device during login.</p>
                                    <button class="bg-white/5 hover:bg-white/10 text-white px-8 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest border border-white/5 transition-all">
                                        Enable 2FA
                                    </button>
                                </div>

                                <div class="space-y-4">
                                    <h4 class="text-xs font-black text-white uppercase tracking-widest ml-1">Active Sessions</h4>
                                    <div class="p-6 bg-white/[0.02] border border-white/5 rounded-3xl flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <svg class="w-8 h-8 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                            <div>
                                                <p class="text-sm font-bold text-white">Prishtine, Kosove • Chrome on Windows</p>
                                                <p class="text-[10px] font-black text-admin-modern uppercase tracking-widest">Current Session</p>
                                            </div>
                                        </div>
                                        <button class="text-red-400 text-[10px] font-black uppercase tracking-widest hover:underline">Log Out Other Sessions</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>
</style>
