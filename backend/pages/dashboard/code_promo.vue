<template>
    <div class="p-4 max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-white">Gestion des codes VIP</h1>
            <Button label="Nouvelle Promo" icon="pi pi-plus" class="p-button-success" @click="openNew" />
        </div>

        <DataTable :value="code_promo" :loading="loading" responsiveLayout="scroll" scrollable scrollHeight="500px">
            <!-- <Column field="id" header="ID" /> -->
            <Column header="Utilisateur">
                <template #body="{ data }">
                    {{ data.user?.name || '—' }}
                </template>
            </Column>
            <Column header="Email">
                <template #body="{ data }">
                    {{ data.user?.email || '—' }}
                </template>
            </Column>
            <Column field="code" header="Code promo" />
            <Column field="percent_off" header="Réduction (%)" />
            <Column header="Utilisé">
                <template #body="{ data }">
                    <span :class="data.used ? 'text-green-600 font-semibold' : 'text-red-600 font-semibold'">
                        {{ data.used ? 'Oui' : 'Non' }}
                    </span>
                </template>
            </Column>
        </DataTable>

        <!-- DIALOG FORM -->
        <Dialog v-model:visible="promoDialog" header="Promo" modal class="w-[500px]">
            <div class="flex flex-col gap-4">
                <div>
                    <label class="block mb-1 text-sm">Code</label>
                    <InputText :disabled="true" v-model="promo.code" class="w-full" placeholder="EX: RENTREE2025" />
                </div>
                <div>
                    <label class="block mb-1 text-sm">% Réduction</label>
                    <InputNumber v-model="promo.percent_off" :min="0" :max="100" class="w-full" />
                </div>
                <div>
                    <label class="block mb-1 text-sm">Valide jusqu'au</label>
                    <Calendar v-model="promo.valid_until" showTime hourFormat="24" class="w-full"
                        placeholder="Optionnel" />
                </div>
            </div>

            <template #footer>
                <Button label="Annuler" icon="pi pi-times" class="p-button-text" @click="hideDialog" />
                <Button label="Enregistrer" icon="pi pi-check" class="p-button-success" @click="savePromo" />
            </template>
        </Dialog>

        <Toast />

        <!-- CONFIRM DELETE -->
        <Dialog v-model:visible="deleteDialog" header="Confirmation" modal class="w-[400px]">
            <p>Voulez-vous vraiment supprimer la promo <b>{{ promo.code }}</b> ?</p>
            <template #footer>
                <Button label="Non" icon="pi pi-times" class="p-button-text" @click="deleteDialog = false" />
                <Button label="Oui" icon="pi pi-check" class="p-button-danger" @click="deletePromo" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const { $moment } = useNuxtApp();
const code_promo = ref([])
const loading = ref(true)
const promoDialog = ref(false)
const deleteDialog = ref(false)
const promo = ref({})
const isEdit = ref(false)
const toast = useToast()

function openNew() {
    console.log(generateVipCode());
    
   promo.value = {
    code: generateVipCode()?.shuffled, // <-- préremplit le code basé sur date/heure/minute
    percent_off: 0,
    valid_until: null
  }
  isEdit.value = false
  promoDialog.value = true
}

// Génère 4 lettres majuscules pseudo-aléatoires
function randomLetters(length = 4) {
  let result = ''
  const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
  for (let i = 0; i < length; i++) {
    result += chars.charAt(Math.floor(Math.random() * chars.length))
  }
  return result
}

// Mélange (shuffle) une chaîne
function shuffleString(str) {
  return str.split('')
    .sort(() => Math.random() - 0.5)
    .join('')
}

function generateVipCode(date = new Date()) {
  // Date en format AAAAMMJJ
  const yyyy = date.getFullYear()
  const mm = String(date.getMonth() + 1).padStart(2, '0')
  const dd = String(date.getDate()).padStart(2, '0')
  const dateStr = `${yyyy}${mm}${dd}`

  // Ajout de 4 lettres
  const letters = randomLetters(4)

  // Concaténation brute
  const baseCode = dateStr + letters // ex: 20250924AZSD

  // Si tu veux le désordonner ensuite :
  const shuffledCode = shuffleString(baseCode)

  return {
    normal: baseCode,
    shuffled: shuffledCode
  }
}

function savePromo() {
  useApi().post('/promo/save', promo.value)
    .then(({ data }) => {
      if (data.state === 'success') {
        toast.add({ severity: 'success', summary: 'Succès', detail: 'Promo enregistrée.', life: 3000 })
        // Actualiser la liste
        const index = code_promo.value.findIndex(p => p.id === promo.value.id)
        if (index !== -1) {
          code_promo.value[index] = { ...promo.value }
        } else {
          promo.value.id = data.promo.id // Assigner l'ID retourné par le backend
          code_promo.value.push({ ...promo.value })
        }
        promoDialog.value = false
      } else {
        toast.add({ severity: 'error', summary: 'Erreur', detail: data.message || 'Erreur lors de l\'enregistrement.', life: 5000 })
      }
    })
    .catch((error) => {
      console.error('Erreur lors de la sauvegarde de la promo:', error)
      toast.add({ severity: 'error', summary: 'Erreur', detail: 'Erreur lors de l\'enregistrement.', life: 5000 })
    })
    
  promoDialog.value = false
}


onMounted(async () => {
    try {
        const { data } = await useApi().get('/promo/all')
        if (data.state === 'success') {
            code_promo.value = data.promos || [];
            console.log(data.promos);

        }
    } catch (e) {
        console.error('Erreur lors du chargement des code_promo', e)
    } finally {
        loading.value = false
    }
});

// const statusLabel = (status) => {
//     switch (status) {
//         case 'pending':
//             return 'En attente'
//         case 'validated':
//             return 'Validée'
//         case 'rejected':
//             return 'Rejetée'
//         case 'cancelled':
//             return 'Annulée'
//         default:
//             return status
//     }
// }

// const exportCustomCSV = () => {
//     const headers = [
//         'Transaction Stripe',
//         'Utilisateur',
//         'Email',
//         'Code promo',
//         'Chasse',
//         'Type Paiement',
//         'Date',
//         'Montant Payé',
//         'Statut'
//     ];

//     const rows = code_promo.value.map(t => [
//         t.stripe_transaction_id || '—',
//         t.user?.name || '—',
//         t.user?.email || '—',
//         t.code_promo || '—',
//         t.hunting?.title || '—',
//         t.payment_type?.label_fr || '—',
//         $moment(t.created_at).format('DD/MM/YYYY HH:mm'),
//         `${t.amount_paid} Euro`,
//         statusLabel(t.status)
//     ]);

//     let csvContent = "data:text/csv;charset=utf-8,"
//         + [headers.join(";"), ...rows.map(r => r.join(";"))].join("\n");

//     const link = document.createElement("a");
//     link.setAttribute("href", encodeURI(csvContent));
//     link.setAttribute("download", "code_promo.csv");
//     document.body.appendChild(link);
//     link.click();
//     document.body.removeChild(link);
// };


definePageMeta({
    layout: 'admin'
});
</script>