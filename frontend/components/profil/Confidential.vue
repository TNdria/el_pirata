<template>
  <div class="flex-grow bg-[#160f08] text-white p-8 font-[Merienda]">
    <h2 class="mb-4 text-lg mt-10">
      <span
        class="inline-flex items-center justify-center w-6 h-6 bg-[#53a95a] text-black rounded-full text-base mr-0.5 relative -translate-y-[1px]">✔</span>
      Sécurité et Confidentialité
    </h2>
    
    <hr class="border-0 h-0.5 bg-[#26211b] my-4" />

    <!-- 2FA -->
    <div class="flex items-center justify-between w-full mb-4">
      <div class="text-md font-[Merienda]">
        <p>
          Activer l’authentification à deux facteurs (2FA)<br>
          Activez cette option pour renforcer la sécurité de votre compte en demandant un code supplémentaire lors de la
          connexion.
        </p>
      </div>
      <InputSwitch v-model="settings.two_factor_auth" class="custom-switch" />
    </div>

    <!-- Confidentialité -->
    <div class="flex items-center justify-between w-full mb-4 font-[Merienda]">
      <div class=" text-md text-left">
        <p>
          Paramètres de confidentialité<br>
          Choisissez quelles informations seront visibles par les autres joueurs.
        </p>
      </div>
    </div>

    <div class="flex flex-col gap-2 ">
      <div class="flex justify-between items-center font-[Merienda] mb-3">
        <p class="text-md">Profil visible aux autres joueurs</p>
        <InputSwitch v-model="settings.profile_visible" class="custom-switch" />
      </div>
      <div class="flex justify-between items-center font-[Merienda] mb-3">
        <p class="text-md">Statut en ligne visible</p>
        <InputSwitch v-model="settings.online_status" class="custom-switch" />
      </div>
      <div class="flex justify-between items-center font-[Merienda] mb-3">
        <p class="text-md">Succès visible</p>
        <InputSwitch v-model="settings.achievements_visible" class="custom-switch" />
      </div>
    </div>

    <!-- Notifications -->
    <div class="flex items-center justify-between w-full  mt-6">
      <div class="text-md  text-left font-[Merienda]">
        <p>
          Gérer les notifications<br>
          Indiquez comment vous souhaitez être informé des mises à jour et événements importants.
        </p>
      </div>
    </div>

    <div class="flex flex-col gap-2 ">
      <div class="flex justify-between items-center font-[Merienda]">
        <p class="text-md">Recevoir des notifications par e-mail</p>
        <InputSwitch v-model="settings.email_notifications" class="custom-switch" />
      </div>
      <div class="flex justify-between items-center font-[Merienda]">
        <p class="text-md">Recevoir des notifications par SMS</p>
        <InputSwitch v-model="settings.sms_notifications" class="custom-switch" />
      </div>
    </div>

    <!-- Suppression de compte -->
    <div class="mt-8 font-[Merienda]">
      <label class="text-[#EB0000]  text-md font-[Merienda] font-bold mb-3">Suppression du compte</label>
      <div class="w-full  text-md">
        <label>⚠ Attention : cette action est irréversible et entraînera la suppression définitive de votre compte et de
          toutes vos données.</label>
      </div>

      <div class="flex items-center gap-2 mt-2">
        <input type="checkbox" v-model="remove_account" id="policy"
          class="w-[18px] h-[18px] rounded cursor-pointer bg-white accent-red-600" />
        <label for="policy" class="text-md">Je souhaite supprimer mon compte</label>
      </div>
    </div>

    <div class="mt-4">
      <button
        class="bg-[#EB0000] hover:red text-white border-none px-5 py-2.5 text-lg cursor-pointer rounded-md block w-fit relative ml-auto font-[Merienda]"
        @click="submitSetting">
        Enregistrer les paramètres
      </button>
    </div>

    <!-- Boîte de confirmation -->
    <ConfirmDialog />

  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { useAuthStore } from '~/store/authStore';

const remove_account = ref(false);
const confirm = useConfirm();
const settings = ref({
  two_factor_auth: true,
  profile_visible: true,
  online_status: false,
  achievements_visible: true,
  email_notifications: true,
  sms_notifications: false,
});

const fecthSetting = async () => {
  let querySetting = await useApi().get('/user/setting/find');
  settings.value = querySetting.data.setting;
}

const submitSetting = async () => {

  if (remove_account.value) {
    // Afficher la boîte de confirmation
    confirm.require({
      message: 'Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.',
      header: 'Confirmation de suppression',
      icon: 'fa fa-exclamation-triangle',
      acceptLabel: 'Oui, supprimer',
      rejectLabel: 'Annuler',
      accept: async () => {
        let queryDellet = await useApi().post('/user/me/delete', {});

        if (queryDellet.data.state == "success") {
          useAuthStore().logout();
        }

      },
      reject: () => {
        // Décocher si refus
        remove_account.value = false
      }
    });

    return null;
  }

  let querySetting = await useApi().post('/user/setting/createOrupdate', {
    settingsInfo: settings.value
  });


  toast(querySetting.data.message, {
    position: "top-right",
    type: querySetting.data.state,
    theme: "colored",
    timeout: 4000,
    closeOnClick: true,
    pauseOnHover: false,
    showCloseButtonOnHover: false,
    hideProgressBar: true,
  });

  if (querySetting.data.state == "success") {
    fecthSetting();
  }
}

onMounted(() => {
  fecthSetting();
});

</script>

<style scoped>
.custom-switch .p-toggleswitch-input {
  background: blue;
}
</style>