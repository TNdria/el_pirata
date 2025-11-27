<template>
    <Dialog :modal="true" :closable="false" :dismissableMask="false" :style="{ width: '70%' }" :draggable="false"
        class="custom-modal font-merienda">
        <div
            class="bg-white w-full h-full rounded-[10px] border border-[#ccc] shadow-[0px_4px_10px_rgba(0,0,0,0.1)] relative p-0 overflow-hidden">
            <div
                class="bg-black text-white px-[15px] py-[15px] text-[20px] font-normal rounded-t-[10px] flex justify-between items-center w-full box-border">
                <label class="block font-normal text-white  my-2">Contacter le support</label>
                <span  @click="emit('close')">
                    <i class="fa fa-close"></i>
                </span>

            </div>

            <div class="p-4 text-black text-lg">
                <label class=""> Message </label>
                <textarea v-model="newPassword" placeholder="Écrivez votre message..." autoResize rows="10"
                    class="w-full p-2 bg-white border border-black" />
                <div class="text-right mt-4">
                    <Button label="Envoyer" icon="pi pi-send"
                        class="bg-[#EB0000] hover:bg-red-700 text-white px-5 py-2 rounded font-normal border-none"
                        @click="updatePassword" />
                </div>
            </div>

        </div>
    </Dialog>
</template>

<script setup>
import { ref } from 'vue';

const emit = defineEmits(['close']);
const showModal = ref(false);
const newPassword = ref('');

function updatePassword() {
    // logique pour envoyer le message
    console.log('Message envoyé:', newPassword.value);
    showModal.value = false;
}
</script>
