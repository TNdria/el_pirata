<template>
    <Dialog :header="updateId ? 'Modifer un énigme' : 'Créer un énigme'" :modal="true" :closable="false"
        class="w-[75%]">
        <form @submit.prevent="createOrUpdateRequest">
            <div class="mb-4 flex flex-col sm:flex-row gap-4">
                <div class="mb-3 w-[50%]">
                    <div v-if="EnigmasInfo.media && mediaPreviewTmp" class="mt-2">
                        <img :src="mediaPreviewTmp" class="h-32 rounded" alt="Preview" />
                    </div>
                    <div v-if="EnigmasInfo.media && !mediaPreviewTmp" class="mt-2">
                        <img :src="baseUrl + EnigmasInfo.media" class="h-32 rounded" alt="Preview" />
                    </div>

                </div>
                <div class="block w-auto">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white"> Publié :
                        </label>
                        <Select v-model="EnigmasInfo.is_active" :options="publishOptions" optionLabel="label"
                            optionValue="value" class="w-full" placeholder="Sélectionner" />

                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white"> Réponse :
                        </label>

                        <InputText v-model="EnigmasInfo.response" class="w-full" required />

                        <small v-if="v$.response.$errors.length" class="text-center text-red-500 mt-1">
                            {{ v$.response.$errors[0].$message }}
                        </small>

                    </div>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">

                <div class="mb-3 w-1/4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Image de
                        fond : </label>
                    <FileUpload mode="basic" name="image" accept="image/png, image/jpeg, image/webp"
                        chooseLabel="Choisir une image" @select="onImageSelect" :auto="true" />

                    <small v-if="v$.media.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.media.$errors[0].$message }}
                    </small>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Titre :
                    </label>

                    <InputText v-model="EnigmasInfo.title" class="w-full" required />

                    <small v-if="v$.title.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.title.$errors[0].$message }}
                    </small>

                </div>

                <div class="mb-3 w-1/4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Chasse : </label>

                    <Select v-model="EnigmasInfo.hunting_id" showClear :options="huntingList" optionLabel="title"
                        optionValue="id" class="w-full" />

                </div>

                <div class="mb-3 w-1/4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Niveau : </label>

                    <Select v-model="EnigmasInfo.level" :options="levelOptions" optionLabel="label" optionValue="value"
                        class="w-full" />

                    <small v-if="v$.level.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.level.$errors[0].$message }}
                    </small>
                </div>

            </div>

            <div class="block sm:flex-row mt-2 gap-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-white"> Description : </label>
                <Editor v-model="EnigmasInfo.text_content" editorStyle="height: 200px" class="mt-2"
                    placeholder="Écris le contenu ici..." />

                <small v-if="v$.text_content.$errors.length" class="text-center text-red-500 mt-1">
                    {{ v$.text_content.$errors[0].$message }}
                </small>

            </div>

            <div class="mt-4 flex justify-end">
                <Button label="Annuler" class="p-button-text" @click="emit('close');EnigmasInfo.value = {};" />
                <Button label="Valider" class="p-button-info ml-2" type="submit" />
            </div>
        </form>
    </Dialog>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useVuelidate } from '@vuelidate/core';
import { useToast } from 'primevue/usetoast';
import { requiredField, requiredIf } from '../../composables/validators'
const { compressImage } = useImageCompressor();

const toast = useToast()
const config = useRuntimeConfig();
const baseUrl = config.public.baseUrl;

const props = defineProps({
    showModal: Boolean,
    updateId: Number,
    huntingList: Array
});

// Watch sur huntingList
watch(
    () => props.huntingList,
    (newVal, oldVal) => {
    },
    { deep: true, immediate: true }
);

const emit = defineEmits(['close', 'fetchEnigmas']);

const EnigmasInfo = ref({
    is_active: 0,
    media_type: 'image'
});

const mediaPreviewTmp = ref(null);

const publishOptions = [
    { label: 'Oui', value: 1 },
    { label: 'Non', value: 0 }
]

const levelOptions = [
    { label: 'Facile', value: 'easy' },
    { label: 'Normal', value: 'normal' },
    { label: 'Difficile', value: 'hard' }
];

const rules = {
    media: { requiredField },
    level: { requiredField },
    response: { requiredField },
    title: { requiredField },
    text_content: { requiredField },
}

const v$ = useVuelidate(rules, EnigmasInfo);

mediaPreviewTmp.value = null;

watch(
    () => props.updateId,
    async (newVal) => {
        if (newVal) {
            findRequest(newVal);
        } else {
            EnigmasInfo.value = {
                is_active: 0,
                media_type: 'image'
            };
            mediaPreviewTmp.value = null;
        }
    }
)

watch(
    () => EnigmasInfo.value, 
    () => v$.value.$validate(), 
    { deep: true }
)

const onImageSelect = async (event) => {
    const file = event.files?.[0]
    if (!file) return
    let compressedImage = await compressImage(file);
    mediaPreviewTmp.value = compressedImage;
    EnigmasInfo.value.media = compressedImage;
}

const findRequest = async (id) => {
    let dataFinding = await useApi().get('/admin/enigmas/find/' + id);
    console.log(dataFinding.data);

    if (dataFinding.data.enigma.id) {
        EnigmasInfo.value = dataFinding.data.enigma
    }
};


const createOrUpdateRequest = async () => {

    const isValid = await v$.value.$validate()

    if (!isValid) return;

    let data = EnigmasInfo.value;
    if (mediaPreviewTmp.value) {
        data.media = mediaPreviewTmp.value;
    }

    let createOrUpdate = await useApi().post('/admin/enigmas/createOrupdate', {
        EnigmasInfo: data
    });

    toast.add({
        severity: createOrUpdate.data.state,
        summary: 'Succès',
        detail: createOrUpdate.data.message,
        life: 3000
    });

    if (createOrUpdate.data.state == 'success') {
        EnigmasInfo.value = {};
        emit('fetchEnigmas');
        emit('close');
    }
};

</script>

<style>
.p-datepicker-weekday-cell {
    text-align: center !important;
}
</style>