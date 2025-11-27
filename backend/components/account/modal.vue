<template>
    <Dialog :header="updateId ? 'Modifier un utilisateur' : 'Créer un utilisateur'" :modal="true" :closable="false"
        class="w-[500px]">
        <form @submit.prevent="createOrUpdateRequest">
            <!-- User name -->
            <div class="mb-3">
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-white">Nom
                    d'utilisateur</label>
                <InputText id="name" v-model="userInfo.name" class="w-full" />

                <small v-if="v$.name.$errors.length" class="text-center text-red-500 mt-1">
                    {{ v$.name.$errors[0].$message }}
                </small>
            </div>

            <!-- User Email -->
            <div class="mb-3">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-white">Email</label>
                <InputText id="email" v-model="userInfo.email" class="w-full" />
                <small v-if="v$.email.$errors.length" class="text-center text-red-500 mt-1">
                    {{ v$.email.$errors[0].$message }}
                </small>
            </div>

            <div class="mb-3">
                <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-white">Rôle</label>
                <Select v-model="userInfo.role_id" :options="roleList" optionLabel="label_fr" optionValue="id"
                    class="w-full" />
                <small v-if="v$.role_id.$errors.length" class="text-center text-red-500 mt-1">
                    {{ v$.role_id.$errors[0].$message }}
                </small>
            </div>

            <div class="mb-3">
                <label for="pwd" class="block text-sm font-medium text-gray-700 dark:text-white">Mot de
                    passe</label>
                <InputText id="pwd" type="password" v-model="userInfo.password" placeholder="Entrez le mot de passe"
                    class="w-full" required />

                <small v-if="v$.password.$errors.length" class="text-center text-red-500 mt-1">
                    {{ v$.password.$errors[0].$message }}
                </small>

            </div>

            <div class="mb-3">
                <label for="pwd_confirm" class="block text-sm font-medium text-gray-700 dark:text-white">Confirmation de
                    mot de passe</label>
                <InputText id="pwd_confirm" type="password" v-model="userInfo.password_confirm"
                    placeholder="Entrez la confirmation de mot de passe" class="w-full" required />

                <small v-if="v$.password_confirm.$errors.length" class="text-center text-red-500 mt-1">
                    {{ v$.password_confirm.$errors[0].$message }}
                </small>

            </div>

            <!-- Action buttons -->
            <div class="mt-4 flex justify-end">
                <Button label="Annuler" class="p-button-text" @click="emit('close')" />
                <Button label="Valider" class="p-button-info ml-2" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { useToast } from 'primevue/usetoast';
import { requiredField, validEmail, sameAsPassword } from '../../composables/validators'

const toast = useToast()

const props = defineProps({
    showModal: Boolean,
    updateId: Number
});

const emit = defineEmits(['close', 'fetchUsers']);

const userInfo = ref({
    id: null,
    email: '',
    name: '',
    role_id: null,
    password: '',
    password_confirm: ''
});

const roleList = ref([]);

const rules = {
    email: { requiredField, validEmail },
    name: { requiredField },
    password: { requiredField },
    role_id: { requiredField },
    password_confirm: {
        requiredField,
        sameAsPassword: sameAsPassword(() => userInfo.value.password)
    },
}

const v$ = useVuelidate(rules, userInfo);


watch(
    () => props.updateId,
    async (newVal) => {
        if (newVal) {
            findRequest(newVal);
        } else {
            userInfo.value = {}
        }
    }
)

watch(
    () => userInfo.value, 
    () => v$.value.$validate(), 
    { deep: true }
)


const findRequest = async (id) => {
    let dataFinding = await useApi().get('/admin/find/' + id);

    if (dataFinding.data.user.id) {
        userInfo.value = dataFinding.data.user
    }
};

const fetchRole = async () => {
    let fetchingRole = await useApi().get('/role/all');

    if (fetchingRole.data.list) {
        roleList.value = fetchingRole.data.list
    }
};


const createOrUpdateRequest = async () => {

    const isValid = await v$.value.$validate()
    if (!isValid) return;

    let data = userInfo.value;
    delete data.password_confirm;
    let createOrUpdate = await useApi().post('/admin/createOrupdate', {
        userInfo: data
    });

    toast.add({
        severity: createOrUpdate.data.state,
        summary: 'Succès',
        detail: createOrUpdate.data.message,
        life: 3000
    });

    if (createOrUpdate.data.state == 'success') {
        emit('fetchUsers');
        emit('close');
    }
};

// monter

onMounted(() => {
    fetchRole();
})
</script>

<style scoped>
/* Personnalisation des styles */
</style>