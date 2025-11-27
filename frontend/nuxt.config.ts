import Aura from '@primeuix/themes/aura';
import svgLoader from 'vite-svg-loader'

export default defineNuxtConfig({
  compatibilityDate: '2024-11-01',
  vite: {
    plugins: [svgLoader()]
  },
  ssr: true,
  devtools: { enabled: true },
  devServer: {
    port: 3000,
    host: '0.0.0.0',
  },
  runtimeConfig: {
    // Accessibles côté client
    public: {
      baseUrl: process.env.NUXT_PUBLIC_BASE_URL,
      apiUrl: process.env.NUXT_PUBLIC_API_URL,
      encryptionKey: process.env.NUXT_KEY_ENCRYPTION,
    },
  },
  css: ["/assets/css/app.css", "vue3-carousel/dist/carousel.css"], // Charger le CSS globalement,
  modules: [
    '@nuxt/icon',
    '@nuxt/image',
    '@nuxt/scripts',
    '@nuxt/test-utils',
    '@nuxt/content',
    '@nuxt/eslint',
    'nuxt-vue3-google-signin',
    '@pinia/nuxt',
    '@primevue/nuxt-module'
  ],
  pinia: {
    storesDirs: ['./stores/**'],
  },
  googleSignIn: {
    clientId: '1006287136023-q8qe46q7mi5g8e601e5sne87mt9vjir6.apps.googleusercontent.com',
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
  // Configuration pour éviter les problèmes d'hydratation
  nitro: {
    experimental: {
      wasm: true
    }
  }
})