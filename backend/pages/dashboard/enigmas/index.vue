<template>
    <div class="mt-5">
        <div class="flex flex-row justify-between">
 <h1 class="text-white text-bold text-2xl"> Liste des égnimes </h1>
        <div class="mb-4 text-right">
            <button class="bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 transition"
                @click="showModal = true">
                <i class="fa fa-plus mr-2"></i> Créer un égnime
            </button>
        </div>
        </div>
       

        <div class="grid grid-cols-2 p-4 gap-4">
            <div>
                <label for="searchTitre" class="block text-lg text-white font-medium mb-1">Titre de enigme</label>
                <input id="searchTitre" v-model="searchTitre" type="text" placeholder="Rechercher par titre"
                    class="border p-2 rounded w-full" />
            </div>

            <div>
                <label for="searchType" class="block text-lg text-white font-medium mb-1">Type de chasse</label>
                <select id="searchType" v-model="searchType" class="border p-2 rounded w-full">
                    <option value="">Tous types</option>
                    <option v-for="hunting in huntingList" :key="hunting.id" :value="hunting.id">
                        {{ hunting?.title }}
                    </option>
                </select>
            </div>
        </div>

        <DataTable :value="filteredChasses" class="min-w-full">
            <Column field="id" header="ID"></Column>
            <Column header="Illustration" class="w-[100px]">
                <template #body="slotProps">
                    <img :src="baseUrl + slotProps.data.media" class="h-50 w-full rounded" alt="Preview"
                        v-if="slotProps.data.media_type == 'image'" />
                </template>
            </Column>
            <Column field="title" header="Titre"></Column>
            <Column header="Chasse">
                <template #body="slotProps">
                    {{ slotProps.data.hunting_id ? slotProps.data?.hunting?.title : 'Gratuit' }}
                </template>
            </Column>
            <Column header="Niveau">
                <template #body="slotProps">
                    {{ levelOptionsMap[slotProps.data.level] }}
                </template>
            </Column>

            <!-- <Column field="description" header="Description"> </Column> -->

            <Column field="response" header="Réponse"> </Column>

            <Column field="is_archive" header="Activé">
                <template #body="slotProps">
                    {{ slotProps.data.is_published ? 'oui' : 'non' }}
                </template>
            </Column>

            <Column header="Actions">
                <template #body="slotProps">
                    <button class="text-green-500 hover:text-green-700 p-2 rounded-full transition"
                        @click="viewEnigma(slotProps.data)">
                        <i class="fa fa-eye text-lg"></i>
                    </button>
                    <button class="text-blue-500 hover:text-blue-700 p-2 rounded-full transition"
                        @click="editEnigmas(slotProps.data.id)">
                        <i class="fa fa-edit text-lg"></i>
                    </button>
                    <button class="text-red-500 hover:text-red-700 p-2 rounded-full transition"
                        @click="archiveEnigma(slotProps.data)">
                        <i class="fa fa-trash text-lg"></i>
                    </button>
                </template>
            </Column>

        </DataTable>

        <!-- Dialog PrimeVue -->
        <Dialog v-model:visible="showDialog" :modal="true" :style="{ width: '100%' }" class="h-full" :closable="true"
            :draggable="false" @hide="() => {
                PreviewEnigma.value = {};
            }">
            <template #header>
                <div class="flex gap-2">
                    <!-- Exemple bouton action -->
                    <!-- <button class="border border-gray text-white px-3 py-1 rounded text-sm md:text-base"
                        @click="iframeMode = 'mobile'">
                        <span class="fa fa-mobile"></span>
                    </button> -->

                    <!-- Bouton fermeture (visible partout) -->
                    <button class="border border-gray text-white px-3 py-1 rounded text-sm md:text-base"
                        @click="iframeMode = 'desktop'">
                        <span class="fa fa-desktop"></span>
                    </button>
                </div>
            </template>

            <iframe v-if="PreviewEnigma?.id"
                :src="'http://localhost:3000/enigme?preview=true&enigma_id=' + PreviewEnigma.id" class="w-full"
                :class="iframeHeightClass" frameborder="0">

            </iframe>
        </Dialog>

        <EngimasModal v-model:visible="showModal" :huntingList="huntingList" @fetchEnigmas="fetchEnigmas"
            @close="showModal = false; updateId = null" :updateId="updateId" />

        <Toast />

        <ConfirmDialog />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
const { $moment } = useNuxtApp();

const confirm = useConfirm();
const EnigmasList = ref([]);
const showModal = ref(false);
const updateId = ref(null);
const toast = useToast()
const config = useRuntimeConfig();
const baseUrl = config.public.baseUrl
const huntingList = ref([]);
const iframeMode = ref('desktop');

const iframeHeightClass = computed(() => {
    if (iframeMode.value === 'mobile') return 'h-[600px] w-[306px] m-auto' // iPhone 7 Plus friendly
    if (iframeMode.value === 'desktop') return 'h-[750px] w-full'
    return 'h-[500px] sm:h-[600px] md:h-[750px] lg:h-[900px]' // auto
})

const levelOptionsMap = {
    easy: 'Facile',
    normal: 'Normal',
    hard: 'Difficile'
}

const searchTitre = ref("");
const searchType = ref("");


const filteredChasses = computed(() => {
    return EnigmasList.value.filter(chasse => {
        const matchTitre = chasse.title
            .toLowerCase()
            .includes(searchTitre.value.toLowerCase());
        const matchType = searchType.value
            ? chasse.hunting_id === searchType.value
            : true;

        return matchTitre && matchType;
    });
});

definePageMeta({
    layout: 'admin'
});

const fetchEnigmas = async () => {
    try {
        const response = await useApi().get('/admin/enigmas/all');
        EnigmasList.value = response.data.list;
        huntingList.value = response.data.huntingList;
    } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs:", error);
    }
};

const showDialog = ref(false);
const PreviewEnigma = ref({});

const viewEnigma = (enigme) => {
    PreviewEnigma.value = enigme;
    showDialog.value = true;
}

const editEnigmas = (id) => {
    updateId.value = id;
    showModal.value = true;
};

const archiveEnigma = async (row) => {
    confirm.require({
        message: `Voulez-vous vraiment supprimer "${row.id}" ?`,
        header: 'Confirmation',
        icon: 'fa fa-danger',
        acceptLabel: 'Oui',
        rejectLabel: 'Non',
        accept: async () => {
            let deleteRequest = await useApi().post(`/admin/enigmas/archive/`, {
                id: row.id
            });

            toast.add({
                severity: deleteRequest.data.state,
                summary: 'Succès',
                detail: deleteRequest.data.message,
                life: 3000
            });

            if (deleteRequest.data.state == "success") {
                fetchEnigmas();
            }

        },
        reject: () => {
            toast.add({ severity: 'info', summary: 'Annulé', detail: 'Suppression annulée', life: 2000 });
        }
    });
}

onMounted(() => {
    fetchEnigmas();
});

</script>