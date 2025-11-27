<template>
    <Dialog :modal="true" :closable="false" :dismissableMask="false" :style="{ width: '70%' }" :draggable="false"
        class="custom-modal font-merienda">
        <div
            class="bg-black text-white px-4 py-3 text-[20px] font-normal rounded-t-[10px] flex justify-between items-center w-full box-border">
            <h2>Reclamer mes récompenses</h2>
            <span @click="emit('close')">
                <i class="fa fa-close"></i>
            </span>
        </div>

        <div class="rounded-lg border border-gray-500 m-4">
            <DataTable :value="recompense" :paginator="true" :rows="5" :rowsPerPageOptions="[5, 10, 15]"
                class="w-full text-md rounded-[10px] overflow-hidden">
                <!-- ✅ Nom de la chasse -->
                <Column header="Nom de la chasse" :headerClass="'bg-[#575757] text-white'" field="nom" />

                <!-- ✅ Date -->
                <Column header="Date" :headerClass="'bg-[#575757] text-white'" field="date" />

                <!-- ✅ Place gagnée -->
                <Column header="Place gagnée" :headerClass="'bg-[#575757] text-white'">
                    <template #body="{ data }">
                        <div
                            class="relative flex m-auto transform -translate-x-1/2 w-[40px] h-[40px] rounded-full text-black font-bold text-sm flex items-center justify-center">
                            <!-- <<svg-icon name="laurel" class="w-full h-full opacity-60 scale-[1.3] z-0" /> -->
                            <Laurel class="absolute w-full h-fullscale-[1.3] z-0" :style="getCircleStyle(data.place)" />
                            <span class="z-10 text-lg" :style="getCircleStyle(data.place)">{{ data.place + 1 }}ᵉ</span>
                        </div>
                    </template>
                </Column>

                <!-- ✅ Récompense -->
                <Column header="Récompense" :headerClass="'bg-[#575757] text-white'">
                    <template #body="{ data }">
                        <div class="recompense-bloc flex items-center justify-between">
                            <span class="recompense-valeur mr-2">{{ data.recompens }}</span>
                            <Button label="Réclamer"
                                class="btn-reclamer p-button-sm p-button-rounded p-button-success" />
                        </div>
                    </template>
                </Column>
            </DataTable>
        </div>
    </Dialog>
</template>

<script setup>
import Laurel from "../icon/laurel.svg";

const emit = defineEmits(['close']);

const recompense = ref([
    { nom: "1Le Trésor de l'Île Maudite", date: "15/01/2025", temps: "12 heures", place: 2, recompens: "50 €" },
    { nom: "Les Secrets du Pharaon", date: "10/01/2025", temps: "4 heures", place: 15, recompens: "30 €" },
    { nom: "Le Secret des Chevaliers", date: "05/02/2025", temps: "2 jours", place: 20, recompens: "20 €" },
    { nom: "Les Arcanes de l'Alchimiste", date: "01/01/2025", temps: "12 heures", place: 1, recompens: "10 €" },
    { nom: "1Le Trésor de l'Île Maudite", date: "14/12/2024", temps: "12 heures", place: 0, recompens: "10 €" }
])

const getCircleStyle = (index) => {
  const colors = ['#C0C0C0', '#FFD700', '#CD7F32']; // Or, argent, bronze
  return {
    fill: colors[index] || '#FFFFFF',
    color: colors[index] || '#FFFFFF',
  };
}

</script>