<template>

    <div class="min-h-screen bg-gray-900 flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-sm space-y-8">
            <div class="text-center">
                <img src="/images/logo.svg" alt="El Pirata Logo" class="mx-auto w-24 h-24">
                <h2 class="mt-6 text-2xl md:text-3xl  text-lg font-bold text-yellow-500">
                    Vérification de votre compte
                </h2>
            </div>

            <div
                class="bg-gray-800 p-6 md:p-8 rounded-lg shadow-lg border-2 border-yellow-500 w-full max-w-sm overflow-hidden">

                <p class="sm:text-3xl text-3xl  text-lg text-gray-300 mb-4 md:text-base break-words" v-if="!token">
                    Félicitations ! Vous êtes presque prêt à commencer. Un e-mail de vérification a été envoyé à votre
                    adresse. Veuillez vérifier votre boîte de réception (et votre dossier de spam) et cliquer sur le
                    lien de confirmation pour activer votre compte.
                </p>

                <p class="sm:text-3xl text-3xl  text-lg text-gray-300 mb-4 md:text-base break-words" v-if="token">
                    Merci de confirmer votre adresse e-mail. Vous pouvez maintenant accéder à toutes les fonctionnalités
                    de votre compte.
                </p>

                <a href="/profil" class="sm:text-2xl text-2xl  text-[#FFFFFF] bg-[#EB0000] sm:text-2xl text-lg p-3 rounded-xl sm:shadow-md shadow-sm shadow-gray-500/50 cursor-pointer btn btn-info" style="float: inline-end;" v-if="authStore.isAuthenticated">
                     Accéder a mon compte
                </a>

            </div>
        </div>
    </div>
</template>
<script>
import { useAuthStore } from '../store/authStore'

export default {
    setup() {
        const authStore = useAuthStore();
        
        return {
            authStore
        }
    },
    data() {
        return {
            token: null,
        }
    },
    mounted() {
        if (this.$route.query.user) {
            this.token = this.$route.query.user || null;
            useApi().get("/verifed_mail", {
                headers: {
                    Authorization: `Bearer ${this.token}`
                }
            }).then(
                async (response) => {
                    if (response.data.state == 'verified') {
                        let data = response.data;
                        await this.authStore.setLoginInfo({
                            user: {
                                name: data.user.name,
                                email: data.user.email
                            },
                            token: this.token
                        });
                    }
                }
            ).catch()
        }

    }
}
</script>