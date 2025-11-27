<template>
  <ClientOnly>
    <Dialog :modal="true" :closable="true" :dismissableMask="true" :style="{ width: '80%' }" :draggable="false"
      class="custom-modal font-merienda">
    <div class="flex w-full rounded-lg bg-white shadow-lg p-2 gap-4">
      <!-- PARTIE GAUCHE -->
      <div class="w-1/3 rounded-l-lg bg-[#170F09] p-8 text-white">
        <h2 class="mb-4 text-3xl font-bold">Contactez-nous</h2>
        <p class="mb-8 text-md">Vous avez une question, un projet ou besoin d'assistance ? N'hésitez pas à nous écrire
          via le formulaire ci-dessous ou à utiliser nos coordonnées directes.</p>
        <div class="mb-8 flex justify-center">
          <img :src="'/images/images_appele/pirate.png'" alt="Pirate Skull" class="h-[250px] w-[250px]" />
        </div>
        <p class="mb-8 text-center text-xl font-semibold">El <span class="text-[#EB0000]">Pirata</span></p>
        <div class="mb-4 flex items-center text-md md:text-lg">
          <i class="fa-solid fa-phone-volume mr-3 h-5 w-5 text-white"></i>
          <span>+1012 3456 789</span>
        </div>
        <div class="mb-4 flex items-center text-md md:text-lg">
          <i class="fa-solid fa-envelope mr-3 h-5 w-5 text-white"></i>
          <span>elpirata@gmail.com</span>
        </div>
        <div class="mb-4 flex items-center text-md md:text-lg">
          <i class="fa-solid fa-location-dot mr-3 h-5 w-5 text-white"></i>
          <span>10 Rue de la Liberté 75001 Paris France</span>
        </div>
        <div class="flex items-center text-md md:text-lg">
          <i class="fa-solid fa-clock mr-3 h-5 w-5 text-white"></i>
          <span>mardi au vendredi de 10h à 16h</span>
        </div>
      </div>

      <!-- FORMULAIRE -->
      <div class="w-2/3 p-8">
        <form @submit.prevent="sendForm">
          <div class="mb-6 flex space-x-6">
            <div class="w-1/2">
              <label for="last_name" class="mb-2 block text-md font-medium text-black">Nom</label>
              <input v-model="form.last_name" type="text" id="last_name"
                class="w-full border-0 border-b border-black p-2 focus:border-b-red-500 focus:ring-0" />
              <span class="text-red-500 text-sm" v-if="v$.form.last_name.$error">Nom requis</span>
            </div>
            <div class="w-1/2">
              <label for="first_name" class="mb-2 block text-md font-medium text-black">Prénom</label>
              <input v-model="form.first_name" type="text" id="first_name"
                class="w-full border-0 border-b border-black p-2 focus:border-b-red-500 focus:ring-0" />
              <span class="text-red-500 text-sm" v-if="v$.form.first_name.$error">Prénom requis</span>
            </div>
          </div>

          <div class="mb-6 flex space-x-6">
            <div class="w-1/2">
              <label for="email" class="mb-2 block text-md font-medium text-black">E-mail</label>
              <input v-model="form.email" type="email" id="email"
                class="w-full border-0 border-b border-black p-2 focus:border-b-red-500 focus:ring-0" />
              <span class="text-red-500 text-sm" v-if="v$.form.email.$error">E-mail valide requis</span>
            </div>
            <div class="w-1/2">
              <label for="phone" class="mb-2 block text-md font-medium text-black">Téléphone</label>
              <input v-model="form.phone" type="tel" id="phone"
                class="w-full border-0 border-b border-black p-2 focus:border-b-red-500 focus:ring-0" />
            </div>
          </div>

          <div class="mb-6">
            <label for="subject" class="mb-2 block text-md font-medium text-black">Objet</label>
            <input v-model="form.subject" type="text" id="subject"
              class="w-full border-0 border-b border-black p-2 focus:border-b-red-500 focus:ring-0" />
            <span class="text-red-500 text-sm" v-if="v$.form.subject.$error">Objet requis</span>
          </div>

          <div class="mb-6">
            <label for="message" class="mb-2 block text-md font-medium text-black">Message</label>
            <textarea v-model="form.message" id="message" rows="5" placeholder="Écrire votre message"
              class="w-full border-0 border-b border-black p-2 focus:border-b-red-500 focus:ring-0"></textarea>
            <span class="text-red-500 text-sm" v-if="v$.form.message.$error">Message requis</span>
          </div>

          <div class="mb-4 flex items-center">
            <input v-model="form.terms_accepted" type="checkbox" id="terms_accepted"
              class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500" />
            <label for="terms_accepted" class="ml-2 text-md text-black">Accepter les CGU / Politique de
              confidentialité</label>
            <span class="text-red-500 text-sm ml-4" v-if="v$.form.terms_accepted.$error">Obligatoire</span>
          </div>

          <div class="mb-8 flex items-center">
            <input v-model="form.captcha_checked" type="checkbox" id="captcha_checked"
              class="h-4 w-4 rounded border-gray-300 text-red-600 focus:ring-red-500" />
            <label for="captcha_checked" class="ml-2 text-md text-black">CAPTCHA pour éviter les spams</label>
            <span class="text-red-500 text-sm ml-4" v-if="v$.form.captcha_checked.$error">Obligatoire</span>
          </div>

          <button type="submit" :disabled="isLoading"
            class="float-right flex items-center justify-center gap-2 rounded-md bg-[#EB0000] px-6 py-2 text-white hover:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
            <span v-if="!isLoading">Envoyer</span>
            <span v-else class="flex items-center gap-2">
              <svg class="h-4 w-4 animate-spin text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
              </svg>
              Envoi...
            </span>
          </button>

        </form>
      </div>
    </div>
  </Dialog>
  </ClientOnly>
</template>

<script setup>
import { ref, defineProps, defineEmits, watch } from 'vue'
import { useVuelidate } from '@vuelidate/core'
import { requiredField, validEmail } from '../composables/validators';
import { toast } from "vue3-toastify";
import { useAuthStore } from '../store/authStore'

const props = defineProps({
  isVisible: Boolean,
});

const isLoading = ref(false);
const emit = defineEmits(['close'])
const authStore = useAuthStore();
const form = ref({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  subject: '',
  message: '',
  terms_accepted: false,
  captcha_checked: false
});

// Schéma de validation
const rules = {
  form: {
    first_name: { requiredField },
    last_name: { requiredField },
    email: { requiredField, validEmail },
    phone: {},
    subject: { requiredField },
    message: { requiredField },
    terms_accepted: { requiredField },
    captcha_checked: { requiredField }
  }
}

watch(() => props.isVisible, (val) => {
  if (val) {
    if (authStore.isAuthenticated) {
      form.value.first_name = authStore.user.first_name;
      form.value.last_name = authStore.user.last_name;
      form.value.email = authStore.user.email;
      form.value.phone = authStore.user.phone;
    }
  }
})

// Activer Vuelidate
const v$ = useVuelidate(rules, { form })

const sendForm = async () => {
  const isValid = await v$.value.$validate()

  if (!isValid) return;

  isLoading.value = true;

  try {
    const request = await useApi().post('/contact/us', { ...form.value });

    toast(request.data.message, {
      position: 'top-right',
      type: request.data.state,
      theme: 'colored',
      timeout: 4000,
      closeOnClick: true,
      pauseOnHover: false,
      showCloseButtonOnHover: false,
      hideProgressBar: true,
    });

    if (request.data.state === 'success') {
      emit('close')
    }
  } catch (error) {

  } finally {
    isLoading.value = false;
  }
}
</script>

<style scoped>
input,
textarea {
  background: transparent !important;
  color: black !important;
}
</style>
