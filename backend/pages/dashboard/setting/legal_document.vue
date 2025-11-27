<template>
    <div class="mt-5">
        <h1 class="text-white text-bold text-2xl"> Liste des documents légaux </h1>
        <div class="mb-4 text-right">

            <DataTable :value="legalList" class="min-w-full">
                <Column field="title" header="Titre"></Column>
                <Column field="type" header="Type"></Column>
                <Column field="slug" header="Slug"></Column>
                <Column field="date" header="date de modification">
                    <template #body="slotProps">
                        {{ $moment(slotProps.data.update_at).format('DD/MM/YYYY') }}
                    </template>
                </Column>
                <Column header="Actions">
                    <template #body="slotProps">
                        <button class="text-blue-500 hover:text-blue-700 p-2 rounded-full transition"
                            @click="edit(slotProps.data.id)">
                            <i class="fa fa-edit text-lg"></i>
                        </button>
                    </template>
                </Column>

            </DataTable>


            <LegalDocumentModal v-model:visible="showModal" @fetchLegal="fetchLegal()" @close="showModal = false; updateId = null"
                :updateId="updateId" />

            <Toast />
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const { $moment } = useNuxtApp();

definePageMeta({
    layout: 'admin'
});

const legalList = ref([]);
const showModal = ref(false);
const updateId = ref(null);

const fetchLegal = async () => {
    try {
        const response = await useApi().get('/legal_document/all');
        legalList.value = response.data.list;
    } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs:", error);
    }
};

const edit = (id) => {
    updateId.value = id;
    showModal.value = true;
};

onMounted(() => {
    fetchLegal();
})
</script>