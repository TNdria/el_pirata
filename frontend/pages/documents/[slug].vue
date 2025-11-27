<template>

    <div class="bg-[#2b1b17] text-white">
        <LazyLayoutMenu />

        <section v-html="legal?.content" class="pt-[100px] px-[10%] pb-[25px] font-merienda">

        </section>

        <LazyLayoutFooter />
    </div>


</template>

<script setup>
import { ref, onMounted } from "vue";

const route = useRoute();
const legal = ref({});
const { slug } = route.params;

const requestLegal = async () => {
    let request = await useApi().post("/legal_document/find", {
        slug: slug
    });

    legal.value = request.data.legal;
}

onMounted(() => {
    requestLegal();
})

</script>