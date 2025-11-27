<template>
  <div class="bg-[#000000]">
    <p class="text-white text-2xl lg:text-2xl md:text-2xl sm:text-2xl font-semibold">Êtes-vous sûr de vouloir vous
      déconnecter ?</p>
    <button @click="confirm"
      style="background-color: #EB0000; float: right; color: white; padding: 5px 10px; border: none; cursor: pointer;">
      Confirmer
    </button>
  </div>
</template>

<script>
import { useAuthStore } from '../../store/authStore'

export default {
  methods: {
    // Fonction de confirmation de la déconnexion
    confirm() {
      const auth = useAuthStore()
      auth.logout(); // Appeler la méthode de déconnexion du store Pinia
      window.location.href = "/";
    },
  },
};
</script>