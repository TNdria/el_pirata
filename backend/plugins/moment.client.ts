import moment from 'moment'
import 'moment/locale/fr' // Si tu veux utiliser le français par exemple

export default defineNuxtPlugin(nuxtApp => {
    moment.updateLocale('fr', {
        relativeTime: {
            future: "dans %s",
            past: "il y a %s",
            s: "quelques secondes",
            m: "une minute",
            mm: "%d minutes",
            h: "une heure",
            hh: "%d heures",
            d: "un jour",
            dd: "%d jours",
            M: "un mois",
            MM: "%d mois",
            y: "un an",
            yy: "%d ans"
        }
    });

    moment.locale('fr') // Met le moment en français

    return {
        provide: {
            moment
        }
    }
})