<template>
    <div class="max-w-4xl mx-auto mt-5" v-if="tresor.id">
        <div class="container">
            <div class="grid grid-cols-2 gap-4 xs:grid-cols-1 xs:gap-1 text-center">
                <div class="flex justify-center items-center rounded-2xl bg-[#58575740] w-full h-[75px]">
                    <div>
                        <p>Prix d’Inscription</p>
                        <p> {{ tresor.registration_fee }} €</p>
                    </div>
                </div>
                <div class="flex justify-center items-center rounded-2xl bg-[#58575740] w-full h-[75px]">
                    <div>
                        <p>Départ</p>
                        <p class="capitalize"> {{ new Date(tresor.start_date).toLocaleDateString("fr-FR", {
                            weekday: "long",
                            year: "numeric",
                            month: "long",
                            day: "numeric"
                        }) || null }} </p>
                    </div>
                </div>
                <div class="flex justify-center items-center rounded-2xl bg-[#58575740] m-auto w-50 h-[40px]  grap-2">
                    <p> Niveau : {{ levelOptionsMap[tresor.level] }} </p>
                </div>
                <div class="flex justify-center items-center rounded-2xl bg-[#58575740] m-auto w-50 h-[40px] grap-2">
                    <p> <i class="fa fa-users" aria-hidden="true"></i> +0/{{ tresor.place_limit }} inscrit</p>
                </div>
            </div>
            <div class="rounded-2xl xs:grid-cols-1 grid grid-cols-5  h-[75px] bg-[#58575740] mt-4">
                <div
                    class="text-center flex justify-center items-center m-auto w-full grap-2 border-r border-white-500 h-50">
                    <p> {{ countdownTimer.months }} <span class="text-[#EB0000]"> Mois </span> </p>
                </div>
                <div
                    class="text-center flex justify-center items-center m-auto w-full grap-2 border-r border-white-500 h-50">
                    <p> {{ countdownTimer.days }} <span class="text-[#EB0000]"> Jour </span> </p>
                </div>
                <div
                    class="text-center flex justify-center items-center m-auto w-full grap-2 border-r border-white-500 h-50">
                    <p> {{ countdownTimer.hours }} <span class="text-[#EB0000]"> Heures </span> </p>
                </div>
                <div
                    class="text-center flex justify-center items-center m-auto w-full grap-2 border-r border-white-500 h-50">
                    <p> {{ countdownTimer.minutes }} <span class="text-[#EB0000]"> Minutes </span> </p>
                </div>
                <div class="text-center flex justify-center items-center m-auto w-full grap-2">
                    <p> {{ countdownTimer.seconds }} <span class="text-[#EB0000]"> Secs </span> </p>
                </div>
            </div>

            <div class="w-full mt-5 text-center">
                <a href="#" class="inline-flex  items-center text-center bg-[#EB0000] text-white
                            px-[4em] py-2
                            rounded-lg sm:rounded-xl
                            hover:bg-[#CC1F1A] transition-all duration-300">
                    <span class="text-base sm:text-lg font-merienda">S’inscrire </span>

                </a>
            </div>
        </div>
    </div>
</template>
<script setup>
import { useCountdown } from "@/composables/useCountdown";
import { watch, ref } from 'vue';

const props = defineProps({
    hunting: Object
});

const levelOptionsMap = {
    easy: 'Facile',
    normal: 'Normal',
    hard: 'Difficile'
}

const tresor = ref({});

// Watch sur la prop hunting
watch(
    () => props.hunting, // source à surveiller
    (newVal, oldVal) => {
        tresor.value = newVal;
    },
    { deep: true, immediate: true } // si c'est un objet et qu'on veut détecter les changements internes
);

const { countdownTimer } = useCountdown(tresor.value.start_date);
</script>
