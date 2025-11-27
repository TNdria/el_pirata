<template>
    <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center text-center sm:items-center sm:p-0">
                <div
                    class="top-[25px] bg-black p-8 rounded-xl max-w-[1000px] w-[90%] text-center relative border border-white ">
                    <span class="fa fa-close text-lg absolute text-white right-10 cursor-pointer"
                        @click="emit('close')"> </span>
                    <div>
                        <p class="success-message">Le <span class="text-[#EB0000] text-lg font-merienda">coffre</span>
                            est
                            <span class="text-[#EB0000] text-lg font-merienda">à toi…</span> Mais l’aventure <span
                                class="text-[#EB0000] text-lg font-merienda">continue !</span>
                        </p>

                        <p class="mb-5 text-white font-normal text-white text-lg">Le dernier secret du capitaine…</p>
                        <p class="mb-5 text-white font-normal text-white text-lg">
                            Bravo, flibustier ! Tu as traversé toutes les épreuves, résolu les énigmes et ouvert le
                            coffre
                            du capitaine…
                            Un véritable pirate sait que chaque trésor a son mystère.
                            Il te faudra encore un peu de patience !
                        </p>
                    </div>
                    <div class="block h-auto w-[300px] m-auto mb-4">
                        <img :src="'/images/chasses/coffre_ferme.png'" alt="coffre_ferme"
                            class="animate-wiggle-more animate-once">
                    </div>
                    <div class="relative w-[400px] m-auto">
                        <InputOtp v-model="optCode" :length="8" class="code_verification m-auto" />
                    </div>
                    <div class="relative mt-10">
                        <button class="btn-info font-semibold py-2 px-4 rounded-xl w-[300px]"
                            style="background-image: url('/images/chasses/bg-btn-tresor.jpg');">
                            Valider le code</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['close']);
const optCode = ref(null);

</script>
<style>
.code_verification input {
    height: 40px;
    width: 40px;
    border: 1px solid red;
    border-radius: 5px;
    margin: auto;
    background-color: #170F09;
}
</style>