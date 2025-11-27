<template>
  <div class="flex-grow bg-[#160f08] text-white p-8 font-[Merienda]">
    <h2 class="mb-4 text-lg mt-10">
      <span
        class="inline-flex items-center justify-center w-6 h-6 bg-[#53a95a] text-black rounded-full text-base mr-0.5 relative -translate-y-[1px]">✔</span>
      Résultats et récompenses
    </h2>

    <hr class="border-0 h-0.5 bg-[#26211b] my-4" />

    <p class="text-center w-full">Les classements des chasses au trésor</p>

    <div class="relative flex items-center justify-center mt-8 text-md  w-[100%] md:w-auto m-auto">
      <input type="text" placeholder="Taper pour rechercher..."
        class="w-full text-md border border-white rounded-lg outline-none  bg-transparent text-white px-4 py-2 w-[250px]" />
      <div
        class="top-0 bottom-0 w-[50px] h-[46px] -ml-[12px] bg-[#EB0000] rounded-r-lg flex items-center justify-center">
        <i class="fa-solid fa-search text-white text-md cursor-pointer"></i>
      </div>
    </div>

    <div class="relative  mb-20 w-[100%] md:w-[70%] m-auto">

      <V3Carousel :items-to-show="isSm ? 1 : 3" :transition="2000" :autoplay="2000" class="pt-16">
        <Slide v-for="(item, index) in carouselItems" :key="'reward' + index"
          class="gap-5 h-[380px] flex justify-center">
          <!-- Carte principale -->
          <div
            class="relative bg-gradient-to-br from-black to-gray-900 text-white w-[90%] h-[260px] rounded-2xl shadow-xl border border-red-600 pt-16 px-4">

            <!-- Avatar en surélévation -->
            <div
              class="absolute top-[-38px] left-1/2 transform -translate-x-1/2 w-[76px] h-[76px] rounded-full border-[4px] border-red-500 shadow-md bg-black flex items-center justify-center overflow-hidden">
              <img :src="`https://i.pravatar.cc/76?img=${index + 1}`" class="object-cover w-full h-full" />
            </div>

            <!-- Texte -->
            <p class="text-center text-[18px] font-semibold mb-3">{{ item.text }}</p>

            <!-- Récompense -->
            <div class="flex justify-center">
              <img :src="`/images/chasses/prix1.png`" class="w-[100px] h-[60px] object-contain mt-4"
                alt="Image récompense" />
            </div>

            <!-- Classement -->
            <div
              class="absolute bottom-4 left-1/2 transform -translate-x-1/2 w-[40px] h-[40px] rounded-full text-black font-bold text-sm flex items-center justify-center">
              <!-- <<svg-icon name="laurel" class="w-full h-full opacity-60 scale-[1.3] z-0" /> -->
              <Laurel class="absolute w-full h-fullscale-[1.3] z-0" :style="getCircleStyle(index)" />
              <span class="z-10 text-lg" :style="getCircleStyle(index)">{{ index + 1 }}ᵉ</span>
            </div>
          </div>
        </Slide>

        <template #addons>
          <CarouselNavigation />
          <CarouselPagination class="relative mt-10" />
        </template>
      </V3Carousel>


    </div>

    <p class="text-[#EB0000] no-underline cursor-pointer -mt-10 text-center" @click="showClassmentHunt = true">Consulter
      Voir les classements de la chasse
    </p>

    <p class="text-center mt-2">Mes chasses au trésor terminés</p>

    <div class="relative flex items-center justify-center mt-2 text-md  w-[100%] md:w-auto m-auto">
      <input type="text" placeholder="Taper pour rechercher..."
        class="w-full text-md border border-white rounded-lg outline-none  bg-transparent text-white px-4 py-2 w-[250px]" />
      <div
        class="top-0 bottom-0 w-[50px] h-[46px] -ml-[12px] bg-[#EB0000] rounded-r-lg flex items-center justify-center">
        <i class="fa-solid fa-search text-white text-md cursor-pointer"></i>
      </div>
    </div>

    <div class="max-w-[100%] overflow-x-auto rounded-lg border border-gray-500 text-md mt-4">
      <DataTable :value="recompense" :paginator="true" :rows="5" :rowsPerPageOptions="[5, 10, 15]"
        class="w-full text-md">

        <Column header="Nom de la chasse" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            {{ data.nom }}
          </template>
        </Column>

        <Column header="Date" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            {{ data.date }}
          </template>
        </Column>

        <Column header="Temps effectué" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            {{ data.temps }}
          </template>
        </Column>

        <Column header="Place" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            <div
              class="relative flex m-auto transform -translate-x-1/2 w-[40px] h-[40px] rounded-full text-black font-bold text-sm flex items-center justify-center">
              <!-- <<svg-icon name="laurel" class="w-full h-full opacity-60 scale-[1.3] z-0" /> -->
              <Laurel class="absolute w-full h-fullscale-[1.3] z-0" :style="getCircleStyle(data.index)" />
              <span class="z-10 text-lg" :style="getCircleStyle(data.index)">{{ data.index + 1 }}ᵉ</span>
            </div>
          </template>
        </Column>

        <Column header="Récompense" :headerClass="'bg-[#575757] text-white'" class="text-center">
          <template #body="{ data }">
            <div class="text-center">
              <span v-if="Number(data.index + 1) === 1" class="icon success">✔</span>
              <span v-else-if="Number(data.index + 1) === 2 || Number(data.index + 1) === 3"
                class="icon warning">!</span>
              <span v-else class="icon error">✖</span>
            </div>

          </template>
        </Column>

      </DataTable>
    </div>

    <LazyProfilClassementDetail v-model:visible="showClassmentHunt" @close="showClassmentHunt = false" />

    <div class="flex items-center gap-2 mt-5 text-md">
      <button @click="showRewardHunt = true"
        class="bg-[#EB0000] text-white border-none py-2.5 px-5 text-md cursor-pointer rounded mt-6 block w-fit relative ml-auto font-[Merienda]">
        Réclamer mes récompenses
      </button>
    </div>

    <LazyProfilRecompense v-model:visible="showRewardHunt" @close="showRewardHunt = false" />

    <br>

  </div>

</template>

<script setup>
import { ref } from 'vue';
import Laurel from "../icon/laurel.svg";
import {
  Carousel as V3Carousel,
  Slide,
  Pagination as CarouselPagination,
  Navigation as CarouselNavigation
} from 'vue3-carousel';
import { useMediaQuery } from '@vueuse/core';

const isSm = useMediaQuery('(max-width: 639px)');
const showClassmentHunt = ref(false);
const showRewardHunt = ref(false);

// === DONNÉES ===

const recompense = ref([
  { nom: "1Le Trésor de l'Île Maudite", date: "15/01/2025", temps: "12 heures", recompens: "50 €", index: 10 },
  { nom: "Les Secrets du Pharaon", date: "10/01/2025", temps: "4 heures", recompens: "30 €", index: 4 },
  { nom: "Le Secret des Chevaliers", date: "05/02/2025", temps: "2 jours", recompens: "20 €", index: 2 },
  { nom: "Les Arcanes de l'Alchimiste", date: "01/01/2025", temps: "12 heures", recompens: "10 €", index: 1 },
  { nom: "1Le Trésor de l'Île Maudite", date: "14/12/2024", temps: "12 heures", recompens: "10 €", index: 0 }
]);

const carouselItems = ref([
  { text: "Marie Jane" },
  { text: "JHON DOE" },
  { text: "JACK Emille" }
])

const activeIndex = ref(1)

const getCircleStyle = (index) => {
  const colors = ['#C0C0C0', '#FFD700', '#CD7F32']; // Or, argent, bronze
  return {
    fill: colors[index] || '#FFFFFF',
    color: colors[index] || '#FFFFFF',
  };
}


</script>


<style scoped>
.checkbox-container {
  display: flex;
  align-items: center;
  /* Alignement vertical centré */
  gap: 8px;
  margin-top: 20px;
}

/* Statut : Payé */
.statut-paye {
  color: green;
  font-weight: bold;
  border: 1px solid green;
  border-radius: 12px;
  /* Légèrement réduit */
  padding: 2px 8px;
  /* Moins de hauteur */
  background: transparent;
  display: inline-block;
  font-size: 14px;
  /* Ajustement de la taille du texte */
  line-height: 1.2;
  margin-top: 12px;
}

/* Statut : En attente */
.statut-attente {
  color: red;
  font-weight: bold;
  border: 1px solid red;
  border-radius: 12px;
  /* Légèrement réduit */
  padding: 2px 8px;
  /* Moins de hauteur */
  background: transparent;
  display: inline-block;
  font-size: 14px;
  /* Ajustement de la taille du texte */
  line-height: 1.2;
  margin-top: 12px;
}




.icon {
  font-weight: bold;
  font-size: 18px;
  display: inline-block;
  width: 20px;
  text-align: center;
}


.warning {
  color: orange;
  border: 2px solid orange;
  border-radius: 50%;
  width: 24px;
  height: 24px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 16px;
}
</style>