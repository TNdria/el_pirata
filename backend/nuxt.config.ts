import Aura from '@primeuix/themes/aura';

export default defineNuxtConfig({
  ssr: true,
  compatibilityDate: '2024-11-01',

  devtools: { enabled: false },

  devServer: {
    port: 3002,          // le port exposé par Docker
    host: '0.0.0.0'
  },

  experimental: {
    payloadExtraction: false
  },

  runtimeConfig: {
    public: {
      baseUrl: process.env.NUXT_PUBLIC_BASE_URL,
      apiUrl: process.env.NUXT_PUBLIC_API_URL || "http://backend:8000/api",
      encryptionKey: process.env.NUXT_KEY_ENCRYPTION,
    },
  },

  modules: [
    '@nuxt/ui',
    '@pinia/nuxt',
    '@primevue/nuxt-module'
  ],

  css: [
    '~/assets/css/critical.css',
    '~/assets/css/main.css',
    'vue3-toastify/dist/index.css'
  ],

  pinia: {
    storesDirs: ['./store/**'],
  },

  primevue: {
    options: {
      theme: { preset: Aura },
      locale: {
        dayNames: ['dimanche', 'lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi'],
        dayNamesShort: ['dim', 'lun', 'mar', 'mer', 'jeu', 'ven', 'sam'],
        dayNamesMin: ['D', 'L', 'M', 'M', 'J', 'V', 'S'],
        monthNames: ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
        monthNamesShort: ['janv', 'févr', 'mars', 'avr', 'mai', 'juin', 'juil', 'août', 'sept', 'oct', 'nov', 'déc'],
        today: "Aujourd'hui",
        clear: 'Effacer',
        dateFormat: 'dd/mm/yy',
        weekHeader: 'Sem',
        fileSizeTypes: ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']
      }
    }
  },

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
