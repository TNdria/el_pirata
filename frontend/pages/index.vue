<template>

    <div class="bg-[#2b1b17] text-white">
        <LayoutMenu />
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

                <h1 class="text-4xl text-[#FFFFFF] mb-4">
                    Les <span class="text-[#EB0000]">chasses</span> au <span class="text-[#EB0000] ">trésor</span> du
                    capitaine et de son équipage !
                </h1>

                <p class="text-3xl mb-6 font-merienda">
                    Démarrez votre périple : des chasses au trésor à vivre entre le virtuel et le tangible
                </p>

                <div class="absolute bottom-16 left-1/2 transform -translate-x-1/2">
                    <a href="/enigme" class="inline-flex items-center bg-[#EB0000] text-white
                            px-2 py-2
                            rounded-lg sm:rounded-xl
                            hover:bg-[#CC1F1A] transition-all duration-300">
                        <span class="text-base sm:text-lg font-merienda">Essayez de résoudre les énigmes <i
                                class="fas fa-arrow-right" style="transform: rotate(-45deg);"></i></span>

                    </a>
                </div>
            </div>
        </section>
        <!-- END SECTION HERO -->

        <!-- SECTION VIDÉO & DESCRIPTION -->
        <section class="py-16 bg-[#FEA250]/12 font-merienda">
            <div class="container mx-auto px-4">
                <!-- Texte centré en haut -->
                <div class="text-center w-75 mx-auto mb-12 font-merienda font-[300]">
                    <h2 class="text-white text-4xl mb-4">
                        Hey ! les pirates ?
                    </h2>
                    <p class="text-white text-4xl mb-2">
                        Regarde la vidéo, elle va t'expliquer comment nos<br>
                        chasses au trésor se passent !
                    </p>
                    <p class="text-[#EB0000] text-4xl">
                        Bonne lecture, pirate!
                    </p>
                </div>

                <!-- Conteneur vidéo avec coins arrondis et bordure blanche -->
                <div class="max-w-4xl mx-auto">
                    <div class="relative rounded-[1.5rem] bg-white p-1">
                        <!-- Ratio 16:9 pour la vidéo -->
                        <div class="relative w-full rounded-[1.5rem] overflow-hidden" style="padding-top: 56.25%;">
                            <!-- Contrôles vidéo personnalisés -->
                            <div class="absolute inset-0 bg-black">
                                <VideoPlayer :urlVideo="'/video/video.mp4'" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </section>
        <!-- END SECTION VIDÉO & DESCRIPTION -->

        <!-- SECTION GALERIE / CAROUSEL -->
        <section class="py-8 md:py-16 bg-[#000000] font-merienda">
            <div class="container mx-auto px-4">
                <h2 class="text-white text-center text-4xl font-merienda mb-10">
                    Choisi une chasse au trésor numérique et <br> <span class="text-[#EB0000]">inscrit toi !</span>
                </h2>

                <V3Carousel :items-to-show="isSm ? 1 : 3" :wrapAround="true" :transition="2000" :autoplay="2000"
                    v-show="hunting_digital.length > 0">
                    <Slide v-for="(slide, index) in hunting_digital" :key="'slide-' + index">
                        <div class="flex-shrink-0 w-full p-4">
                            <div class="relative bg-cover bg-center rounded-2xl shadow-lg border border-gray-500 overflow-hidden h-96 flex flex-col items-center justify-center text-white"
                                :style="'background-image: url(' + baseUrl + slide.image + ')'">
                                <div class="absolute inset-0 bg-black opacity-75"></div>
                                <div class="relative text-center">
                                    <div class="text-2xl">
                                        <p>
                                            {{ slide.title }}
                                            <br>
                                            <span class="text-1xl">{{
                                                momentLib(slide.start_date).format('D MMMM YYYY') }}</span>
                                            <br>
                                            <span class="text-[#EB0000]"> {{ slide?.prize_win }}€ </span>
                                            <br>
                                            à gagner
                                        </p>
                                    </div>
                                    <a :href="'/chasse/' + slide.id">
                                        <button
                                            class="participer-button mt-4 bg-[#EB0000] text-white py-2 px-6 rounded-full text-lg  shadow-md hover:bg-red-700 transition font-rubik">Voirs
                                            les details</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </Slide>

                    <template #addons>
                        <CarouselNavigation />

                        <CarouselPagination class="relative mt-[35px]" />

                    </template>
                </V3Carousel>

                <div v-show="hunting_digital.length == 0">
                    <p class="text-center text-4xl leading-relaxed px-4 lg:px-0 mb-3">
                        Chasse au trésor bientôt disponible
                    </p>
                </div>

            </div>

        </section>
        <!-- END SECTION GALERIE / CAROUSEL -->

        <!-- SECTION CARTE BLANCHE (IMAGE & TEXTE) -->
        <section class="py-8 md:py-16 bg-[#FEA250]/12 font-merienda">
            <div class="container mx-auto px-4">
                <!-- Changement de flex-row à flex-col sur mobile -->
                <div>
                    <h2 class="text-white text-center text-4xl font-merienda mb-10">
                        Commencer la chasse au trésor, c’est partie pour <br> <span class="text-[#EB0000]">l’aventure
                            !</span>
                    </h2>
                </div>
                <div class="sm:block lg:flex items-center justify-center gap-8 lg:gap-20">
                    <!-- Cadre carré responsive -->
                    <div class="w-full h-[300px] sm:h-[400px] lg:h-[450px] bg-white rounded-3xl">
                        <img alt="Carte Blanche" class="w-full h-full object-cover rounded-3xl">
                    </div>

                    <!-- Bloc de texte responsive -->
                    <div class="w-full lg:mt-0 lg:text-left font-merienda">
                        <div class="mt-6 space-y-4 lg:space-y-6">
                            <h2 class="px-4 text-2xl sm:text-3xl lg:text-[30px] leading-tight font-semibold mb-3">
                                Lorem Ipsum is simply dummy<br class="hidden sm:block">
                                text dummy text
                            </h2>
                            <p class="text-base sm:text-lg leading-relaxed px-4 lg:px-0 mb-3">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum
                                has
                                been the industry's standard dummy text ever since the 1500s, when an unknown printer
                                took a
                                galley of type and scrambled it to make a type specimen book.
                            </p>

                            <p class="text-base sm:text-lg leading-relaxed px-4 lg:px-0 mb-3">
                                Lorem Ipsum is simply dummy text of the printing and typesetting industry.
                            </p>
                        </div>

                        <!-- Bouton Inscrivez-vous -->
                        <!-- Bouton Inscrivez-vous avec responsivité améliorée -->
                        <div class="px-4 flex justify-start mt-6 sm:mt-8 mx-0">
                            <a href="/login" class="inline-flex items-center bg-[#EB0000] text-white
                                            px-1 sm:px-6 lg:px-8
                                            py-1 sm:py-3 lg:py-4
                                            rounded-lg sm:rounded-xl
                                            hover:bg-[#CC1F1A] transition-all duration-300">
                                <span class="text-base sm:text-lg font-merienda">Commencer la chasse au trésor <i
                                        class="fas fa-arrow-right" style="transform: rotate(-45deg);"></i></span>

                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- END SECTION CARTE BLANCHE (IMAGE & TEXTE) -->

        <!-- SECTION FUTURES CHASSES -->
        <section class="py-8 md:py-16 bg-black font-merienda">
            <div class="lg:container mx-auto px-4">
                <!-- Titres -->
                <div class="text-center mb-8 md:mb-12">
                    <h2 class="text-4xl text-white mb-3 md:mb-4">
                        Choisi une chasse au trésor <span class="text-[#EB0000]">physique</span>, <span
                            class="text-[#EB0000]">en mode urbain</span>, et <span class="text-[#EB0000]">inscrit toi
                            !</span>
                    </h2>
                </div>
                <div v-show="hunting_physic.length == 0">
                    <p class="text-center text-4xl leading-relaxed px-4 lg:px-0 mb-3">
                        Chasse au trésor bientôt disponible
                    </p>
                </div>
                <div v-show="hunting_physic.length > 0">
                    <V3Carousel :items-to-show="isSm ? 1 : 3" :wrapAround="true" :transition="2000" :autoplay="2000">
                        <Slide v-for="(event, index) in hunting_physic" :key="'event-' + index">
                            <div class="flex rounded-2xl overflow-hidden w-[90%] h-90 xs:h-52 sm:h-56 md:h-90 object-cover hover:scale-105  bg-opacity-50 bg-no-repeat bg-cover bg-center"
                                :style="'background-image: url(' + baseUrl + event.image + ')'" :key="'event' + index">
                                <div class="absolute inset-0 bg-black opacity-75"></div>
                                <div class="relative rounded-2xl h-full w-full bg-[#00000082] text-center">
                                    <div
                                        class="m-auto text-center mt-[3%] pt-3 bg-[#BA7F3B1F] rounded-2xl h-[90%] w-[90%] ">
                                        <p class="text-white text-lg"> {{ catOptionsMap[event.category] }} <br> {{
                                            momentLib(event.start_date).format('D MMMM YYYY') }}
                                        </p>
                                        <p class="text-[#EB0000] text-lg"> {{ event.prize_win }} €</p>
                                        <a :href="'/chasse/' + event.id">
                                            <button
                                                class="participer-button mt-4 bg-[#EB0000] text-white py-2 px-6 rounded-full text-lg  shadow-md hover:bg-red-700 transition font-rubik z-100">Voirs
                                                les details</button>
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </Slide>

                        <template #addons>
                            <CarouselNavigation />

                            <CarouselPagination class="relative mt-[35px]" />

                        </template>
                    </V3Carousel>
                </div>

            </div>
        </section>
        <!-- END SECTION FUTURES CHASSES -->
        <LazyTemoinage />
        <LazyLayoutFooter />
    </div>
</template>

<script>

import { Carousel as V3Carousel, Slide, Pagination as CarouselPagination, Navigation as CarouselNavigation } from 'vue3-carousel'
import { useMediaQuery } from '@vueuse/core'

export default {
    setup() {
        const isSm = useMediaQuery('(max-width: 639px)');
        const config = useRuntimeConfig();
        const baseUrl = config.public.baseUrl;
        const { $momentLib } = useNuxtApp();
        const catOptionsMap = {
            urban_physical_hunt: 'Chasse physique urbaine',
            mystic_adventure: 'Aventure mystique',
            underground_exploration: 'Exploration souterraine',
            treasure_race: 'Course au trésor',
            extreme_challenge: 'Défi extrême',
            night_hunt: 'Chasse nocturne'
        }
        return {
            isSm,
            baseUrl,
            catOptionsMap,
            momentLib: $momentLib
        }
    },
    data() {
        return {
            hunting_digital: [],
            carouselConfig: {},
            temoins: [],
            hunting_physic: [],

        }
    },
    components: {
        V3Carousel,
        Slide,
        CarouselPagination,
        CarouselNavigation
    },
    methods: {
    },
    async mounted() {
        let hunterList = await useApi().get('/hunting/slide');
        let hunting = hunterList.data.hunting;
        this.hunting_physic = hunting.physic || [];
        this.hunting_digital = hunting.digital || [];
    }
}
</script>
<style>
body {
    font-family: 'Roboto', sans-serif;
}

/* Bouton principal personnalisé */
.btn-primary {
    background-color: #EB0000;
    color: #ffffff;
}

.btn-primary:hover {
    background-color: #CC1F1A;
}

header {
    transition: background-color 0.3s ease;
}

.fa-times {
    transform: rotate(180deg);
    transition: transform 0.3s ease;
}

.absolute {
    transition: opacity 0.3s ease, transform 0.3s ease;
}

@media (max-width: 1023px) {
    .absolute {
        transform-origin: top;
    }
}

.participer-button {
    font-family: 'Rubik', sans-serif;
    font-size: 15px;
}

#carousel {
    display: flex;
    transition: transform 0.5s ease-in-out;
}

.active-slide {
    transform: scale(1.1);
    z-index: 10;
    transition: transform 0.5s ease;
}

.inactive-slide {
    transform: scale(0.95);
    transition: transform 0.5s ease;
}

#prev i,
#next i {
    transition: color 0.3s ease;
}

#prev:hover i,
#next:hover i {
    color: white;
}

body {
    font-family: 'Roboto', sans-serif;
}

/* Police pour les titres */
.header-title {
    font-family: 'Pirata One', cursive;
}

/* Bouton principal personnalisé */
.btn-primary {
    background-color: #EB0000;
    color: #ffffff;
}

.btn-primary:hover {
    background-color: #CC1F1A;
}
</style>