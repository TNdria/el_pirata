<template>
  <div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-bold">Journal des actions utilisateurs</h2>
    <button @click="$emit('close')" class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600">
      <i class="fa fa-close text-2xl"></i>
    </button>
  </div>

  <DataTable :value="enigmas" responsiveLayout="scroll">
    <Column field="enigma.title" header="Enigme" />
    <Column field="viewed_at" header="Vue le" :body="formatDate" />
    <Column field="completed_at" header="Résolue le" :body="formatDate" />
    <Column field="attempts" header="Tentatives" />
    <Column header="Temps de résolution">
      <template #body="slotProps">
        {{ getResolutionTime(slotProps.data.viewed_at, slotProps.data.completed_at) }}
      </template>
    </Column>
    <Column field="is_used" header="Code utilisé">
      <template #body="slotProps">
        {{ slotProps.data.is_used ? 'Oui' : 'Non' }}
      </template>
    </Column>
  </DataTable>
</template>

<script setup>
import { ref, watch } from "vue";
// Exemple de données (normalement récupérées via API Laravel)
const { $moment } = useNuxtApp();
const props = defineProps({
  selectedPlayer: {
    type: Object,
    required: true
  }
});

// Déclare l'événement close
defineEmits(['close']);

const enigmas = ref([]);

const fetchEnigmas = async (user_id) => {
  const response = await useApi().post(`/admin/enigmas/user/stat`, {
    user_id: user_id
  });

  enigmas.value = response.data?.enigmas || [];
}



const getResolutionTime = (viewed, completed) => {
  if (!viewed || !completed) return '-';

  const start = $moment(viewed);
  const end = $moment(completed);
  const duration = $moment.duration(end.diff(start));

  const hours = Math.floor(duration.asHours());
  const minutes = duration.minutes();
  const seconds = duration.seconds();

  return `${hours}h ${minutes}m ${seconds}s`;
};


watch(
  () => props.selectedPlayer,
  (newPlayer) => {
    console.log(newPlayer);

    if (newPlayer) {
      fetchEnigmas(newPlayer.id);
    } else {
      enigmas.value = [];
    }
  },
  { immediate: true } // <-- exécute le watcher dès le début
);

const formatDate = (row) => row ? moment(row).format('DD/MM/YYYY HH:mm') : '-'


</script>


<style scoped>
h2 {
  color: #4ade80;
  /* vert clair pour le titre */
}
</style>
