<template>

  <div class="bg-[#2b1b17] text-white">
    <LazyLayoutMenu />

    <!-- SECTION HERO -->
    <section class="relative bg-cover bg-center h-[90vh] font-merienda bg-[#EB0000]"
      style="background-image: url('/images/banner.jpg'); background-size: cover; background-position: center;">
      <!-- Overlay noir semi-transparent -->
      <div class="absolute inset-0 bg-black opacity-50"></div>
      <!-- Contenu centré (titre, sous-titre, CTA) -->
      <div class="container mx-auto relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
        <img src="/images/pirate.png" alt="pirate" class="w-32 h-32 mb-6 object-contain mx-auto">
        <!-- Ajuste la taille avec w-32 h-32 -->

        <h1 class="text-4xl text-[#FFFFFF] mb-4">
          Votre <span class="text-[#EB0000]">Aventure</span> Vous <span class="text-[#EB0000] ">Attend</span>
          capitaine et de son équipage !
        </h1>

        <p class="text-3xl mb-6 font-merienda">
          Explorez toutes nos chasses au trésor numériques,
          pour une aventure unique !
        </p>

      </div>
    </section>
    <!-- END SECTION HERO -->

    <div class="space-y-6 font-merienda">
      <div class="text-center mb-4">
        <h2 class="text-2xl  text-white ">Explorez nos chasses et trouvez celle faite pour vous</h2>
      </div>

      <!-- BOUTONS DE FILTRE -->
      <div class="flex flex-wrap gap-6 mb-6 justify-center">
        <button v-for="type in typesHuntinf" :key="type" :class="[
          'px-4 py-2 border border-white rounded text-white transition-all duration-300',
          selectedType === type.value
            ? 'bg-cover bg-center bg-[url(/images/fond_btn.jpg)]'
            : 'bg-transparent hover:bg-white hover:text-black'
        ]" @click="selectedType = type.value">
          {{ type.label }}
        </button>
      </div>

      <!-- LISTE FILTRÉE DES CHASSES -->
      <div class="flex flex-wrap justify-center gap-y-6 gap-x-3 mt-4 mb-12 py-8 w-[96%] m-auto">
        <div v-for="hunt in filteredHunt" :key="hunt.id"
          class="transition-all duration-500 ease-in-out relative bg-cover bg-center rounded-2xl shadow-lg border-gray-500 overflow-hidden flex flex-col items-center justify-center text-white"
          :style="`background-image: url(${baseUrl + hunt.image}); width: 18.75rem; height: 17.25rem;`">
          <div class="absolute inset-0 bg-black opacity-75"></div>

          <div class="relative text-center p-4 rounded-lg" style="background: #BA7F3B1F;">
            <div class="text-xl">
              <p>
                {{ hunt.title }}
                <br>
                <span class="text-base">{{ $moment(hunt.start_date).format('D MMMM YYYY') }}</span>
                <br>
                <img src="/images/chasses/lingo.png" class="inline-block my-2" style="width: 60px; height: 40px;">
                <br>
                <span>{{ hunt.prize_win }}</span>
                <br>
              </p>
            </div>

            <a :href="'/chasse/' + hunt.id">
              <button
                class="participer-button mt-4 bg-[#EB0000] text-white py-1 px-6 rounded-full text-[18px] shadow-md hover:bg-red-700 transition"
                style="font-family: 'Merienda', cursive;">
                Voir les détails
              </button>
            </a>

          </div>
        </div>
      </div>
    </div>
    <LazyTemoinage />
    <LazyLayoutFooter />
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const config = useRuntimeConfig();
const baseUrl = config.public.baseUrl;
const { $moment } = useNuxtApp();

const typesHuntinf = [
  { label: 'Toutes les chasses', value: 'all' },
  { label: 'Chasses numériques', value: 'digital' },
  { label: 'Chasses urbaines', value: 'physic' }
]

const catOptionsMap = {
  urban_physical_hunt: 'Chasse physique urbaine',
  mystic_adventure: 'Aventure mystique',
  underground_exploration: 'Exploration souterraine',
  treasure_race: 'Course au trésor',
  extreme_challenge: 'Défi extrême',
  night_hunt: 'Chasse nocturne'
}

// 🔹 Type sélectionné
const selectedType = ref('all')

// 🔸 Liste des chasses
const hunting = ref([])

// ✅ Computed filtré selon le type sélectionné
const filteredHunt = computed(() => {
  if (selectedType.value === 'all') return hunting.value
  return hunting.value.filter(c => c.type === selectedType.value)
});

onMounted(async () => {
  let hunterList = await useApi().get('/hunting/all');

  if (hunting.value = hunterList.data.hunting) {
    hunting.value = hunterList.data.hunting;
  }

})

</script>
