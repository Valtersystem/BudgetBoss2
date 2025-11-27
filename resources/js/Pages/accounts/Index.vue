<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <!-- Título futurista -->
        <template #header>
            <h2 class="text-2xl font-bold title-gradient drop-shadow-[0_0_12px_rgba(168,85,247,0.7)]">
                Dashboard
            </h2>
        </template>

        <!-- Conteúdo principal -->
        <div class="py-10 px-4 sm:px-6 lg:px-8">

            <div class="max-w-7xl mx-auto space-y-8">

                <!-- CARD GLASS PRINCIPAL -->
                <div class="glass-card glass-highlight p-8 rounded-3xl shadow-xl">
                    <p class="text-lg font-medium text-gray-200">
                        You're logged in!
                    </p>

                    <p class="mt-2 text-sm text-purple-300">
                        Bem-vindo ao dashboard futurista ✨
                    </p>
                </div>

                <!-- Grid de Cards (exemplo futurista) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-purple-200 font-semibold text-lg mb-2">
                            💰 Transações
                        </h3>
                        <p class="text-gray-300 text-sm">
                            Veja todas as atividades recentes da sua conta.
                        </p>
                    </div>

                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-purple-200 font-semibold text-lg mb-2">
                            📊 Relatórios
                        </h3>
                        <p class="text-gray-300 text-sm">
                            Acompanhe gráficos, métricas e estatísticas.
                        </p>
                    </div>

                    <div class="glass-card p-6 rounded-2xl">
                        <h3 class="text-purple-200 font-semibold text-lg mb-2">
                            🏦 Contas
                        </h3>
                        <p class="text-gray-300 text-sm">
                            Gerencie contas conectadas e novos depósitos.
                        </p>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
