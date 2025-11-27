<template>
    <div class="max-w-md mx-auto mt-10 p-6 bg-gray-800 rounded-xl shadow-md text-white space-y-5">
        <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-white">
            <i class="fas fa-shield-alt mr-2 text-blue-600"></i> Double Authentification (2FA)
        </h2>

        <p class="text-gray-600 dark:text-gray-300 mb-4">
            Renforcez la sécurité de votre compte en activant la vérification en deux étapes.
            Une fois activée, un code vous sera demandé à chaque connexion, généré par l'application.
        </p>

        <div>
            <button @click="showDialog = true"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition">
                <i class="fas fa-lock mr-1"></i> {{ !isEnabled ? 'Activer la 2FA' : 'Desactiver la 2FA' }}
            </button>
        </div>
    </div>

    <Toast />

    <Dialog v-model:visible="showDialog" modal header="Confirmation" :closable="false">
        <div class="p-fluid">
            <div>
                <label for="configPassword">Votre mot de passe</label>
            </div>
            <div class="mb-2">
                <Password id="configPassword" v-model="password" toggleMask :feedback="false" />
            </div>
            <div class="flex justify-end gap-2 mt-2">
                <Button label="Annuler" @click="showDialog = false" class="p-button-text" />
                <Button label="Valider" @click="change2FA" />
            </div>
        </div>
    </Dialog>


</template>
<script setup>
import { ref, onMounted } from 'vue'
import { useToast } from 'primevue/usetoast';

const toast = useToast()
const showDialog = ref(false)
const password = ref('')

definePageMeta({
    layout: 'admin'
});
const isEnabled = ref(false);

const change2FA = async () => {
    let requestChange2FA = await useApi().post('/admin/2fa', {
        auth_two_factor: Number(!isEnabled.value),
        password: password.value
    })

    let data = requestChange2FA.data;
    toast.add({ severity: data.state, detail: data.message, life: 3000 })

    if (data.state == 'success') {
        check2FA();
        showDialog.value = false;
        password.value = null;
    }

}

const check2FA = async () => {
    let requestCheck2FA = await useApi().get('/admin/2fa/check');
    isEnabled.value = requestCheck2FA.data.auth_two_factor;
}

onMounted(() => {
    check2FA();
})

</script>