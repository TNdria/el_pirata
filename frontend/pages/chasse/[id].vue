<template>

    <div class="bg-[#2b1b17] text-white">
        <LazyLayoutMenu />
        <!-- SECTION HERO -->
        <section class="relative bg-cover bg-center h-[90vh] font-merienda bg-[#EB0000]"
            style="background-image: url('/images/banner.jpg'); background-size: cover; background-position: center;">
            <!-- Overlay noir semi-transparent -->
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <!-- Contenu centré (titre, sous-titre, CTA) -->
            <div
                class="container mx-auto relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
                <img src="/images/pirate.png" alt="pirate" class="w-32 h-32 mb-6 object-contain mx-auto">
                <!-- Ajuste la taille avec w-32 h-32 -->

                <h1 class="text-4xl lg:text-4xl header-title text-[#FFFFFF] mb-4">
                     L’appel du <span class="text-[#EB0000]">trésor</span> maudit
                </h1>

                <p class="text-xl lg:text-2xl mb-6 font-merienda">
                    Partez à l’aventure dans l’univers numérique pour gagner le trésor sans sortir de chez vous !
                </p>
            </div>
        </section>
        <!-- END SECTION HERO -->

        <!-- SECTION VIDÉO & DESCRIPTION -->
        <section class="py-16 bg-[#FEA250]/12">
            <div class="container mx-auto px-4">
                <!-- Texte centré en haut -->
                <div class="text-center w-75 mx-auto mb-12 font-merienda font-[300]">
                    <p class="text-[#EB0000] text-4xl md:text-4xl sm:text-2xl font-bold">
                        {{ hunting.title }}
                    </p>
                </div>

                <!-- Conteneur vidéo avec coins arrondis et bordure blanche -->
                <div class="max-w-4xl mx-auto">
                    <div class="relative rounded-[2rem] bg-white p-3">
                        <!-- Ratio 16:9 pour la vidéo -->
                        <div class="relative w-full rounded-[1.5rem] overflow-hidden" style="padding-top: 56.25%;">
                            <!-- Contrôles vidéo personnalisés -->
                            <div class="absolute inset-0 bg-black">
                                <LazyVideoPlayer :urlVideo="'/video/video.mp4'" />
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="flex justify-center items-center flex-col sm:gap-4 p-4 sm:mt-4">
                <p class="text-[#FFFFFF] text-xl text-center pl-6 mr-6 p-8 sm:w-[80%]">
                    Laissez-vous transporter par des paysages mystérieux, des cartes énigmatiques et l’ombre d’un
                    trésor enfoui Chaque image vous rapproche un peu plus de l’aventure…
                </p>
                <h1 class="sm:text-3xl text-2xl text-[#EB0000] sm:mt-4 sm:mb-6 font-bold">Montant du trésor à gagner
                </h1>
                <div class="relative flex justify-center items-center w-fit m-4">
                    <img src="/images/images_appele/back-tresor.png" alt="back-tresor" class="w-[36rem] brightness-75">
                    <div class=" flex flex-col z-1 justify-center items-center absolute  w-full h-[90%] -top-5 ">
                        <img src="/images/images_appele/gold.png" alt="gold" class="w-[15rem]">
                        <p class="text-4xl font-bold text-[#EB0000] text-center ">{{ hunting.prize_win }} €</p>
                    </div>
                </div>
                <div
                    class="rounded-3xl bg-[#58575740] flex justify-center items-center flex-col w-fit h-fit sm:p-10 p-5 sm:gap-8 gap-6 ">
                    <div class="flex justify-between items-center gap-2">
                        <img src="/images/images_appele/flag-bone.png" alt="flag-bone" class="w-[5rem]">
                        <h1 class="sm:text-3xl text-2xl text-[#EB0000] font-bold">Récompense spéciale !</h1>
                        <img src="/images/images_appele/flag-bone.png" alt="flag-bone" class="w-[5rem]">
                    </div>
                    <p class="text-[#FFFFFF] sm:text-2xl text-xl text-center ">
                        Les 9 chasseurs suivant le vainqueur
                        <br>gagneront une place
                        <br>gratuite pour une chasse au trésor de la
                        <br>même valeur !
                    </p>
                </div>
            </div>
            <LazyChasseTresor :hunting="hunting" />
        </section>

        <section class="py-16 relative bg-cover bg-center  font-merienda bg-[#000000]">
            <LazyChasseTresormaudit />
        </section>

        <!-- regle du jeu -->
        <section class="all-marcellus bg-[#1e130a] xs:block p-4 sm:p-10 sm:flex sm:items-center sm:gap-6">
            <img src="/images/images_appele/history-paper-rouller.png" alt="history-paper-rouller"
                class="w-[14rem] sm:w-[20rem] m-auto">
            <div class="sm:w-[30rem] w-fit flex flex-col justify-center items-center ">
                <h1 class="sm:text-3xl text-2xl text-[#FFFFFF] sm:p-6 p-2">Règle du jeu</h1>
                <div class="bg-[#EB0000] w-[4rem] h-[1px]  m-auto mt-1 mb-2"></div>
                <div class="flex flex-col justify-center items-center sm:gap-4 gap-2 ellipsis-multiline"
                    v-html="regle_jeu?.content">
                </div>
                <a href="/documents/regles-du-jeu">
                    <button type="button" class="inline-flex  items-center text-center bg-[#EB0000] text-white
                            px-[4em] py-2
                            rounded-lg sm:rounded-xl
                            hover:bg-[#CC1F1A] transition-all duration-300 mt-5">
                        <span class="text-base sm:text-lg font-merienda"> Voir plus de détails </span>
                    </button>
                </a>
            </div>
        </section>
        <!-- regle du jeu -->

        <LazyLayoutFooter />
    </div>
</template>
<script setup>
import { ref, onMounted } from "vue";

const route = useRoute();
const { id } = route.params;
const hunting = ref({});
const regle_jeu = ref({});


// const catOptionsMap = {
//     urban_physical_hunt: 'Chasse physique urbaine',
//     mystic_adventure: 'Aventure mystique',
//     underground_exploration: 'Exploration souterraine',
//     treasure_race: 'Course au trésor',
//     extreme_challenge: 'Défi extrême',
//     night_hunt: 'Chasse nocturne'
// }

const requestHunt = async () => {
    let request = await useApi().get("/hunting/retriew/" + id);
    hunting.value = request.data.hunting;
}

const requestLegal = async () => {
    let request = await useApi().post("/legal_document/find", {
        slug: 'regles-du-jeu'
    });
    regle_jeu.value = request.data.legal;
}

onMounted(() => {
    requestHunt();
    requestLegal();
});


</script>
<style scoped>
.ellipsis-multiline {
    display: -webkit-box;
    -webkit-line-clamp: 8;
    /* Nombre de lignes max */
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>