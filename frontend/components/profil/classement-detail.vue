<template>
    <Dialog :modal="true" :closable="false" :dismissableMask="false" :style="{ width: '70%' }" :draggable="false"
        class="custom-modal font-merienda">
        <div
            class="bg-black text-white px-4 py-3 text-[20px] font-normal rounded-t-[10px] flex justify-between items-center w-full box-border">
            <h2>Classements de la chasse : <span class="text-[#EB0000] no-underline cursor-pointer -mt-10">Les
                    Arcanes de l’Alchimiste</span></h2>
            <span @click="emit('close')">
                <i class="fa fa-close"></i>
            </span>
        </div>

        <div class="rounded-lg border border-gray-500 m-4">
            <DataTable :value="participants" :paginator="true" :rows="5" :rowsPerPageOptions="[5, 10, 15]"
                class="w-full text-md">

                <!-- ✅ AVATAR -->
                <Column header="Avatar" :headerClass="'bg-[#575757] text-white'">
                    <template #body="{ data }">
                        <img :src="data.avatar" alt="Avatar" class="w-10 h-10 rounded-full object-cover" />
                    </template>
                </Column>

                <!-- ✅ PSEUDO -->
                <Column header="Pseudo" :headerClass="'bg-[#575757] text-white'">
                    <template #body="{ data }">
                        {{ data.pseudo }}
                    </template>
                </Column>

                <!-- ✅ TEMPS EFFECTUÉ -->
                <Column header="Temps effectué" :headerClass="'bg-[#575757] text-white'">
                    <template #body="{ data }">
                        {{ data.temps }}
                    </template>
                </Column>

                <!-- ✅ PLACE -->
                <Column header="Rang" :headerClass="'bg-[#575757] text-white'">
                    <template #body="{ data }">
                        <div
                            class="relative flex m-auto transform -translate-x-1/2 w-[40px] h-[40px] rounded-full text-black font-bold text-sm flex items-center justify-center">
                            <!-- <<svg-icon name="laurel" class="w-full h-full opacity-60 scale-[1.3] z-0" /> -->
                            <Laurel class="absolute w-full h-fullscale-[1.3] z-0" :style="getCircleStyle(data.place)" />
                            <span class="z-10 text-lg" :style="getCircleStyle(data.place)">{{ data.place + 1 }}ᵉ</span>
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

const participants = ref([
    { avatar: "https://i.pravatar.cc/40?img=1", pseudo: "Marie Jane", temps: "45min", place: 1 },
    { avatar: "https://i.pravatar.cc/40?img=2", pseudo: "JHON DOE", temps: "12 heures 10 minutes", place: 2 },
    { avatar: "https://i.pravatar.cc/40?img=3", pseudo: "JACK Emille", temps: "35min", place: 3 },
    { avatar: "https://i.pravatar.cc/40?img=4", pseudo: "Alice Doe", temps: "50min", place: 4 }
])

const getCircleStyle = (index) => {
  const colors = ['#C0C0C0', '#FFD700', '#CD7F32']; // Or, argent, bronze
  return {
    fill: colors[index] || '#FFFFFF',
    color: colors[index] || '#FFFFFF',
  };
}


</script>