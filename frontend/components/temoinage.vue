<template>

    <!-- SECTION TÉMOIGNAGES -->
    <section class="py-8 md:py-16 bg-[#FEA2501F] font-merienda">
        <div class="container mx-auto px-4">
            <!-- Titre principal avec animations -->
            <div class="text-center mb-8 md:mb-16 overflow-hidden">
                <h2
                    class="text-4xl text-[#FFFFFF] mb-3 md:mb-4 transform transition-all duration-1000 animate-slide-down">
                    Partage ton aventure et remporte une réduction de
                </h2>
                <div class="text-[#EB0000] text-center">
                    <span
                        class="text-4xl inline-block transform transition-all duration-1000 delay-300 animate-scale-in">
                        10%
                    </span>
                    <span
                        class="text-4xl text-[#FFFFFF] block sm:inline transform transition-all duration-1000 delay-500 animate-slide-right">
                        sur tes prochaines participations à nos
                    </span>
                </div>
                <h3
                    class="text-4xl text-[#EB0000] mt-2 transform transition-all duration-1000 delay-700 animate-slide-up">
                    chasses au trésor !
                </h3>
            </div>
        </div>
        <div class="container mx-auto px-4">
            <V3Carousel v-if="isClient" :items-to-show="isSm ? 1 : 3" :transition="2000" :autoplay="2000" :wrap-around="true">
                <Slide v-for="(temoin, index) in temoins" :key="'temoin' + index">
                    <div class="carte flex-shrink-0 px-2 sm:px-4 w-full">
                        <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-200 transform transition-all duration-300 hover:scale-105 hover:shadow-xl"
                            :style="{ animationDelay: `${index * 100}ms` }">
                            <div class="bg-white rounded-3xl p-6 shadow-lg border border-gray-200 transform transition-all duration-300 hover:scale-105 hover:shadow-xl"
                                :style="{ animationDelay: `${index * 100}ms` }">
                                <!-- En-tête -->
                                <div class="flex items-center mb-4">
                                    <!-- Avatar -->
                                    <img :src="`/images/p${(index % 3) + 1}.png`"
                                        class="w-16 h-16 rounded-full object-cover transform transition-all duration-300 hover:scale-110"
                                        :alt="temoin.name" />
                                    <div class="ml-4">
                                        <!-- Étoiles -->
                                        <div class="flex absolute right-0">
                                            <span v-for="star in 5" :key="star" class="text-yellow-400 text-lg">
                                                ★
                                            </span>
                                        </div>
                                        <h4 class="text-lg">{{ temoin.name }}</h4>
                                        <p class="text-gray-600 text-lg">{{ temoin.location }}</p>
                                    </div>
                                </div>

                                <!-- Témoignage -->
                                <p class="text-gray-700 text-lg leading-relaxed mt-2">
                                    "Lorem Ipsum is simply dummy text of the printing and typesetting industry."
                                </p>
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

    </section>
    <!-- SECTION TÉMOIGNAGES -->
</template>
<script setup>
import { ref, onMounted, watch, reactive } from 'vue';
import { Carousel as V3Carousel, Slide, Pagination as CarouselPagination, Navigation as CarouselNavigation } from 'vue3-carousel'
import { useMediaQuery } from '@vueuse/core';
import { useCarousel } from '~/composables/useCarousel';

const isSm = useMediaQuery('(max-width: 639px)');
const { isClient } = useCarousel();
const temoins = ref([
    { id: 1, name: 'Viezh Robert', location: 'Warsaw, Poland' },
    { id: 2, name: 'Yessica Christy', location: 'Shanxi, China' },
    { id: 3, name: 'Kim Young Jou', location: 'Seoul, Korea' },
    { id: 4, name: 'Jean Dupont', location: 'Paris, France' },
    { id: 5, name: 'Marie Martin', location: 'Lyon, France' },
    { id: 6, name: 'Luc Bernard', location: 'Marseille, France' },
    { id: 7, name: 'Sophie Chen', location: 'Beijing, China' },
    { id: 8, name: 'Marco Silva', location: 'Lisbon, Portugal' },
    { id: 9, name: 'Anna Kowalski', location: 'Krakow, Poland' },
    { id: 10, name: 'Thomas Mueller', location: 'Berlin, Germany' },
    { id: 11, name: 'Linda Johnson', location: 'London, UK' },
    { id: 12, name: 'Carlos Garcia', location: 'Madrid, Spain' }
]);

</script>