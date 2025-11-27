<template>

    <div class="bg-[#2b1b17] text-white">
        <LazyLayoutMenu />

        <!-- SECTION HERO -->
        <section class="relative bg-cover bg-center h-[90vh] bg-[#EB0000]"
            style="background-image: url('/images/banner.jpg'); background-size: cover; background-position: center;">
            <!-- Overlay noir semi-transparent -->
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <!-- Contenu centré (titre, sous-titre, CTA) -->

            <div class="absolute bottom-4 px-4 ">
                <a href="/" class="text-lg text-[#EB0000]">Accueil > </a>
                <span class="text-lg text-[#EB0000]">Profil</span>
                <br><br>
                <h1 class="text-2xl text-white">Mon profil</h1>
            </div>
        </section>
        <!-- END SECTION HERO -->

        <section class="bg-[#160f08] pt-4 font-merienda">
            <div
                class="rounded-full border border-white  w-[200px] h-[200px] md:absolute m-auto md:top-[475px] md:right-4">
                <img :src="baseUrl + authStore.user?.avatar" alt="Mon profil" @error="handleImageError"
                    class="w-full h-full object-cover rounded-full" />
                <div
                    class="-mt-[40px] ml-[144px] z-50 rounded-full bg-[#160f08] border border-white w-[35px] h-[35px] flex items-center justify-center relative">
                    <button class="fa fa-camera text-white" @click="triggerFileInput"></button>
                    <input ref="fileInput" type="file" accept="image/*" @change="handleFileChange" class="hidden" />
                </div>
            </div>

            <div
                class="w-full  max-w-[120rem] h-[10.5rem] px-4 py-4 flex flex-col items-start justify-center gap-4 bg-[#160f08] text-white  font-bold">
                <p class="uppercase text-sm md:text-lg"> {{ authStore.user?.name }}</p>
                <p class="opacity-80 text-sm md:text-lg">{{ authStore.user?.email }} . Membre depuis : {{
                    $moment(authStore.user?.created_at).format('MMMM YYYY') }}</p>
            </div>
        </section>

        <div class="block md:grid md:grid-cols-4  bg-[#160f08]">
            <aside
                class="flex overflow-x md:flex-1 text-white p-1 md:p-5 h-auto box-border md:col-span-1 font-merienda">
                <nav>
                    <ul v-for="item in menuItems" :key="item.key">
                        <li :class="{ active: selected === item.key }">
                            <button @click="handleClick(item.key)" class="nav-link">
                                {{ item.label }}
                            </button>
                        </li>
                        <!-- Sous-menu affiché dynamiquement -->
                        <ul v-if="selected === item.key && item.children" class="submenu">
                            <li v-for="child in item.children" :key="child.key"
                                :class="{ 'active-link': submenuSeleted === child.key }">
                                <button @click="handleSupportClick(child.key)" class="nav-link">
                                    {{ child.label }}
                                </button>
                            </li>
                        </ul>
                    </ul>
                </nav>
            </aside>
            <div class="md:col-span-3">
                <LazyProfilForm v-if="selected == 'profil'" />
                <!-- <ProfilPayment v-if="selected == 'mode-paiement'" /> -->
                <LazyProfilFacture v-if="selected == 'facture'" />
                <LazyProfilConfidential v-if="selected == 'confident'" />
                <LazyProfilClassement v-if="selected == 'resultat'" />
                <div v-show="selected == 'support'" class="p-2 md:p-0">
                    <h2 class="mb-4 text-lg mt-10">
                        <span
                            class="inline-flex items-center justify-center w-6 h-6 bg-[#53a95a] text-black rounded-full text-base mr-0.5 relative -translate-y-[1px]">✔</span>
                        Support et Assistance
                    </h2>
                    <hr>
                    <div class="content pt-6">
                        <LazyProfilCGU v-if="submenuSeleted === 'cgu'" />
                        <div v-html="mention.content" v-if="submenuSeleted === 'mentions'" class="p-4"></div>
                        <div v-html="rgpd.content" v-if="submenuSeleted === 'rgpd'" class="p-4"></div>
                        <div v-html="support.content" v-if="submenuSeleted === 'assistance'" class="p-4"></div>
                        <div v-html="securisation.content" v-if="submenuSeleted === 'securisation'" class="p-4"></div>
                    </div>
                </div>

            </div>
        </div>


        <LazyLayoutFooter />
    </div>

</template>
<script setup>
import { useAuthStore } from '../../store/authStore';
import { toast } from "vue3-toastify";

const selected = ref('profil');
const authStore = useAuthStore();
const submenuSeleted = ref(null); // pour suivre le sous-menu sélectionné
const { handleImageError } = useImageFallback();
const config = useRuntimeConfig();
const baseUrl = config.public.baseUrl;
 const { $moment } = useNuxtApp();
 
const mention = ref({});
const rgpd = ref({});
const support = ref({});
const securisation = ref({});

definePageMeta({
    middleware: 'auth'
})

const menuItems = [
    { key: 'profil', label: 'Infos personnelles' },
    // { key: 'profil', label: 'Profil et infos personnelles' },
    // { key: 'mode-paiement', label: 'Mode de paiement' },
    { key: 'facture', label: 'Facturation' },
    { key: 'confident', label: 'Sécurité & Confidentialité' },
    { key: 'resultat', label: 'Mes résultats et récompenses' },
    {
        key: 'support', label: 'Support et Assistance', children: [
            { key: 'mentions', label: 'Mentions Légales' },
            { key: 'cgu', label: 'CGU et CGV' },
            { key: 'rgpd', label: 'RGPD' },
            { key: 'assistance', label: 'Support et Assistance' },
            { key: 'securisation', label: 'Sécurisation et Transparence' },
        ],
    },
]

const requestLegal = async () => {
    let request = await useApi().post("/legal_document/find", {
        slug: 'mentions-legales'
    });

    mention.value = request.data.legal;
}

const requestRGPD = async () => {
    let request = await useApi().post("/legal_document/find", {
        slug: 'politique-confidentialite'
    });

    rgpd.value = request.data.legal;
}

const requestSecurisation = async () => {
    let request = await useApi().post("/legal_document/find", {
        slug: 'securisation-transparence'
    });

    securisation.value = request.data.legal;
}

const requestSupport = async () => {
    let request = await useApi().post("/legal_document/find", {
        slug: 'support-assistance'
    });

    support.value = request.data.legal;
}

onMounted(() => {
    requestLegal();
    requestRGPD();
    requestSupport();
    requestSecurisation();
})

const handleClick = (key) => {
    selected.value = key
    const selectedItem = menuItems.find(item => item.key === key)

    if (selectedItem?.children?.length) {
        // Si sous-menus présents, on sélectionne le premier
        submenuSeleted.value = selectedItem.children[0].key
    } else {
        // Sinon on réinitialise
        submenuSeleted.value = null
    }
}

const fileInput = ref(null);
const triggerFileInput = () => {
    fileInput.value?.click();
};

// Gère l'image sélectionnée
const handleFileChange = (avent) => {
    const target = event.target;
    const file = target.files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = async () => {

            let request = await useApi().post("user/avatar/update", {
                avatar: reader.result
            });

            if (request.data.state == 'success') {
                authStore.setLoginInfo({ user: request.data.user });
                toast(request.data.message, {
                    position: "top-right",
                    type: request.data.state,
                    theme: "colored",
                    timeout: 4000,
                    closeOnClick: true,
                    pauseOnHover: false,
                    showCloseButtonOnHover: false,
                    hideProgressBar: true,
                });
            }

        };

        reader.readAsDataURL(file); // Conversion en base64
    }
};

const handleSupportClick = (subKey) => {
    submenuSeleted.value = subKey
}

</script>

<style scoped>
nav ul {
    list-style: none;
    padding: 0;

}

/* 🔹 Chaque élément de menu */
nav li {
    margin: 0.625rem 0;
    /* 10px */
    font-size: 1.125rem;
    /* 18px */
    font-weight: 400;
    padding: 0.625rem 0.9375rem;
    /* 10px 15px */
    transition: border-left 0.3s ease, background-color 0.3s ease;
    cursor: pointer;
    border-left: 0.25rem solid transparent;
    /* 4px */
    /* font-family: 'Marcellus', serif; */
}

/* 🔹 Lien du menu */
.nav-link {
    text-decoration: none;
    color: white;
    display: block;
}

/* ✅ État actif : barre rouge + fond */
li.active {
    border-left: 0.5rem solid red;
    /* 8px */
    background-color: #291b10;
    font-weight: bold;
    border-radius: 0.625rem 0 0 0.625rem;
    /* 10px */
}

/* 🖱️ Hover */
.nav-link:hover {
    border-radius: 0.625rem 0 0 0.625rem;
    /* 10px */
}

/* Sous-menu */
.submenu {
    list-style-type: none;
    padding-left: 1.25rem;
    /* 20px */
}

.submenu li {
    padding: 0.3125rem 0;
    /* 5px 0 */
    cursor: pointer;
    /* font-family: 'Arial', serif; */
}

/* 🔹 Élément de sous-menu actif */
.submenu li.active-link {
    background-color: #ff4c4c;
    color: white;
    font-weight: bold;
    padding-left: 0.625rem;
    /* 10px */
    border-radius: 0.3125rem;
    /* 5px */
}
</style>