<template>
    <div class="mt-5">
        <h1 class="text-white text-bold text-2xl"> Liste des administateurs </h1>
        <div class="mb-4 text-right" v-if="userInfo.role.name === 'super_admin'">
            <button class="bg-blue-500 text-white p-2 rounded-md hover:bg-blue-600 transition"
                @click="showModal = true">
                <i class="fa fa-user-plus mr-2"></i> Créer un utilisateur
            </button>
        </div>

        <DataTable :value="userList" class="min-w-full">
            <Column field="id" header="ID"></Column>
            <Column field="name" header="Nom"></Column>
            <Column field="email" header="Email"></Column>
            <Column field="role.label_fr" header="Rôle"></Column>
            <Column header="Actions" v-if="userInfo.role.name === 'super_admin'">
                <template #body="slotProps">
                    <button class="text-blue-500 hover:text-blue-700 p-2 rounded-full transition"
                        @click="editUser(slotProps.data.id)" v-if="userInfo.id != slotProps.data.id">
                        <i class="fa fa-edit text-lg"></i>
                    </button>
                    <button class="text-red-500 hover:text-red-700 p-2 rounded-full transition"
                        @click="archiveUser(slotProps.data)" v-if="userInfo.id != slotProps.data.id">
                        <i class="fa fa-trash text-lg"></i>
                    </button>

                    <p class="text-red-500 hover:text-red-700 p-2 rounded-full transition"
                        v-if="userInfo.id == slotProps.data.id">
                        Votre compte
                    </p>
                </template>
            </Column>

        </DataTable>

        <AccountModal v-model:visible="showModal" @fetchUsers="fetchUsers" @close="showModal = false; updateId = null"
            :updateId="updateId" />

        <Toast />

        <ConfirmDialog />
    </div>
</template>

<script setup>
import { AccountModal } from '#components';
import { ref, onMounted } from 'vue'
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from 'primevue/usetoast';
import { useAuthStore } from '../../../store/authStore';

const confirm = useConfirm();
const userList = ref([]);
const showModal = ref(false);
const updateId = ref(null);
const toast = useToast()
const AuthStore = useAuthStore();

const userInfo = AuthStore.user

definePageMeta({
    layout: 'admin'
});


const fetchUsers = async () => {
    try {
        const response = await useApi().get('/admin/all');
        userList.value = response.data.list;
    } catch (error) {
        console.error("Erreur lors de la récupération des utilisateurs:", error);
    }
};

const editUser = (id) => {
    updateId.value = id;
    showModal.value = true;
};

const archiveUser = async (row) => {
    confirm.require({
        message: `Voulez-vous vraiment supprimer "${row.email}" ?`,
        header: 'Confirmation',
        icon: 'fa fa-danger',
        acceptLabel: 'Oui',
        rejectLabel: 'Non',
        accept: async () => {
            let deleteRequest = await useApi().post(`/admin/archive/`, {
                id: row.id
            });

            toast.add({
                severity: deleteRequest.data.state,
                summary: 'Succès',
                detail: deleteRequest.data.message,
                life: 3000
            });

            if (deleteRequest.data.state == "success") {
                fetchUsers();
            }

        },
        reject: () => {
        }
    });
}

onMounted(() => {
    fetchUsers();
});

</script>