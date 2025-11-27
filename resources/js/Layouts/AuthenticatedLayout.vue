<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

const user = usePage().props.auth.user;

// Definição dos itens do menu
const menuItems = [
    {
        name: 'Dashboard',
        route: 'dashboard',
        icon: 'M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z'
    },
    {
        name: 'Transações',
        route: 'dashboard', // Mudar para 'transactions.index' futuramente
        icon: 'M9 14l6-6m0 0l-6-6m6 6H3m0 12h18M3 20h18'
    },
    {
        name: 'Contas',
        route: 'dashboard', // Mudar para 'accounts.index' futuramente
        icon: 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'
    },
    {
        name: 'Categorias',
        route: 'dashboard', // Mudar para 'categories.index' futuramente
        icon: 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'
    },
    {
        name: 'Perfil',
        route: 'profile.edit',
        icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'
    },
];
</script>

<template>
    <div
        class="min-h-screen text-gray-100 font-sans selection:bg-purple-500 selection:text-white bg-[#050017] relative overflow-hidden"
    >
        <!-- BACKGROUND FUTURISTA (blobs de luz) -->
        <div class="pointer-events-none fixed inset-0 -z-10">
            <div class="absolute -top-32 -left-24 h-80 w-80 rounded-full bg-purple-500/40 blur-3xl opacity-70" />
            <div class="absolute top-1/3 right-[-6rem] h-96 w-96 rounded-full bg-fuchsia-500/30 blur-3xl opacity-70" />
            <div class="absolute bottom-[-6rem] left-1/2 -translate-x-1/2 h-96 w-96 rounded-full bg-indigo-500/40 blur-3xl opacity-60" />
        </div>

        <!-- DESKTOP SIDEBAR -->
        <aside
            class="hidden md:flex flex-col w-64 fixed inset-y-0 left-0 sidebar-futuristic z-30 rounded-r-3xl overflow-hidden"
        >
            <!-- Header / Logo -->
            <div class="flex items-center justify-center h-16 border-b border-white/5">
                <Link :href="route('dashboard')" class="flex items-center">
                    <ApplicationLogo
                        class="block h-8 w-auto fill-current text-purple-200 drop-shadow-[0_0_10px_rgba(216,180,254,0.9)]"
                    />
                    <span
                        class="ml-2 text-lg font-semibold bg-clip-text text-transparent bg-gradient-to-r from-purple-100 via-fuchsia-200 to-emerald-200"
                    >
                        BudgetBoss
                    </span>
                </Link>
            </div>

            <!-- MENU LATERAL -->
            <nav class="flex-1 px-4 pt-8 pb-6 overflow-y-auto">
                <div class="flex flex-col space-y-2">
                    <Link
                        v-for="item in menuItems"
                        :key="item.name"
                        :href="route(item.route)"
                        class="group"
                    >
                        <div
                            :class="[
                                'flex items-center gap-4 px-4 py-2.5 rounded-2xl transition-all duration-200',
                                route().current(item.route)
                                    ? 'bg-white/18 backdrop-blur-xl shadow-[0_18px_45px_-25px_rgba(0,0,0,0.8)]'
                                    : 'bg-transparent hover:bg-white/6'
                            ]"
                        >
                            <svg
                                class="h-5 w-5 flex-shrink-0 transition-colors"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                :class="route().current(item.route)
                                    ? 'text-white'
                                    : 'text-purple-100/80 group-hover:text-white'"
                            >
                                <path :d="item.icon" />
                            </svg>

                            <span
                                class="text-lg font-medium tracking-wide"
                                :class="route().current(item.route)
                                    ? 'text-white'
                                    : 'text-purple-50/80 group-hover:text-white'"
                            >
                                {{ item.name }}
                            </span>
                        </div>
                    </Link>
                </div>
            </nav>

            <!-- User -->
            <div class="p-4 border-t border-white/5 bg-black/25 backdrop-blur-xl">
                <div class="flex items-center">
                    <div
                        class="h-9 w-9 rounded-2xl bg-gradient-to-br from-purple-400 to-fuchsia-500 flex items-center justify-center text-xs font-semibold text-white shadow-[0_0_18px_rgba(168,85,247,0.8)]"
                    >
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-white">{{ user.name }}</p>
                        <p class="text-xs text-purple-100/70">{{ user.email }}</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <div class="md:pl-64 flex flex-col min-h-screen transition-all duration-300">
            <!-- Top Bar (Mobile & Desktop Header) -->
            <header
                class="sticky top-0 z-20 bg-black/40 backdrop-blur-2xl border-b border-purple-500/20 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-[0_10px_40px_-30px_rgba(0,0,0,0.9)]"
            >
                <!-- Logo Mobile -->
                <div class="flex items-center md:hidden">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationLogo class="block h-8 w-auto fill-current text-purple-400" />
                        <span
                            class="ml-2 text-lg font-bold bg-clip-text text-transparent bg-gradient-to-r from-purple-300 to-fuchsia-400"
                        >
                            BudgetBoss
                        </span>
                    </Link>
                </div>

                <!-- Page Title Slot -->
                <div class="flex-1 px-2 sm:px-4">
                    <h1
                        v-if="$slots.header"
                        class="text-lg sm:text-xl font-semibold title-gradient drop-shadow-[0_0_10px_rgba(168,85,247,0.6)]"
                    >
                        <slot name="header" />
                    </h1>
                </div>

                <!-- Settings Dropdown -->
                <div class="flex items-center">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                class="flex items-center text-sm font-medium text-gray-300 hover:text-white focus:outline-none transition duration-150 ease-in-out"
                            >
                                <span class="hidden md:inline">{{ user.name }}</span>
                                <svg
                                    class="ml-2 -mr-0.5 h-4 w-4"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <div class="bg-[#050016]/95 border border-purple-500/30 rounded-lg shadow-xl">
                                <DropdownLink
                                    :href="route('profile.edit')"
                                    class="text-gray-200 hover:bg-purple-500/20 hover:text-white"
                                >
                                    Perfil
                                </DropdownLink>
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                    class="text-gray-200 hover:bg-red-500/20 hover:text-red-200"
                                >
                                    Sair
                                </DropdownLink>
                            </div>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 pb-24 md:pb-8">
                <slot />
            </main>
        </div>

        <!-- MOBILE BOTTOM NAVIGATION (App Menu) -->
        <nav
            class="md:hidden fixed bottom-0 left-0 right-0 h-[calc(4rem+env(safe-area-inset-bottom))] pb-[env(safe-area-inset-bottom)] bg-black/60 backdrop-blur-2xl border-t border-purple-500/30 flex justify-around items-center z-50 shadow-[0_-10px_40px_rgba(0,0,0,0.9)]"
        >
            <Link
                v-for="item in menuItems"
                :key="item.name"
                :href="route(item.route)"
                class="flex flex-col items-center justify-center w-full h-full space-y-1"
                :class="route().current(item.route)
                    ? 'text-purple-300'
                    : 'text-gray-500 hover:text-gray-300'"
            >
                <div
                    class="relative p-1 rounded-2xl transition-all duration-300"
                    :class="route().current(item.route)
                        ? 'bg-purple-500/30 shadow-[0_0_18px_rgba(168,85,247,0.7)] scale-105'
                        : 'bg-transparent'"
                >
                    <svg
                        class="h-6 w-6"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    >
                        <path :d="item.icon" />
                    </svg>
                </div>
                <span class="text-[10px] font-medium tracking-wide">
                    {{ item.name }}
                </span>
            </Link>
        </nav>
    </div>
</template>

<style scoped>
nav::-webkit-scrollbar {
    width: 0px;
    background: transparent;
}
</style>
