<template>
    <h2 class="text-2xl font-merienda mb-3">MES TRESORS GAGNÉS</h2>
    <div class="border-1 border-[#EB0000] rounded-xl block pb-4">
        <div class="pb-4 pt-2">
            <p class="text-lg font-merienda border-b-2 border-[#FFFFFF] px-2 py-2 bg-black" v-for="code in codeList">
                Code Enigme {{ code.enigma_number }} : {{ code.code }}
            </p>

        </div>

        <img :src="'/images/chasses/img-code.png'" alt="code" class="w-[90px] h-[90px] m-auto mt-4 ">
        <p class="text-lg text-center">Ton Code pour ouvrir le fameux coffre au trésor <br>
            <span class="text-lg text-[#EB0000]">972664848499</span>
        </p>
        <div class="flex justify-center">
            <button style="background-image: url('/images/chasses/bg-btn-tresor.jpg');background-repeat: no-repeat;"
                class="py-2 px-4 rounded-[25px] mt-4" @click="boXModalOpened = !boXModalOpened">
                Voir les coffres au trésor
            </button>
        </div>
        <LazyCoffreTresor v-show="boXModalOpened" @close="boXModalOpened = false" />
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';

const codeList = ref([]);
const boXModalOpened = ref(false);

const codeFetching = async () => {
    try {
        let queryResponse = await useApi().get('/user/enigmas/code/list');
        codeList.value = queryResponse.data.codes;
    } catch (error) {

    }

}

onMounted(() => {
    codeFetching();
})

// Expose la méthode pour le parent
defineExpose({
    codeFetching
});
</script>