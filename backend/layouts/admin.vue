<template>
    <div class="flex" id="wrapper">
        <!-- Sidebar -->
        <nav id="sidebar"
            class="bg-gray-800 text-white p-2 w-[200px] sm:w-0 lg:w-[200px] transition-all duration-300 xs:hidden lg:block">
            <div class="sidebar-header mb-6">
                <h4 class="text-xl font-semibold">Tableau de bord</h4>
            </div>
            <ul class="space-y-4">
                <li>
                    <NuxtLink to="/dashboard"
                        class="nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa fa-home mr-2"></i> Dashboard
                    </NuxtLink>
                </li>

                <li>
                    <NuxtLink to="/dashboard/account"
                        class="nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa fa-users mr-2"></i> Utilisateurs
                    </NuxtLink>
                </li>

                <li>
                    <NuxtLink to="/dashboard/player"
                        class="nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa fa-gamepad mr-2"></i> Joueur
                    </NuxtLink>
                </li>

                <li>
                    <NuxtLink to="/dashboard/hunting"
                        class="nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa-solid fa-bullseye mr-2"></i> Chasse
                    </NuxtLink>
                </li>

                <li>
                    <NuxtLink to="/dashboard/enigmas"
                        class="nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa-solid  fa-magnifying-glass mr-2"></i> Enigme
                    </NuxtLink>
                </li>

                <li>
                    <NuxtLink to="/dashboard/transaction"
                        class="nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa-solid fa-eur mr-2"></i> Transaction
                    </NuxtLink>
                </li>

                 <li>
                    <NuxtLink to="/dashboard/code_promo"
                        class="nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa-solid fa-eur mr-2"></i> Code promo
                    </NuxtLink>
                </li>

                <li>
                    <input type="checkbox" id="toggleSetting" class="peer hidden">

                    <label for="toggleSetting"
                        class="cursor-pointer nav-link text-white sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fa fa-cogs mr-2"></i> Paramètres
                        </div>
                        <i class="fa fa-chevron-down transition-transform duration-300 peer-checked:rotate-180"></i>
                    </label>

                    <div class="pl-6 mt-2 max-h-0 overflow-hidden transition-all duration-300 peer-checked:max-h-96">
                        <ul class="space-y-2">
                            <li>
                                <NuxtLink to="/dashboard/setting/change_password"
                                    class="block text-white sm:text-sm hover:text-gray-300 mb-2">
                                    <i class="fa fa-lock mr-2"></i> Changement de mot de passe
                                </NuxtLink>
                                <NuxtLink to="/dashboard/setting/2fa"
                                    class="block text-white sm:text-sm hover:text-gray-300 mb-2">
                                    <i class="fa fa-stopwatch mr-2"></i> Activation 2FA
                                </NuxtLink>
                                <NuxtLink to="/dashboard/setting/legal_document"
                                    class="block text-white sm:text-sm hover:text-gray-300 mb-2">
                                    <i class="fa fa-scroll mr-2"></i> Documents légaux
                                </NuxtLink>
                                <NuxtLink to="/dashboard/setting/trace_log"
                                    class="block text-white sm:text-sm hover:text-gray-300">
                                    <i class="fa fa-file mr-2"></i> Logs
                                </NuxtLink>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <div @click="logout()"
                        class="nav-link text-red-500 sm:text-sm text-lg hover:bg-gray-700 p-2 rounded-md flex items-center">
                        <i class="fa fa-sign-out mr-2"></i> Déconnexion
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div id="page-content-wrapper" class="flex-grow flex flex-col min-h-screen">
            <!-- Topbar -->
            <nav class="navbar navbar-expand-lg navbar bg-gray-500 shadow-sm px-4">
                <button class="btn btn-outline-dark d-md-none" @click="toggleSidebar">☰</button>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3">👤 {{ AuthStore.user.name }}</span>
                    <img src="https://i.pravatar.cc/40" class="rounded-circle" alt="avatar">
                </div>
            </nav>

            <!-- Content -->
            <main class="p-6 flex-grow" style="background-color: black;">
                <NuxtPage />
            </main>

            <!-- Footer -->
            <footer class="bg-gray-600 text-white text-center py-4 mt-auto">
                <small>© 2025 harinirinamamyandoniaina@gmail.com - Tous droits réservés.</small>
            </footer>
        </div>

        <ConfirmDialog />

    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '../store/authStore';
import { useConfirm } from 'primevue/useconfirm';

const AuthStore = useAuthStore();
const confirm = useConfirm();

const toggleSidebar = () => {
    const sidebar = document.getElementById("sidebar")
    if (sidebar) {
        sidebar.classList.toggle("hidden")
    }
}

const logout = async (row) => {
    confirm.require({
        message: `Voulez-vous vraiment déconneter ?`,
        header: 'Confirmation',
        icon: 'fa fa-danger',
        acceptLabel: 'Oui',
        rejectLabel: 'Non',
        accept: async () => {
            await AuthStore.logout();
            window.location.href = '/login'
        },
        reject: () => {

        }
    });
}
</script>

<style scoped>
/* Aucune règle spécifique pour Tailwind, les styles sont inclus dans les classes utilitaires */
</style>