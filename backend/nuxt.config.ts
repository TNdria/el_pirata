import Aura from '@primeuix/themes/aura';

export default defineNuxtConfig({
  ssr: true,
  compatibilityDate: '2024-11-01',
  devtools: { enabled: false },
  devServer: {
    port: 3002,
    host: '0.0.0.0'
  },
  // Optimisation pour éviter le FOUC
  experimental: {
    payloadExtraction: false
  },
  runtimeConfig: {
    // Accessibles côté client
    public: {
      baseUrl: process.env.NUXT_PUBLIC_BASE_URL,
      apiUrl: process.env.NUXT_PUBLIC_API_URL,
      encryptionKey: process.env.NUXT_KEY_ENCRYPTION,
    },
  },
  modules: [
    '@nuxt/scripts',
    '@nuxt/ui',
    '@nuxt/test-utils',
    // '@nuxt/image',
    '@pinia/nuxt',
    '@primevue/nuxt-module'
  ],
  css: [
    // Styles critiques pour éviter le FOUC
    '~/assets/css/critical.css',
    // Styles personnalisés
    '~/assets/css/main.css',
    // Styles vue3-toastify
    'vue3-toastify/dist/index.css'
  ],
  pinia: {
    storesDirs: ['./store/**'],
  },
  primevue: {
    options: {
      theme: {
        preset: Aura
      },
      locale: {
        dayNames: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
        dayNamesShort: ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        monthNames: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
        monthNamesShort: ['janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'],
        today: 'Aujourd\'hui',
        clear: 'Effacer',
        dateFormat: 'dd/mm/yy',
        weekHeader: 'Sem',
        fileSizeTypes: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
      }
    },
  },
  // Configuration pour éviter les conflits CSS
  vite: {
    css: {
      preprocessorOptions: {
        scss: {
          additionalData: `@import "~/assets/scss/variables.scss";`
        }
      }
    }
  }
})