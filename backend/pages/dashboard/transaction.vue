<template>
    <div class="p-4 max-w-6xl mx-auto">

        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-semibold text-white">Liste des Transactions</h2>
            <Button label="Exporter en CSV" icon="pi pi-download" class="p-button-success"
                @click="exportCustomCSV()" />
        </div>

        <DataTable  ref="dt" :value="transactions" :loading="loading" responsiveLayout="scroll" scrollable scrollHeight="500px">
            <!-- <Column field="id" header="ID" style="width: 80px" /> -->
            <Column field="stripe_transaction_id" header="Transaction Stripe" />
            <!-- <Column field="amount_paid" header="Montant Payé" /> -->
            <!-- <Column field="status" header="Statut" /> -->

            <Column header="Utilisateur">
                <template #body="{ data }">
                    <p>
                        Pseudo : {{ data.user?.name || '—' }}
                        <br>
                        Email : {{ data.user?.email || '—' }}
                    </p>

                </template>
            </Column>

            <Column field="code_prome" header="Code prome" />

            <Column header="Chasse">
                <template #body="{ data }">
                    {{ data.hunting?.title || '—' }}
                </template>
            </Column>

            <Column header="Type Paiement">
                <template #body="{ data }">
                    {{ data.payment_type?.label_fr || '—' }}
                </template>
            </Column>

            <Column header="Date">
                <template #body="{ data }">
                    {{ $moment(data.created_at).format('DD/MM/YYYY HH:mm') }}
                </template>
            </Column>

            <Column header="Montant Payé">
                <template #body="{ data }">
                    <span :class="{
                        'text-green-400 font-semibold': data?.payment_type?.direction === 'debit',
                        'text-red-400 font-semibold': data?.payment_type?.direction === 'credit'
                    }">
                        {{ data.amount_paid }} Euro
                    </span>
                </template>
            </Column>

            <Column header="Statut">
                <template #body="{ data }">
                    <span class="px-2 py-1 rounded-full text-sm font-semibold" :class="{
                        'bg-yellow-100 text-yellow-800': data.status === 'pending',
                        'bg-green-100 text-green-800': data.status === 'validated',
                        'bg-red-100 text-red-800': data.status === 'rejected',
                        'bg-gray-200 text-gray-800': data.status === 'cancelled',
                    }">
                        {{ statusLabel(data.status) }}
                    </span>
                </template>
            </Column>

        </DataTable>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
const { $moment } = useNuxtApp();

const transactions = ref([])
const loading = ref(true)

onMounted(async () => {
    try {
        const { data } = await useApi().get('/admin/transactions/all')
        if (data.state === 'success') {
            transactions.value = data.data
        }
    } catch (e) {
        console.error('Erreur lors du chargement des transactions', e)
    } finally {
        loading.value = false
    }
});

const statusLabel = (status) => {
    switch (status) {
        case 'pending':
            return 'En attente'
        case 'validated':
            return 'Validée'
        case 'rejected':
            return 'Rejetée'
        case 'cancelled':
            return 'Annulée'
        default:
            return status
    }
}

const exportCustomCSV = () => {
    const headers = [
        'Transaction Stripe',
        'Utilisateur',
        'Email',
        'Code promo',
        'Chasse',
        'Type Paiement',
        'Date',
        'Montant Payé',
        'Statut'
    ];

    const rows = transactions.value.map(t => [
        t.stripe_transaction_id || '—',
        t.user?.name || '—',
        t.user?.email || '—',
        t.code_promo || '—',
        t.hunting?.title || '—',
        t.payment_type?.label_fr || '—',
        $moment(t.created_at).format('DD/MM/YYYY HH:mm'),
        `${t.amount_paid} Euro`,
        statusLabel(t.status)
    ]);

    let csvContent = "data:text/csv;charset=utf-8," 
        + [headers.join(";"), ...rows.map(r => r.join(";"))].join("\n");

    const link = document.createElement("a");
    link.setAttribute("href", encodeURI(csvContent));
    link.setAttribute("download", "transactions.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};


definePageMeta({
    layout: 'admin'
});
</script>