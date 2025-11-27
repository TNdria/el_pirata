<template>
    <div class="min-h-screen flex items-center justify-center bg-black p-4">
        <div class="w-full max-w-md bg-gray-600 p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-bold text-white">Changer le mot de passe</h2>

            <!-- Nouveau mot de passe -->
            <div>
                <label class="block mb-1  text-white">Nouveau mot de passe</label>
                <div class="relative">
                    <input :type="showNew ? 'text' : 'password'" v-model="newPassword" placeholder="••••••"
                        class="w-full p-2 pr-10 rounded text-white border border-gray-700 focus:ring-2 focus:ring-blue-500" />
                    <i :class="showNew ? 'fas fa-eye-slash' : 'fas fa-eye'"
                        class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                        @click="toggleVisibility('new')"></i>
                </div>
            </div>

            <!-- Confirmation -->
            <div>
                <label class="block mb-1  text-white">Confirmer le mot de passe</label>
                <div class="relative">
                    <input :type="showConfirm ? 'text' : 'password'" v-model="confirmPassword" placeholder="••••••"
                        class="w-full p-2 pr-10 rounded text-white border border-gray-700 focus:ring-2 focus:ring-blue-500" />
                    <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"
                        class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                        @click="toggleVisibility('confirm')"></i>
                </div>
            </div>

            <!-- Message d'erreur -->
            <p v-if="errorMessage" class="text-red-400 text-sm">{{ errorMessage }}</p>

            <!-- Bouton -->
            <button @click="submit" :disabled="!isValid || loading"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded disabled:opacity-50">
                <i v-if="loading" class="pi pi-spin pi-spinner"></i>
                <span>{{ loading ? 'Chargement...' : 'Changer le mot de passe' }}</span>
            </button>
        </div>
    </div>

    <Toast />

</template>

<script setup>
import { useRoute } from 'vue-router';
import { useAuthStore } from '../store/authStore';
import { ref, computed } from 'vue';
import { useToast } from 'primevue/usetoast';

const newPassword = ref('');
const confirmPassword = ref('');
const toast = useToast();
const showNew = ref(false);
const showConfirm = ref(false);
const route = useRoute();
const token = route.query.user;
const errorMessage = ref('');

const toggleVisibility = (field) => {
    if (field === 'new') showNew.value = !showNew.value
    if (field === 'confirm') showConfirm.value = !showConfirm.value
}

const isValid = computed(() =>
    newPassword.value.length >= 6 &&
    newPassword.value === confirmPassword.value
)

const submit = async () => {
    let request = await useApi().post('/admin/recovery_password', {
        new_password: newPassword.value
    }, {
        headers: {
            Authorization: `Bearer ${token}` // ton token ici
        }
    });

    toast.add({ severity: request.data.state, detail: request.data.message, life: 2000 });

    const auth = useAuthStore()

    if (request.data.state == "success") {
        let data = request.data;
        const auth = useAuthStore()
        await auth.setLoginInfo({
            user: {
                name: data.user.name,
                email: data.user.email,
                id: data.user.id,
                role: data.user.role
            },
            token: data.token
        });
        navigateTo('/dashboard');
    }
}
</script>