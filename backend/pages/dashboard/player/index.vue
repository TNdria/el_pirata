<template>
    <div class="mt-5">
        <div class="mb-4 flex justify-between gap-2">
            <h1 class="text-white font-bold text-2xl">Liste des joueurs</h1>
            <button @click="exportCSV" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow">
                <i class="fa fa-file-csv mr-2"></i> Exporter CSV
            </button>
        </div>

        <!-- Filtre par colonne -->
        <div class="mb-4 grid grid-cols-6 gap-2 text-white">
            <div>
                <label class="block mb-1 text-sm font-medium">ID</label>
                <input v-model="filters.id" type="text" placeholder="ID" class="p-2 rounded border w-full text-black" />
            </div>

            <!-- <div>
    <label class="block mb-1 text-sm font-medium">Pseudo</label>
    <input
      v-model="filters.name"
      type="text"
      placeholder="Pseudo"
      class="p-2 rounded border w-full text-black"
    />
  </div> -->

            <div>
                <label class="block mb-1 text-sm font-medium">Email</label>
                <input v-model="filters.email" type="text" placeholder="Email"
                    class="p-2 rounded border w-full text-black" />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Sexe</label>
                <select v-model="filters.sexe" class="p-2 rounded border w-full text-black">
                    <option value="">Tous</option>
                    <option value="F">Femme</option>
                    <option value="M">Homme</option>
                </select>
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Énigmes visitées ≥</label>
                <input v-model.number="filters.enigmas_viewed_count" type="number" placeholder="Visité >= "
                    class="p-2 rounded border w-full text-black" />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Énigmes résolues ≥</label>
                <input v-model.number="filters.enigmas_completed_count" type="number" placeholder="Résolu >= "
                    class="p-2 rounded border w-full text-black" />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Pays</label>
                <input v-model="filters.country" type="text" placeholder="Pays"
                    class="p-2 rounded border w-full text-black" />
            </div>

            <div>
                <label class="block mb-1 text-sm font-medium">Dernière connexion ≥</label>
                <input v-model="filters.last_activity_at" type="date" placeholder="Dernière connexion"
                    class="p-2 rounded border w-full text-black" />
            </div>
        </div>


        <div class="mb-4 text-right">
            <DataTable ref="dt" :value="filteredPlayers" class="w-full" scrollable scrollHeight="400px"
                style="width: 100%;">
                <Column field="id" header="ID"></Column>
                <Column field="name" header="Pseudo"></Column>
                <Column field="last_name" header="Nom"></Column>
                <Column field="first_name" header="Prénom"></Column>
                <Column field="email" header="Email"></Column>
                <Column field="country" header="Pays"></Column>
                <Column header="Sexe">
                    <template #body="slotProps">
                        {{ slotProps.data.sexe === 'F' ? 'Femme' : slotProps.data.sexe === 'M' ? 'Homme' : '-' }}
                    </template>
                </Column>
                <Column header="Dernière connexion">
                    <template #body="slotProps">
                        {{ slotProps.data.last_activity_at ? $moment(slotProps.data.last_activity_at).fromNow() : '-' }}
                    </template>
                </Column>
                <Column field="enigmas_viewed_count" header="Enigme visité"></Column>
                <Column field="enigmas_completed_count" header="Enigme résolu"></Column>
                <Column header="Archivé">
                    <template #body="slotProps">
                        {{ slotProps.data.is_archived ? "Oui" : "Non" }}
                    </template>
                </Column>
                <Column header="Actions">
                    <template #body="slotProps">
                        <button class="text-red-500 hover:text-red-700 p-2 rounded-full transition"
                            @click="archiveUser(slotProps.data)" v-if="!slotProps.data.is_archived">
                            <i class="fa fa-trash text-lg"></i>
                        </button>
                        <button class="text-blue-500 hover:text-blue-700 p-2 rounded-full transition"
                            @click="viewStats(slotProps.data)">
                            <i class="fa fa-chart-line text-lg"></i>
                        </button>
                    </template>
                </Column>
            </DataTable>

            <Toast />
            <Dialog v-model:visible="detailDialog" :modal="true" :closable="false" class="w-full min-h-screen">
                <PlayerStat :selectedPlayer="selectedPlayer" @close="selectedPlayer = null; detailDialog = false" />
            </Dialog>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, computed, reactive } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';

const { $moment } = useNuxtApp();
const confirm = useConfirm();
const toast = useToast();
const detailDialog = ref(false);
const playerList = ref([]);
const selectedPlayer = ref(null);
const dt = ref(null);

definePageMeta({ layout: 'admin' });

// Filtres
const filters = reactive({
    id: '',
    name: '',
    email: '',
    sexe: '',
    country: '',
    enigmas_viewed_count: null,
    enigmas_completed_count: null,
    last_activity_at: ''
});

// Filtrage réactif
const filteredPlayers = computed(() => {
    return playerList.value.filter(p => {
        return (!filters.id || p.id.toString().includes(filters.id)) &&
            (!filters.name || p.name.toLowerCase().includes(filters.name.toLowerCase())) &&
            (!filters.email || p.email.toLowerCase().includes(filters.email.toLowerCase())) &&
            (!filters.sexe || p.sexe === filters.sexe) &&
            (!filters.country || (p.country && p.country.toLowerCase().includes(filters.country.toLowerCase()))) &&
            (!filters.enigmas_viewed_count || p.enigmas_viewed_count >= filters.enigmas_viewed_count) &&
            (!filters.enigmas_completed_count || p.enigmas_completed_count >= filters.enigmas_completed_count) &&
            (!filters.last_activity_at || $moment(p.last_activity_at).isSameOrAfter(filters.last_activity_at, 'day'))

    });
});

const exportCSV = () => {
    if (dt.value) dt.value.exportCSV();
};

const fetchUsers = async () => {
    try {
        const response = await useApi().get('/players/all');
        playerList.value = response.data.list;
    } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs:", error);
    }
};

const viewStats = (player) => {
    selectedPlayer.value = player;
    detailDialog.value = true;
};

const archiveUser = async (row) => {
    confirm.require({
        message: `Voulez-vous vraiment supprimer "${row.id}" ?`,
        header: 'Confirmation',
        icon: 'fa fa-danger',
        acceptLabel: 'Oui',
        rejectLabel: 'Non',
        accept: async () => {
            let deleteRequest = await useApi().post(`/players/archive/`, { id: row.id });
            toast.add({
                severity: deleteRequest.data.state,
                summary: 'Succès',
                detail: deleteRequest.data.message,
                life: 3000
            });
            if (deleteRequest.data.state == "success") fetchUsers();
        }
    });
};

onMounted(fetchUsers);
</script>
