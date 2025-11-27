<template>
    <Dialog header="Modification d'un document" :modal="true" :closable="false" class="w-[75%]">
        <form @submit.prevent="createOrUpdateRequest">
            <div class="block">
                <div class="mb-3 w-full">
                    <label class="block text-lg font-medium text-gray-700 dark:text-white"> Titre : {{ legalInfo.title
                    }} </label>
                    <label class="block text-lg font-medium text-gray-700 dark:text-white"> Slug : {{ legalInfo.slug }}
                    </label>
                </div>
                <div class="mb-3 w-full">
                    <label class="block text-lg font-medium text-gray-700 dark:text-white"> Description : </label>
                    <Editor v-model="legalInfo.content" editorStyle="height: 320px" class="mt-2"
                        placeholder="Écris le contenu ici..." />
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <Button label="Annuler" class="p-button-text" @click="emit('close')" />
                <Button label="Valider" class="p-button-info ml-2" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, watch } from 'vue';
import { useToast } from 'primevue/usetoast';

const legalInfo = ref({});
const toast = useToast()
const props = defineProps({
    showModal: Boolean,
    updateId: Number
});

const emit = defineEmits(['close', 'fetchLegal']);


watch(
    () => props.updateId,
    async (newVal) => {
        if (newVal) {
            findRequest(newVal);
        } else {
            legalInfo.value = {};
        }
    },
)
const createOrUpdateRequest = async () => {

    let data = legalInfo.value;

    let createOrUpdate = await useApi().post('/legal_document/createOrupdate', {
        legalInfo: data
    });

    toast.add({
        severity: createOrUpdate.data.state,
        summary: 'Succès',
        detail: createOrUpdate.data.message,
        life: 3000
    });

    if (createOrUpdate.data.state == 'success') {
        emit('fetchLegal');
        emit('close');
    }
};

const findRequest = async (id) => {
    let dataFinding = await useApi().post('/legal_document/find', {
        id: id
    });

    if (dataFinding.data.legal.id) {
        legalInfo.value = dataFinding.data.legal;
    }
};

</script>