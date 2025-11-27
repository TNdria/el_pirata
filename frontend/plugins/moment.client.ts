import moment from 'moment'
import 'moment/dist/locale/fr' // Si tu veux utiliser le français par exemple

export default defineNuxtPlugin(nuxtApp => {
    moment.locale('fr') // Met le moment en français
    // Utiliser un nom sans préfixe $ pour éviter les warnings Vue
    nuxtApp.provide('momentLib', moment)
})