<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <Card>
            <template #content>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-white">Enigme</div>
                        <div class="text-2xl font-bold">{{ stats?.enigmes }}</div>
                    </div>
                    <i class="pi pi-users text-blue-500 text-3xl"></i>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-white">Abonnement</div>
                        <div class="text-2xl font-bold">{{ stats?.user }}</div>
                    </div>
                    <i class="pi pi-sitemap text-green-500 text-3xl"></i>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-white">Chasse</div>
                        <div class="text-2xl font-bold">{{ stats?.hunt }}</div>
                    </div>
                    <i class="pi pi-briefcase text-purple-500 text-3xl"></i>
                </div>
            </template>
        </Card>

        <Card>
            <template #content>
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-white">Note</div>
                        <div class="text-2xl font-bold"> {{ stats?.notes }}</div>
                    </div>
                    <i class="pi pi-chart-line text-orange-500 text-3xl"></i>
                </div>
            </template>
        </Card>
    </div>
</template>

<script setup>

const stats = ref({})
import { ref, onMounted } from 'vue';

onMounted(async () => {
    try {
        const { data } = await useApi().get('/admin/stats')
        stats.value = data || {};
    } catch (error) {
        console.error("Erreur lors du chargement des stats:", error)
    }
})

definePageMeta({
    layout: 'admin'
})
</script>