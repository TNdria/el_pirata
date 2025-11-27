<template>
    <div class="mt-5">
        <h1 class="text-white text-bold text-2xl"> Liste des chasses </h1>
        <div class="mb-4 text-right">
            <button class="bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 transition"
                @click="showModal = true">
                <i class="fa fa-plus mr-2"></i> Créer un chasse
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <div v-for="hunt in HuntingList" :key="hunt.id"
                class="bg-gray-800 text-white p-4 rounded-2xl shadow-lg relative">
                <img :src="baseUrl + hunt.image" alt="Hunt Image" class="w-full h-40 object-cover rounded-xl mb-4" />

                <h2 class="text-xl font-semibold mb-2">{{ hunt.title }}</h2>

                <div class="text-sm text-gray-300 mb-2">
                    <p><i class="fa fa-map-marker-alt text-yellow-400 mr-1"></i> Limite de place : {{ hunt.limit_place
                        }}</p>
                    <p><i class="fa fa-calendar-alt text-green-400 mr-1"></i> Départ : {{
                        $moment(hunt.start_date).format('DD/MM/YYYY HH:mm') }}</p>
                    <p><i class="fa fa-layer-group text-blue-400 mr-1"></i> Type : {{ typeOptionsMap[hunt.type] }}</p>
                    <p><i class="fa fa-signal text-purple-400 mr-1"></i> Niveau : {{ levelOptionsMap[hunt.level] ||
                        'N/A' }}</p>
                    <p><i class="fa fa-tags text-pink-400 mr-1"></i> Catégorie : {{ catOptionsMap[hunt.category] ||
                        'N/A' }}</p>
                    <p><i class="fa fa-trophy text-yellow-300 mr-1"></i> Prix à gagner : {{ hunt.prize_win }}</p>
                    <p><i class="fa fa-coins text-orange-400 mr-1"></i> Tarif : {{ hunt.registration_fee }}</p>
                    <p><i class="fa fa-eye mr-1" :class="hunt.is_published ? 'text-green-500' : 'text-red-500'"></i>
                        Publié : {{ hunt.is_published ? 'Oui' : 'Non' }}
                    </p>
                </div>

                <div class="absolute top-3 right-3 flex gap-2">
                    <button @click="editHunting(hunt.id)" class="text-blue-400 hover:text-blue-600">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button @click="archiveHunt(hunt)" class="text-red-400 hover:text-red-600">
                        <i class="fa fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <HuntingModal v-model:visible="showModal" @fetchHunting="fetchHunting"
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
const HuntingList = ref([]);
const showModal = ref(false);
const updateId = ref(null);
const toast = useToast()
const config = useRuntimeConfig();
const baseUrl = config.public.baseUrl
const typeOptionsMap = {
    physic: 'Physique',
    digital: 'Numérique'
};

const levelOptionsMap = {
    easy: 'Facile',
    normal: 'Normal',
    hard: 'Difficile'
}

const catOptionsMap = {
    urban_physical_hunt: 'Chasse physique urbaine',
    mystic_adventure: 'Aventure mystique',
    underground_exploration: 'Exploration souterraine',
    treasure_race: 'Course au trésor',
    extreme_challenge: 'Défi extrême',
    night_hunt: 'Chasse nocturne'
}

definePageMeta({
    layout: 'admin'
});


const fetchHunting = async () => {
    try {
        const response = await useApi().get('/admin/hunting/all');
        HuntingList.value = response.data.list;
    } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs:", error);
    }
};

const editHunting = (id) => {
    updateId.value = id;
    showModal.value = true;
};

const archiveHunt = async (row) => {
    confirm.require({
        message: `Voulez-vous vraiment supprimer "${row.id}" ?`,
        header: 'Confirmation',
        icon: 'fa fa-danger',
        acceptLabel: 'Oui',
        rejectLabel: 'Non',
        accept: async () => {
            let deleteRequest = await useApi().post(`/admin/hunting/archive/`, {
                id: row.id
            });

            toast.add({
                severity: deleteRequest.data.state,
                summary: 'Succès',
                detail: deleteRequest.data.message,
                life: 3000
            });

            if (deleteRequest.data.state == "success") {
                fetchHunting();
            }

        },
        reject: () => {
            toast.add({ severity: 'info', summary: 'Annulé', detail: 'Suppression annulée', life: 2000 });
        }
    });
}

onMounted(() => {
    fetchHunting();
});

</script>