<template>
    <div class="mt-5">
        <h1 class="text-white text-bold text-2xl"> Liste des actions admin </h1>
        <div class="mb-4 text-right">

            <DataTable :value="logsList" class="min-w-full">
                <Column field="admin.name" header="Nom"></Column>
                <Column field="admin.email" header="Email"></Column>
                <Column field="ip_address" header="IP"></Column>
                <Column field="user_agent" header="User agent"></Column>
                <Column field="table_id" header="ID du table"></Column>
                <Column header="Type du table">
                    <template #body="slotProps">
                        {{ findTypeTable(slotProps.data.table_type) }}
                    </template>
                </Column>
                <Column header="Action">
                    <template #body="slotProps">
                        {{ findAction(slotProps.data.action) }}
                    </template>
                </Column>

                <Column field="date" header="date">
                    <template #body="slotProps">
                        {{ $moment(slotProps.data.created_at).format('DD/MM/YYYY') }}
                    </template>
                </Column>

            </DataTable>

            <Toast />
        </div>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue'

const logsList = ref([]);

definePageMeta({
    layout: 'admin'
});

const fetchLogs = async () => {
    try {
        const response = await useApi().get('/logs/all');
        logsList.value = response.data.list;
    } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs:", error);
    }
};

const findAction = ($action) => {
    switch ($action) {
        case 'create':
            return 'création';
        case 'update':
            return 'modification';
        case 'archive':
            return 'archive';
        default:
            return 'action inconnue';
    }
}

const findTypeTable = ($table) => {
    switch ($table) {
        case 'App\\Models\\Admin':
            return 'table admin';
        case 'App\\Models\\hunting':
            return 'table chasse';
        case 'App\\Models\\User':
            return 'table joueur';
        case 'App\\Models\\enigmas':
            return 'table Enigme';
        default:
            return 'table inconnue';
    }
}

onMounted(() => {
    fetchLogs();
})

</script>