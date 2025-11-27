<template>
    <Dialog :header="updateId ? 'Modifer un chasses' : 'Créer un chasses'" :modal="true" :closable="false"
        class="w-[75%]">
        <form @submit.prevent="createOrUpdateRequest">
            <div class="mb-4 flex flex-col sm:flex-row gap-4">
                <div class="mb-3 w-[50%]">
                    <div v-if="HuntingInfo.image && ImagePreviewTmp" class="mt-2">
                        <img :src="ImagePreviewTmp" class="h-32 rounded" alt="Preview" />
                    </div>
                    <div v-if="HuntingInfo.image && !ImagePreviewTmp" class="mt-2">
                        <img :src="baseUrl + HuntingInfo.image" class="h-32 rounded" alt="Preview" />
                    </div>
                </div>
                <div class="block w-[50%]">
                    <div class="mb-3">
                        <label class="block text-sm font-medium text-gray-700 dark:text-white"> Prix d'inscription :
                        </label>
                        <InputNumber v-model="HuntingInfo.registration_fee" mode="currency" currency="EUR"
                            locale="fr-FR" class="w-full" />

                        <small v-if="v$.registration_fee.$errors.length" class="text-center text-red-500 mt-1">
                            {{ v$.registration_fee.$errors[0].$message }}
                        </small>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-white"> Publié :
                        </label>
                        <Dropdown v-model="HuntingInfo.is_published" :options="publishOptions" optionLabel="label"
                            optionValue="value" class="w-full" placeholder="Sélectionner" />

                    </div>
                </div>
            </div>
            <div class="grid md:grid-cols-3 gap-4">

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Image de
                        fond : </label>
                    <FileUpload mode="basic" name="image" accept="image/png, image/jpeg, image/webp"
                        chooseLabel="Choisir une image" @select="onImageSelect" :auto="true" />

                    <small v-if="v$.image.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.image.$errors[0].$message }}
                    </small>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Titre: </label>

                    <InputText v-model="HuntingInfo.title" class="w-full" />

                    <small v-if="v$.title.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.title.$errors[0].$message }}
                    </small>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Place limite: </label>

                    <InputNumber v-model="HuntingInfo.limit_place" class="w-full" />

                    <small v-if="v$.limit_place.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.limit_place.$errors[0].$message }}
                    </small>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Type : </label>

                    <Dropdown v-model="HuntingInfo.type" :options="typeOptions" optionLabel="label" optionValue="value"
                        class="w-full" />

                    <small v-if="v$.type.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.type.$errors[0].$message }}
                    </small>

                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Catégorie : </label>

                    <Dropdown v-model="HuntingInfo.category" :options="catOptions" optionLabel="label"
                        optionValue="value" class="w-full" :disabled="HuntingInfo.type == 'digital'" />

                    <small v-if="v$.category.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.category.$errors[0].$message }}
                    </small>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white">Date du début : </label>

                    <Calendar v-model="HuntingInfo.start_date" dateFormat="dd/mm/yy" :locale="'fr'" showTime
                        hourFormat="24" showIcon fluid class="w-full text-center" />

                    <small v-if="v$.start_date.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.start_date.$errors[0].$message }}
                    </small>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Niveau : </label>

                    <Dropdown v-model="HuntingInfo.level" :options="levelOptions" optionLabel="label"
                        optionValue="value" class="w-full" />

                    <small v-if="v$.level.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.level.$errors[0].$message }}
                    </small>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-white"> Prix à gagner: </label>

                    <InputNumber v-model="HuntingInfo.prize_win" mode="currency" currency="EUR" locale="fr-FR"
                        class="w-full" />

                    <small v-if="v$.prize_win.$errors.length" class="text-center text-red-500 mt-1">
                        {{ v$.prize_win.$errors[0].$message }}
                    </small>
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
    updateId: Number
});

const emit = defineEmits(['close', 'fetchHunting']);

const HuntingInfo = ref({});
const ImageTmp = ref(null);
const ImagePreviewTmp = ref(null);

const typeOptions = [
    { label: 'Physique', value: 'physic' },
    { label: 'Numérique', value: 'digital' }
];

const publishOptions = [
    { label: 'Oui', value: 1 },
    { label: 'Non', value: 0 }
]

const levelOptions = [
    { label: 'Facile', value: 'easy' },
    { label: 'Normal', value: 'normal' },
    { label: 'Difficile', value: 'hard' }
];

const catOptions = [
    { label: 'Chasse physique urbaine', value: 'urban_physical_hunt' },
    { label: 'Aventure mystique', value: 'mystic_adventure' },
    { label: 'Exploration souterraine', value: 'underground_exploration' },
    { label: 'Course au trésor', value: 'treasure_race' },
    { label: 'Défi extrême', value: 'extreme_challenge' },
    { label: 'Chasse nocturne', value: 'night_hunt' },
]

const rules = {
    image: { requiredField },
    type: { requiredField },
    category: { requiredIf: requiredIf(() => HuntingInfo.value.type == 'physic') },
    start_date: { requiredField },
    prize_win: { requiredField },
    level: { requiredField },
    registration_fee: { requiredField },
    limit_place: { requiredField },
    title: { requiredField }
}

const v$ = useVuelidate(rules, HuntingInfo);

ImagePreviewTmp.value = null;

watch(
    () => props.updateId,
    async (newVal) => {
        if (newVal) {
            findRequest(newVal);
        } else {
            HuntingInfo.value = {};
            ImagePreviewTmp.value = null;
        }
    }
)

watch(
    () => HuntingInfo.value, 
    () => v$.value.$validate(), 
    { deep: true }
)

const onImageSelect = async (event) => {
    const file = event.files?.[0]
    if (!file) return
    let compressedImage = await compressImage(file);
    ImagePreviewTmp.value = compressedImage;
    HuntingInfo.value.image = compressedImage;
}

watch(
    () => HuntingInfo.value.type,
    (newVal) => {
        if (newVal === 'digital') {
            HuntingInfo.value.category = null; // Réinitialiser la catégorie
        }
    }
);

const findRequest = async (id) => {
    let dataFinding = await useApi().get('/admin/hunting/find/' + id);

    if (dataFinding.data.hunting.id) {
        dataFinding.data.hunting.start_date = new Date(dataFinding.data.hunting.start_date);
        HuntingInfo.value = dataFinding.data.hunting
    }
};


const createOrUpdateRequest = async () => {

    const isValid = await v$.value.$validate()

    if (!isValid) return;

    let data = HuntingInfo.value;
    if (ImagePreviewTmp.value) {
        data.image = ImagePreviewTmp.value;
    }

    let createOrUpdate = await useApi().post('/admin/hunting/createOrupdate', {
        HuntingInfo: data
    });

    toast.add({
        severity: createOrUpdate.data.state,
        summary: 'Succès',
        detail: createOrUpdate.data.message,
        life: 3000
    });

    if (createOrUpdate.data.state == 'success') {

        emit('fetchHunting');
        emit('close');
    }
};


</script>

<style>
.p-datepicker-weekday-cell {
    text-align: center !important;
}
</style>