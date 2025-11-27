<template>
  <div class="flex-grow bg-[#160f08] text-white p-8 font-[Merienda]">
    <h2 class="mb-4 text-lg mt-10">
      <span
        class="inline-flex items-center justify-center w-6 h-6 bg-[#53a95a] text-black rounded-full text-base mr-0.5 relative -translate-y-[1px]">✔</span>
      Facturation
    </h2>

    <hr class="h-1 bg-[#26211b] my-4 border-none" />

    <label class="text-base">Gérer vos factures et vos paiements</label>
    <br>
    <label class="text-base mt-2 block">MES FACTURES</label>

    <div class="relative flex items-center   mt-8 text-base  justify-end">
      <input type="text" placeholder="Taper pour rechercher..."
        class="w-full text-md border border-gray-300 rounded-lg outline-none min-w-[270px] bg-[#160f08] text-white px-4 py-2 w-[250px]" />
      <div
        class="top-0 bottom-0 w-[50px] h-[46px] -ml-[12px] bg-[#EB0000] rounded-r-lg flex items-center justify-center">
        <i class="fa-solid fa-search text-white text-base cursor-pointer"></i>
      </div>
    </div>

    <div class="max-w-[100%] overflow-x-auto rounded-lg border border-gray-500 text-md mt-4">

      <DataTable :value="factures" :paginator="true" :rows="5" :rowsPerPageOptions="[5, 10, 15]"
        class="w-full border-collapse custom_datatable text-md" tableStyle="min-width: 100%">


        <Column header="Date" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            <span>{{ data.date }}</span>
          </template>
        </Column>

        <Column header="N° Facture" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            <span>{{ data.numero }}</span>
          </template>
        </Column>

        <Column header="Mode de paiement" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            <span>{{ data.paiement }}</span>
          </template>
        </Column>

        <Column header="Total" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            <span>{{ data.total }}</span>
          </template>
        </Column>

        <Column header="Statut" :headerClass="'bg-[#575757] text-white'">
          <template #body="{ data }">
            <span :class="data.statut === 'Payé'
              ? 'text-green-500 font-bold border border-green-500 rounded-xl px-2 py-1 text-sm inline-block'
              : 'text-red-500 font-bold border border-red-500 rounded-xl px-2 py-1 text-sm inline-block'">
              {{ data.statut }}
            </span>
          </template>
        </Column>

        <Column header="Actions" :headerClass="'bg-[#575757] text-white'">
          <template #body>
            <div class="flex items-center gap-2 justify-center">
              <div class="bg-white rounded-md p-2 border border-gray-300 flex items-center justify-center">
                <svg class="w-6 h-6 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                  stroke-width="2" viewBox="0 0 24 24">
                  <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"></path>
                  <circle cx="12" cy="12" r="3"></circle>
                </svg>
              </div>
              <div class="bg-green-600 rounded-md p-2 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor"
                  stroke-width="2" viewBox="0 0 24 24">
                  <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                  <polyline points="7 10 12 15 17 10"></polyline>
                  <line x1="12" y1="15" x2="12" y2="3"></line>
                </svg>
              </div>
            </div>
          </template>
        </Column>
      </DataTable>
    </div>

    <LazyNousContactez v-model:visible="showModal" @close="showModal = false" :isVisible="showContactUs"/>

    <div class="flex items-center gap-2 mt-5 text-md">
      <button @click="showModal = true"
        class="bg-[#EB0000] text-white border-none py-2.5 px-5 text-md cursor-pointer rounded mt-6 block w-fit relative ml-auto font-[Merienda]">
        Contacter le support par mail
      </button>
    </div>

  </div>
</template>
<script setup>
import { ref } from 'vue';

// Liste des factures
const factures = ref([
  {
    date: "2025-03-16",
    numero: "Pirata-05-14-12",
    paiement: "Carte de crédit",
    total: "35€",
    statut: "En attente",
  },
  {
    date: "2025-03-15",
    numero: "Pirata-05-14-12",
    paiement: "PayPal",
    total: "50€",
    statut: "Payé",
  },
  {
    date: "2025-03-16",
    numero: "Pirata-05-14-12",
    paiement: "Carte de crédit",
    total: "35€",
    statut: "Payé",
  },
  {
    date: "2025-03-15",
    numero: "Pirata-05-14-12",
    paiement: "PayPal",
    total: "50€",
    statut: "Payé",
  },
  {
    date: "2025-03-16",
    numero: "Pirata-05-14-12",
    paiement: "Carte de crédit",
    total: "35€",
    statut: "En attente",
  },
  {
    date: "2025-03-15",
    numero: "Pirata-05-14-12",
    paiement: "PayPal",
    total: "50€",
    statut: "Payé",
  },
  {
    date: "2025-03-16",
    numero: "Pirata-05-14-12",
    paiement: "Carte de crédit",
    total: "35€",
    statut: "Payé",
  },
  {
    date: "2025-03-15",
    numero: "Pirata-05-14-12",
    paiement: "PayPal",
    total: "50€",
    statut: "Payé",
  },
  {
    date: "2025-03-16",
    numero: "Pirata-05-14-12",
    paiement: "Carte de crédit",
    total: "35€",
    statut: "En attente",
  },
  {
    date: "2025-03-15",
    numero: "Pirata-05-14-12",
    paiement: "PayPal",
    total: "50€",
    statut: "Payé",
  },
  {
    date: "2025-03-16",
    numero: "Pirata-05-14-12",
    paiement: "Carte de crédit",
    total: "35€",
    statut: "Payé",
  },
  {
    date: "2025-03-15",
    numero: "Pirata-05-14-12",
    paiement: "PayPal",
    total: "50€",
    statut: "Payé",
  },
]);

// État du modal et mot de passe
const showModal = ref(false);
const oldPassword = ref("");
const newPassword = ref("");
const confirmPassword = ref("");

// Fonctions
function openModalContact() {
  showModal.value = true;
}

function closeModal() {
  showModal.value = false;
}

function updatePassword() {
  if (newPassword.value !== confirmPassword.value) {
    alert("Les mots de passe ne correspondent pas !");
    return;
  }
  alert("Mot de passe mis à jour !");
  closeModal();
}
</script>
