<template>
  <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
      <div class="flex h-[70%] items-end justify-center text-center sm:items-center sm:p-0">
        <div class="top-[35%] bg-black p-8 rounded-xl max-w-[1000px] w-[90%] text-center relative border border-white ">
          <span class="fa fa-close text-lg absolute text-white right-10 cursor-pointer" @click="emit('close')"> </span>

          <div class="w-[200px] m-auto">
            <img :src="'/images/chasses/pirate.png'" alt="Success Image" class="animate-jump-in animate-twice">
          </div>

          <div>
            <p class="text-white mb-5 text-lg" v-if="isCorrect">BIEN JOUÉ, <span
                class="text-[#EB0000] text-lg font-merienda">BRAVO!</span></p>

            <p class="text-white mb-5 text-lg" v-if="!isCorrect"><span
                class="text-[#EB0000] text-lg font-merienda">PERDU !</span></p>

            <p class="mb-10 text-white text-base" v-if="isCorrect">
              Tu as trouvé l’énigme numéro {{ IdEnigma }}.<br>
              Le code t’aidera à ouvrir le coffre après avoir réussi à résoudre toutes les énigmes.
            </p>
            <p class="mb-1 text-white text-base" v-if="!isCorrect">
              Ton navire va couler, ce n’est pas la bonne solution.<br>
              Soit persévérant comme un pirate et lis bien l’énigme.
            </p>
          </div>

          <div v-if="isCorrect">
            <div class="flex m-auto align-center items-center justify-center gap-x-10">
              <img :src="'/images/chasses/img-code.png'" alt="" srcset="" class="w-[125px] h-[125px]">
              <p> {{ code }} </p>
              <img :src="'/images/chasses/img-code.png'" alt="" srcset="" class="w-[125px] h-[125px]">
            </div>
          </div>

          <div class="w-[200px] m-auto">
            <img src="/images/icon_done.png" alt="Done" class="m-auto" v-if="isCorrect">
            <img src="/images/icon_fail.png" alt="Done" class="m-auto" v-if="!isCorrect">
          </div>

          <div class="success-footer-message">
            <p class="text-white text-base">Les codes sont conservés dans l’espace « MES TRESORS GAGNÉS ».</p>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['close']);
const isCorrect = ref(false);
const IdEnigma = ref(false);
const code = ref(null);
import { watch } from 'vue'

// Déclaration de la prop
const props = defineProps({
  responseEnigmasIsCorrect: Boolean,
  enigmasId: Number,
  UniqueCode: String
})

watch(() => props.responseEnigmasIsCorrect, (newVal, oldVal) => {
  isCorrect.value = newVal;
  console.log(newVal);
}, { immediate: true });

watch(() => props.responseEnigmasIsCorrect, (newVal, oldVal) => {
  IdEnigma.value = newVal;
}, { immediate: true });

watch(() => props.UniqueCode, (newVal, oldVal) => {
  console.log(newVal);
  
  if (newVal) {
    code.value = newVal;
  }

}, { immediate: true });

</script>