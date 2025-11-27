<template>

    <div class="bg-[#2b1b17] text-white font-merienda">
        <LazyLayoutMenu />

        <!-- SECTION HERO -->
        <section class="relative bg-cover bg-center h-[90vh] bg-[#EB0000]"
            style="background-image: url('/images/banner.jpg'); background-size: cover; background-position: center;">
            <!-- Overlay noir semi-transparent -->
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <!-- Contenu centré (titre, sous-titre, CTA) -->

            <div class="absolute bottom-4 px-4">
                <a href="/" class="text-lg text-[#EB0000]">Accueil > </a>
                <span class="text-lg text-[#EB0000]">Enigmes</span> >
                <span class="text-lg text-[#EB0000]">{{ nbr_resolved }}/{{ nbr_enigmas }}</span>
                <br><br>
                <h1 class="text-2xl text-white">L’ APPEL DU TRÉSOR MAUDIT</h1>
            </div>
        </section>
        <!-- END SECTION HERO -->

        <!-- Bloc Livre (à gauche) -->
        <div class="w-full relative overflow-hidden pt-4 pb-4 bg-[#170f09]">
            <h2 class="text-2xl text-white text-center mb-4">ENIGME {{ enigmasInfo.id }}</h2>

            <div class="w-full h-auto mx-auto flex d-flex grap-6 -mt-8">
                <img :src="'/images/chasses/livre.png'" alt="Papier" id="engimeBgPage" ref="engimeBgPage"
                    class="h-[75%] w-[75%] m-auto" />

                <div class="absolute flex flex-row w-full" :style="{ ...enigmePaperPostion }">
                    <div class="pt-[8%] w-[50%] w-max-[52%]  block rotate-[-11deg] text-black text-center"
                        @mouseup="$DragSelectedText" @dragstart="$DragSelectedText">
                        <div class="w-[75%] text-xs sm:text-sm md:text-sm lg:text-md  ml-auto" id="DescriptionEnigme"
                            v-html="`<h1 class='text-xl text-bold'>${enigmasInfo.title}</h1><br>${enigmasInfo.text_content}`">

                        </div>
                    </div>

                    <div class="w-[48%] flex flex-row items-start rotate-[-11deg] text-center">
                        <img :src="baseUrl + enigmasInfo.media" alt="Indice"
                            class="cursor-pointer h-[50px] w-[50px] md:h-[100px] md:w-[100px] lg:h-[150px] lg:w-[150px] m-auto mt-0 mb-0"
                            draggable="true" @dragstart="$DragMouving" v-if="enigmasInfo.media_type == 'image'" />
                    </div>
                </div>

            </div>

        </div>

        <section class="relative  text-center bg-[#000000] py-5">
            <h2 class="text-white text-4xl font-bold mb-6 font-merienda"> Inscrit ta réponse dans le parchemin
                <br> moussaillon !
            </h2>
            <div class="border-b-2 border-[#EB0000] w-[30%] m-auto"> </div>
            <div class="w-[70%]  m-auto mt-4">
                <img :src="'/images/chasses/fond-response.png'" alt="Papier" class="h-[500px] m-auto" />
                <textarea
                    class="absolute left-[50%] bottom-[350px] bg-transparent font-meridian placeholder-black text-black border-none focus:border-none hover:bg-transparent focus:bg-transparent w-[40%]"
                    placeholder="Votre réponse ..." style="transform: translateX(-50%);" v-model="answer"></textarea>

                <button class="absolute bottom-[125px] bg-[#EB0000] text-lg px-4 py-2 rounded-[25px] left-[50%]"
                    style="transform: translateX(-50%);" @click="verifyAnswer">Valider la réponse de l’énigme</button>
            </div>

            <!-- Livre magique -->
            <div class="fixed bottom-5 right-0 z-10" ref="floatingBook">
                <img :src="'/images/chasses/livre.png'" v-if="noteBookOpened" @click="noteBookOpened = !noteBookOpened"
                    alt="Indice" class="cursor-pointer w-[75px] md:w-[150px]" style="margin-right: 20px;" />
                <img :src="'/images/chasses/btn-livre.png'"
                    class="w-[75px] md:w-[150px] cursor-pointer  animate-bounce perspective-dramatic"
                    v-if="!noteBookOpened" @click="noteBookOpened = !noteBookOpened" alt="Indice"
                    style="margin-right: 20px;" />
            </div>

            <!-- Grand livre -->
            <div ref="noteBook" id="noteBook"
                class="overflow-hidden resize fixed h-[80%]  bottom-5 w-[600px] h-[600px] z-10" v-show="noteBookOpened">

                <img :src="'/images/chasses/block-note.png'" id="noteBookBckground" ref="noteBookBckground"
                    alt="block-note" class="absolute w-full h-full">

                <div class="absolute h-auto w-auto top-2 right-5" @click="noteBookOpened = !noteBookOpened">
                    <i class="fa fa-close text-lg text-black"></i>
                </div>

                <div class="flex flex-row px-[10%] grap-2 ">
                    <div @dragover.prevent @drop="(e) => $DragDroping(e, page)" :style="{ ...noteBookPosition }"
                        v-for="(page, i) in pagesBook" v-show="page == currentPage * 2 - 1 || page == currentPage * 2"
                        :class="(page == currentPage * 2 - 1 || page == currentPage * 2) ? 'h-[80%] pl-3 text-black z-20' : 'h-0 w-0'"
                        id="pageBook">
                        <span class="text">Page {{ page }}</span>

                        <p @click="$addTextarea($event)" :id="'addNote' + page"
                            class="text-center text-lg text-black mb-2 h-[10px] w-[10px] m-auto">
                            <i class="fa fa-pen"></i>
                        </p>
                    </div>

                </div>

                <div
                    class="absolute bottom-9 left-0 right-0 flex justify-between px-8 pointer-events-auto items-center text-black text-sm font-merienda font-bold max-w-[90%] mx-auto">
                    <!-- Navigation gauche -->
                    <div class="flex items-center gap-2" @click="PrevPage()">
                        <img :src="'/images/chasses/arrow.png'" alt="Précédent"
                            class="w-6 h-6 cursor-pointer transform rotate-180" />
                        <span> {{ currentPage * 2 - 1 }}</span>
                    </div>

                    <!-- Navigation droite -->
                    <div class="flex items-center gap-2" @click="NextPage()">
                        <span> {{ currentPage * 2 }}</span>
                        <img :src="'/images/chasses/arrow.png'" alt="Suivant" class="w-6 h-6 cursor-pointer" />
                    </div>
                </div>
            </div>
        </section>

        <section class="relative bg-[#170f09] pb-5 xs:block md:flex md:flex-row gap-5 px-[10%]">
            <div class="text-center w-full md:w-1/2 pt-5">
                <h2 class="text-2xl font-merienda">PROGRESSIONS</h2>
                <br>
                <img :src="'/images/chasses/pirate.png'" alt="PROGRESSIONS" class="w-[50%] m-auto animate-pulse">
                <div class="highlight"></div>
                <label class="label text-white text-2xl font-merienda">{{ (nbr_resolved > 0 && nbr_enigmas > 0) ?
                    nbr_resolved / nbr_enigmas * 100 : 0 }}%</label>
            </div>

            <div class="w-full md:w-1/2 pt-5">
                <Tresorgagner ref="tresorRef" />
            </div>

        </section>

        <LazyValidationReponseChasse v-if="responseFoundedModal" :responseEnigmasIsCorrect="responseEnigmasIsCorrect"
            @close="responseFoundedModal = false" :enigmasId="enigmasInfo.id" :UniqueCode="UniqueCode" />

        <LazyLayoutFooter />

    </div>
</template>
<script setup>
import { ref, onMounted, watch, reactive } from 'vue';
const { $dragElement, $DragMouving, $DragDroping, $addTextarea, $DragSelectedText } = useNuxtApp();
const noteBookSize = reactive({
    width: 300,
    height: 300,
    value: null
})
const config = useRuntimeConfig();
const baseUrl = config.public.baseUrl;

definePageMeta({
    middleware: 'auth'
})

const noteBookOpened = ref(false);
const currentPage = ref(1);
const noteBook = ref(null);
const noteBookBckground = ref(null);
const noteBookPosition = ref({});
const pagesBook = ref([1, 2]);
const engimeBgPage = ref(null);
const enigmePaperPostion = ref({});
const enigmasInfo = ref({});
const floatingBook = ref(null);
const responseFoundedModal = ref(false);
const responseEnigmasIsCorrect = ref(false);
const answer = ref(null);
const tresorRef = ref(null);
const nbr_resolved = ref(0);
const nbr_enigmas = ref(0);

let enigmaId = null;
const route = useRoute();
const preview = route.query.preview;
// => "true"

const getEnigmabyUser = async () => {
    if (!preview) {
        let enigmas = await useApi().post('/user/enigmas/getEnigma', {
            hunting_id: null
        });

        nbr_resolved.value = enigmas.data.nbr_resolved || 0;
        nbr_enigmas.value = enigmas.data.nbr_enigmas || 0;

        if (enigmas.data.enigma) {
            enigmaId = enigmas.data.enigma.id
            EnigmasById(enigmaId);
        }
    } else {
        EnigmasById(route.query.enigma_id)
    }

}

const EnigmasById = async (id) => {
    let enigmaInfo = await useApi().post(!preview ? '/user/enigmas/info' : '/enigmas/preview', {
        hunting_id: null,
        id: id
    });

    if (enigmaInfo.data.enigma) {
        let enigmaInfoData = enigmaInfo.data.enigma;
        enigmasInfo.value.text_content = enigmaInfoData.text_content;
        enigmasInfo.value.media = enigmaInfoData.media;
        enigmasInfo.value.media_type = enigmaInfoData.media_type;
        enigmasInfo.value.title = enigmaInfoData.title;
        enigmasInfo.value.id = enigmaInfoData.id;
    }
}

const UniqueCode = ref(null);

const verifyAnswer = async (id) => {
    let answerResponse = await useApi().post('/user/enigmas/check/answer', {
        id_engima: enigmaId,
        answer: answer.value
    });

    if (answerResponse.data.is_correct) {
        tresorRef.value.codeFetching();
        getEnigmabyUser();
    }

    responseEnigmasIsCorrect.value = answerResponse.data.is_correct;
    responseFoundedModal.value = true;
    UniqueCode.value = answerResponse.data.unique_code;
}


onMounted(() => {
    if (process.client) {
        $dragElement(noteBook.value);
        $dragElement(floatingBook.value);
        reactiveEnigmePaper();
        window.addEventListener('resize', reactiveEnigmePaper);
        window.addEventListener('resize', reactiveNoteBookpaper);

        const observer = new ResizeObserver(() => {
            reactiveNoteBookpaper();
        });

        observer.observe(noteBook.value);
        getEnigmabyUser();
    }
});

const NextPage = () => {
    const startLength = pagesBook.value.length;
    const startPagesBook = pagesBook.value; // copie de la liste
    const targetPage = currentPage.value * 2 + 1;

    if (!startPagesBook.includes(targetPage)) {
        for (let i = 1; i <= 2; i++) {
            pagesBook.value.push(startLength + i);
        }
    }

    currentPage.value++;
}

const PrevPage = () => {
    if (currentPage.value > 1) {
        currentPage.value--;
    }
}

watch(noteBookOpened, (newValue, oldValue) => {
    if (newValue) {
        setTimeout(() => {
            reactiveNoteBookpaper();
        }, 50);

    }
    console.log('noteBookOpened changed:', newValue);
    // Ajoutez ici ce que vous souhaitez faire lorsque noteBookOpened change.
});

const reactiveNoteBookpaper = () => {
    if (noteBookBckground.value) {
        noteBookPosition.value.height = (noteBookBckground.value.offsetHeight - noteBookBckground.value.offsetHeight * 0.22) + 'px';
        noteBookPosition.value['max-width'] = (noteBookBckground.value.offsetWidth * 0.4) + 'px';
        noteBookPosition.value['min-width'] = (noteBookBckground.value.offsetWidth * 0.4) + 'px';
        noteBookPosition.value['margin-top'] = (noteBookBckground.value.offsetWidth * 0.09) + 'px';
    }

}

const reactiveEnigmePaper = () => {
    if (engimeBgPage.value) {
        enigmePaperPostion.value.width = (engimeBgPage.value.offsetWidth * 0.9) + 'px';
        enigmePaperPostion.value.height = (engimeBgPage.value.offsetHeight * 0.8) + 'px';
        enigmePaperPostion.value.top = (engimeBgPage.value.offsetHeight * 0.20 + engimeBgPage.value.offsetTop) + 'px'; // position par rapport à la page
        enigmePaperPostion.value.left = '50%';
        enigmePaperPostion.value.transform = 'translateX(-50%)';
    }
}

</script>

<style>
html,
body {
    height: 100%;
}

.resize {
    resize: both;
    overflow: auto;
}
</style>