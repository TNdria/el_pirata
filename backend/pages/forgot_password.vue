<template>
    <div class="min-h-screen flex items-center justify-center bg-black p-4">
        <div class="w-full max-w-md bg-gray-600 p-6 rounded-2xl shadow-lg">
            <h2 class="text-2xl font-bold mb-4 text-center text-white">
                Mot de passe oublié
            </h2>

            <p class="text-md text-white mb-6 text-center">
                Entrez votre adresse e-mail et nous vous enverrons un lien pour réinitialiser votre mot de passe.
            </p>

            <form @submit.prevent="submit">
                <div class="mb-4">
                    <label for="email" class="block text-mf font-medium text-white mb-2">
                        Adresse e-mail
                    </label>
                    <InputText id="email" v-model="email" type="email" class="w-full"
                        :class="{ 'p-invalid': submitted && !email }" placeholder="Entrez votre e-mail" />
                </div>

                <Button type="submit" :label="loading ? 'Envoi en cours...' : 'Envoyer le lien'" icon="pi pi-envelope"
                    :loading="loading" :disabled="!email || loading"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded disabled:opacity-50" />
            </form>
        </div>
    </div>
    <Toast />
</template>

<script setup>
import { ref } from 'vue'
import InputText from 'primevue/inputtext'
import Button from 'primevue/button'
import { useToast } from 'primevue/usetoast';

const email = ref('')
const submitted = ref(false)
const toast = useToast();
const loading = ref(false);

const submit = async () => {
    loading.value = true;
    let request = await useApi().post('/admin/forgot_password', {
        email: email.value
    });

    toast.add({ severity: request.data.state, detail: request.data.message, life: 2000 });
    loading.value = false;
}
</script>