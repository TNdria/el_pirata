<template>
    <div class="flex-grow bg-[#160f08] text-white p-8  font-[Merienda]  ">
        <h2 class="mb-4 text-lg mt-10">
            <span
                class="inline-flex items-center justify-center w-6 h-6 bg-[#53a95a] text-black rounded-full text-base mr-0.5 relative -translate-y-[1px]">✔</span>
            Mon profil
        </h2>

        <hr class="border-0 h-[2px] bg-[#26211b] my-4">

        <div class="block md:flex justify-between gap-20">
            <div class="w-full text-lg">
                <label class="block font-bold mb-2 font-[Merienda]">Pseudo</label>
                <input type="text" v-model="userInfo.name" disabled
                    class="w-full p-2.5 rounded bg-white text-black border border-gray-400 font-[Merienda]">
            </div>
            <div class="w-full text-lg">
                <label class="block font-bold mb-2 font-[Merienda]">Adresse e-mail</label>
                <input type="email" v-model="userInfo.email" disabled
                    class="w-full p-2.5 rounded bg-white text-black border border-gray-400 font-[Merienda]">
            </div>
        </div>

        <div class="flex justify-between gap-2 my-4 items-center">
            <div class="w-full">
                <label class="block font-bold mb-2 text-lg font-[Merienda]">Mot de passe</label>
                <input type="password" value="********" disabled
                    class="w-full p-2.5 rounded bg-white text-black border border-gray-400 font-[Merienda]">
            </div>
            <button
                class="bg-[#EB0000] font-bold text-white border-none py-2.5 px-3.5 text-sm cursor-pointer rounded mt-[35px] h-[47px] hover:bg-red font-[Merienda]"
                @click="showModal = true">Modifier</button>
        </div>

        <h2 class="text-lg font-normal flex items-center mt-6 font-[Merienda]">
            <span
                class=" text-lg inline-flex items-center justify-center w-6 h-6 bg-[#53a95a] text-black border border-[#53a95a] rounded-full font-bold text-lg mr-1 -translate-y-[1px]">✔</span>
            Mes informations personnelles
        </h2>
        <hr class="border-0 h-[2px] bg-[#26211b] my-4">


        <form @submit.prevent="submitUserInfo">
            <div class="block md:block md:flex justify-between gap-20">
                <div class="w-full text-lg">
                    <label class="block font-bold mb-2 font-[Merienda]">Nom</label>
                    <input type="text" v-model="userInfo.last_name"
                        class="w-full p-2.5 rounded bg-white text-black border border-gray-400 font-[Merienda]">
                </div>
                <div class="w-full text-lg">
                    <label class="block font-bold mb-2 font-[Merienda]">Prénoms</label>
                    <input type="text" v-model="userInfo.first_name"
                        class="w-full p-2.5 rounded bg-white text-black border border-gray-400 font-[Merienda]">
                </div>
            </div>

            <div class="block md:flex justify-between gap-20 mt-4">
                <div class="w-full text-lg">
                    <label class="block font-bold mb-2 font-[Merienda]">Date de Naissance</label>
                    <input type="date" v-model="userInfo.birth_date"
                        class="w-full p-2.5 rounded bg-white text-black border border-gray-400 font-[Merienda]">
                </div>
                <div class="w-full text-lg">
                    <label class="block font-bold mb-2 font-[Merienda]">Adresse postale</label>
                    <input type="text" v-model="userInfo.address"
                        class="w-full p-2.5 rounded bg-white text-black border border-gray-400 font-[Merienda]">
                </div>
            </div>

            <div class="mt-4 relative w-full">
                <label class="block font-bold mb-2 font-[Merienda]">Numéro de téléphone</label>
                <div class="relative">
                    <vue-tel-input v-model="userInfo.phone"
                        class="text-black w-full pl-10 p-2 rounded bg-white border border-gray-400 font-[Merienda]"
                        mode="international" :defaultCountry="'FR'" :inputOptions="{ showDialCode: true }" />
                    <!-- <input type="tel" v-model="userInfo.phone"
                        class="w-full pl-10 p-2 rounded bg-white text-black border border-gray-400 font-[Merienda]"> -->
                </div>
            </div>

            <div class="flex items-center gap-2 mt-5 text-lg">
                <input type="checkbox" id="policy"
                    class="w-[18px] h-[18px] rounded cursor-pointer bg-white accent-[#EB0000]"
                    v-model="userInfo.accepted_policy" />

                <label for="policy" class="font-[Merienda]">
                    J'accepte
                    <span class="text-[#EB0000] underline cursor-pointer">la politique de sauvegarde des
                        données
                        personnelles</span>
                </label>
            </div>

            <div class="flex items-center gap-2 mt-5 text-lg">
                <button type="submit"
                    class="bg-[#EB0000] text-white border-none py-2.5 px-5 text-lg cursor-pointer rounded mt-6 block w-fit relative ml-auto font-[Merienda]">
                    Sauvegarder les modifications
                </button>

            </div>

        </form>

        <Dialog v-model:visible="showModal" :modal="true" :closable="false" :dismissableMask="false"
            :closeOnEscape="false" :draggable="false" :style="{ width: '100%', maxWidth: '600px' }"
            class="w-full mx-2 sm:mx-auto custom-modal">
            <!-- En-tête -->
            <div
                class="font-[Merienda] bg-black text-white px-4 py-3 text-xl font-normal rounded-t-lg flex justify-between items-center w-full box-border">
                <h2>Changement de mot de passe</h2>
                <button class="text-xl fa fa-close" @click="showModal = false"></button>
            </div>

            <!-- Contenu -->
            <div class="bg-white font-[Merienda] text-lg">
                <form @submit.prevent="submitChangePassword()" class="grid gap-y-3 p-4 sm:p-6">
                    <label class="text-black">Ancien mot de passe</label>
                    <input class="bg-transparent px-4 py-2 border border-black text-black placeholder-black rounded-md"
                        type="password" v-model="oldPassword" placeholder="Ancien mot de passe" />
                    <label class="text-black">Nouveau mot de passe</label>
                    <input class="bg-transparent px-4 py-2 border border-black text-black placeholder-black rounded-md"
                        type="password" v-model="newPassword" placeholder="Nouveau mot de passe" />
                    <label class="text-black">Confirmation mot de passe</label>
                    <input class="bg-transparent px-4 py-2 border border-black text-black placeholder-black rounded-md"
                        type="password" v-model="confirmPassword" placeholder="Confirmation mot de passe" />

                    <div class="mt-4">
                        <button
                            class="bg-[#EB0000] hover:bg-red-700 text-white border-none px-5 py-2.5 text-lg cursor-pointer rounded-md block w-full sm:w-fit sm:ml-auto font-[Merienda]"
                            @click="updatePassword" :disabled="!isValid">
                            Changer mon mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </Dialog>

    </div>
</template>

<script setup>
import { ref, defineEmits, onMounted } from 'vue';
import { useAuthStore } from '../../store/authStore';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const emit = defineEmits(['switchForm']);
const authStore = useAuthStore();
const showModal = ref(false);
const oldPassword = ref(null);
const newPassword = ref(null);
const confirmPassword = ref(null);

const userInfo = ref({})

const isValid = computed(() =>
    oldPassword.value &&
    newPassword.value.length >= 6 &&
    newPassword.value === confirmPassword.value
)

const submitChangePassword = async () => {
    let request = await useApi().post('/user/change_password', {
        old_password: oldPassword.value,
        new_password: newPassword.value
    });

    toast(request.data.message, {
        position: "top-right",
        type: request.data.state,
        theme: "colored",
        timeout: 4000,
        closeOnClick: true,
        pauseOnHover: false,
        showCloseButtonOnHover: false,
        hideProgressBar: true,
    });

    if (request.data.state == "success") {
        oldPassword.value = null;
        newPassword.value = null;
        confirmPassword.value = null;
        showModal.value = false;
    }
}

const submitUserInfo = async () => {

    if (!userInfo.value.accepted_policy) {
        toast("⚠️ Vous devez accepter la politique de sauvegarde des données personnelles", {
            position: "top-right",
            type: "warning",
            theme: "colored",
            timeout: 4000,
            closeOnClick: true,
            pauseOnHover: false,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
        });
        return;
    }

    let data = userInfo.value;
    delete data.last_activity_at;
    let request = await useApi().post("user/update", {
        userInfo: data
    });

    if (request.data.state == 'success') {
        authStore.setLoginInfo({ user: request.data.user });
        toast(request.data.message, {
            position: "top-right",
            type: request.data.state,
            theme: "colored",
            timeout: 4000,
            closeOnClick: true,
            pauseOnHover: false,
            showCloseButtonOnHover: false,
            hideProgressBar: true,
        });
    }
}

onMounted(() => {
    setTimeout(() => {
        userInfo.value = authStore.user || {};
    }, 1000);
});


</script>
