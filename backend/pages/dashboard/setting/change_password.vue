<template>
    <div class="max-w-md mx-auto mt-10 p-6 bg-gray-800 rounded-xl shadow-md text-white space-y-5">
        <h2 class="text-xl font-bold">Changer le mot de passe</h2>

        <!-- Ancien mot de passe -->
        <div>
            <label class="block mb-1">Ancien mot de passe</label>
            <div class="relative">
                <input :type="showOld ? 'text' : 'password'" v-model="oldPassword" placeholder="••••••"
                    class="w-full p-2 pr-10 rounded bg-gray-900 border border-gray-700 focus:ring-2 focus:ring-blue-500" />
                <i :class="showOld ? 'fas fa-eye-slash' : 'fas fa-eye'"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                    @click="toggleVisibility('old')"></i>
            </div>
        </div>

        <!-- Nouveau mot de passe -->
        <div>
            <label class="block mb-1">Nouveau mot de passe</label>
            <div class="relative">
                <input :type="showNew ? 'text' : 'password'" v-model="newPassword" placeholder="••••••"
                    class="w-full p-2 pr-10 rounded bg-gray-900 border border-gray-700 focus:ring-2 focus:ring-blue-500" />
                <i :class="showNew ? 'fas fa-eye-slash' : 'fas fa-eye'"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                    @click="toggleVisibility('new')"></i>
            </div>
        </div>

        <!-- Confirmation -->
        <div>
            <label class="block mb-1">Confirmer le mot de passe</label>
            <div class="relative">
                <input :type="showConfirm ? 'text' : 'password'" v-model="confirmPassword" placeholder="••••••"
                    class="w-full p-2 pr-10 rounded bg-gray-900 border border-gray-700 focus:ring-2 focus:ring-blue-500" />
                <i :class="showConfirm ? 'fas fa-eye-slash' : 'fas fa-eye'"
                    class="absolute top-1/2 right-3 transform -translate-y-1/2 text-gray-400 cursor-pointer"
                    @click="toggleVisibility('confirm')"></i>
            </div>
        </div>

        <!-- Message d'erreur -->
        <p v-if="errorMessage" class="text-red-400 text-sm">{{ errorMessage }}</p>

        <!-- Bouton -->
        <button @click="submit" :disabled="!isValid"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded disabled:opacity-50">
            Changer le mot de passe
        </button>
    </div>

    <Toast />

</template>

<script setup>
import { ref, computed } from 'vue'
import { useToast } from 'primevue/usetoast';

const oldPassword = ref('')
const newPassword = ref('')
const confirmPassword = ref('')
const toast = useToast();
const showOld = ref(false)
const showNew = ref(false)
const showConfirm = ref(false)

const errorMessage = ref('')


definePageMeta({
    layout: 'admin'
});


const toggleVisibility = (field) => {
    if (field === 'old') showOld.value = !showOld.value
    if (field === 'new') showNew.value = !showNew.value
    if (field === 'confirm') showConfirm.value = !showConfirm.value
}

const isValid = computed(() =>
    oldPassword.value &&
    newPassword.value.length >= 6 &&
    newPassword.value === confirmPassword.value
)

const submit = async () => {
    let request = await useApi().post('/admin/change_password', {
        old_password: oldPassword.value,
        new_password: newPassword.value
    });

    toast.add({ severity: request.data.state, detail: request.data.message, life: 2000 });

    if(request.data.state == "success") {
        oldPassword.value = null;
        newPassword.value = null;
        confirmPassword.value = null;
    }
}
</script>