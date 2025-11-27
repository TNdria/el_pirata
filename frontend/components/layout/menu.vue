<template>
    <header class="fixed top-0 left-0 w-full z-50 h-20 bg-black">
        <nav class="relative px-2 py-4">
            <div id="menu_web" class="flex justify-between items-center">
                <div class="mt-0">
                    <a href="/" :class="'bg-transparent px-5 py-1 text-white md:flex'" role="button">
                        <span class="text-[24px] font-merienda text-white font-[900] mr-3">
                            El
                        </span>

                        <span class="text-[24px] font-merienda text-[#EB0000] font-[900]">
                            Pirata
                        </span>
                    </a>

                </div>
                <ul class="md:flex space-x-6 ml-[-10%]">
                    <li><a href="/" class="hover:text-[#EB0000] font-[Merienda] text-lg">Accueil</a></li>
                    <li><a href="/profil" class="hover:text-[#EB0000] font-[Merienda] text-lg"
                            v-if="authStore.isAuthenticated">Profil</a></li>
                    <li class="flex relative group">
                        <a href="#" class="mr-1 hover:text-[#EB0000] font-[Merienda] text-lg">Chasses au Trésor</a>
                        <!-- Submenu starts -->
                        <ul
                            class="absolute bg-black rounded-b-md z-10 p-3 w-60 top-10 transform scale-0 group-hover:scale-100 transition duration-150 ease-in-out origin-top shadow-lg">
                            <li class=" leading-8 px-2 rounded-sm hover:text-[#EB0000] font-[Merienda] text-lg">
                                <a href="/chasses-tresors">Chasses au trésor</a>
                            </li>
                            <li class=" leading-8 px-2 rounded-sm hover:text-[#EB0000] font-[Merienda] text-lg">
                                <a href="#">Commencer l'aventure</a>
                            </li>
                        </ul>
                        <!-- Submenu ends -->
                    </li>
                    <li class="flex relative group">
                        <a href="#" class="mr-1 font-[Merienda] text-lg">Légal</a>
                        <!-- Submenu starts -->
                        <ul
                            class="absolute bg-black rounded-b-md z-10 p-3 w-[400px] top-10 transform scale-0 group-hover:scale-100 transition duration-150 ease-in-out origin-top shadow-lg left-[-130px]">

                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/mentions-legales">Mentions légales</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/politique-confidentialite">Politique de confidentialité</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/cgu">Conditions Générales d'Utilisation (CGU)</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/cgv">Conditions Générales de Vente (CGV)</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/politique-remboursement">Politique de remboursement</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/charte-ethique-engagements">Charte éthique & engagements</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/politique-cookies">Politique de cookies</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/regles-du-jeu">Règles du jeu</a>
                            </li>
                        </ul>
                        <!-- Submenu ends -->
                    </li>
                    <li class="flex relative group">
                        <a href="#" class="mr-1 hover:text-[#EB0000] font-[Merienda] text-lg">A propos</a>
                        <!-- Submenu starts -->
                        <ul
                            class="absolute bg-black rounded-b-md z-10 p-3 w-60 top-10 transform scale-0 group-hover:scale-100 transition duration-150 ease-in-out origin-top shadow-lg">
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/qui-sommes-nous">Qui sommes-nous ?</a>
                            </li>
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/faq">FAQ</a>
                            </li>
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]"
                                @click="showContactUs = true">
                                Contact
                            </li>
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]">
                                <a href="#">Se désinscrire</a>
                            </li>
                        </ul>
                        <!-- Submenu ends -->
                    </li>
                    <!-- <li><a href="#">About</a></li>
                    <li><a href="#">Contact</a></li> -->
                </ul>
                <div :class="'md:flex gap-3 items-center'">

                    <a href="/login" class="w-[25px] h-[25px] flex items-center" v-if="!authStore.isAuthenticated">
                        <img src="/images/chasses/user.png" alt="login" class="w-full h-full object-contain">
                        <!-- <a href="#" class="bg-[#EB0000] px-5 py-1 rounded-3xl hover:bg-[#EB0000] text-white carouselCarte md:flex font-[Merienda] text-lg" role="button">Sign In</a> -->
                    </a>

                    <a href="/cart" class="w-[25px] h-[25px] flex items-center text-white cart-icon"
                        v-if="authStore.isAuthenticated">
                        <img src="/images/cart_shop.svg" alt="carte shop" class="w-full h-full object-contain">
                    </a>

                    <a class="w-[25px] h-[25px] flex items-center" v-if="authStore.isAuthenticated"
                        @click="confirmLogout()">
                        <img src="/images/chasses/sign-out-option.png" alt="disconnect"
                            class="w-full h-full object-contain">
                    </a>
                    <div class="flex items-center space-x-2">
                        <img src="https://upload.wikimedia.org/wikipedia/en/c/c3/Flag_of_France.svg"
                            class="w-[20px] h-[20px]" alt="FR">
                        <span class="text-white font-normal text-lg font-[Merienda]">FR</span>
                    </div>
                </div>
            </div>

            <div id="mobile-menu" class="mt-2 absolute">
                <a href="/" :class="'bg-transparent px-5 py-1 text-white md:flex'" role="button">
                    <span class="text-[24px] font-merienda text-white font-[900] mr-3">
                        El
                    </span>

                    <span class="text-[24px] font-merienda text-[#EB0000] font-[900]">
                        Pirata
                    </span>
                </a>
            </div>

            <button id="mobile-icon" @click="showMenu = !showMenu">
                <i v-show="!showMenu" class="fa-solid fa-bars"></i>
                <i v-show="showMenu" class="fa-solid fa-close"></i>
            </button>
        </nav>


        <div class="flex justify-center mt-3 w-full duration-150 ease-in-out" v-show="showMenu">
            <div id="mobile-menu" class="mobile-menu top-23 w-1/2 ml-auto"> <!-- add carouselCarte here later -->
                <ul class="bg-black shadow-lg leading-9 font-bold h-screen text-center">
                    <li><a href="/" class="hover:text-[#EB0000] font-[Merienda] text-lg">Accueil</a></li>
                    <li><a href="/profil" class="hover:text-[#EB0000] font-[Merienda] text-lg"
                            v-if="authStore.isAuthenticated">Profil</a></li>
                    <li class="relative group text-center">
                        <a href="#" class="mr-1 hover:text-[#EB0000] font-[Merienda] text-lg w-100">Chasses au
                            Trésor</a>
                        <!-- Submenu starts -->
                        <ul
                            class="bg-transparent z-10 p-3 w-60 top-10 transform scale-0 group-hover:scale-100 transition duration-150 ease-in-out origin-top shadow-lg w-100">
                            <li class=" leading-8 px-2 rounded-sm hover:text-[#EB0000] font-[Merienda] text-lg">
                                <a href="/chasses-tresors">Chasses au trésor</a>
                            </li>
                            <li class=" leading-8 px-2 rounded-sm hover:text-[#EB0000] font-[Merienda] text-lg">
                                <a href="#">Commencer l'aventure</a>
                            </li>
                        </ul>
                        <!-- Submenu ends -->
                    </li>
                    <li class="relative group text-center">
                        <a href="#" class="mr-1 font-[Merienda] text-lg w-100">Légal</a>
                        <!-- Submenu starts -->
                        <ul
                            class="text-center bg-transparent z-10 p-3 w-52 top-10 transform scale-0 group-hover:scale-100 transition duration-150 ease-in-out origin-top shadow-lg">

                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/mentions-legales">Mentions légales</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/politique-confidentialite">Politique de confidentialité</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/cgu">Conditions Générales d'Utilisation (CGU)</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/cgv">Conditions Générales de Vente (CGV)</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/politique-remboursement">Politique de remboursement</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/charte-ethique-engagements">Charte éthique & engagements</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/politique-cookies">Politique de cookies</a>
                            </li>
                            <li class="leading-8 px-2 font-[Merienda] text-lg rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/regles-du-jeu">Règles du jeu</a>
                            </li>
                        </ul>
                        <!-- Submenu ends -->
                    </li>
                    <li class="relative group text-center">
                        <a href="#" class="mr-1 hover:text-[#EB0000] font-[Merienda] text-lg w-100">A propos</a>
                        <!-- Submenu starts -->
                        <ul
                            class="absolute bg-transparent z-10 p-3 w-52 top-10 transform scale-0 group-hover:scale-100 transition duration-150 ease-in-out origin-top shadow-lg w-10">
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/qui-sommes-nous">Qui sommes-nous ?</a>
                            </li>
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]">
                                <a href="/documents/faq">FAQ</a>
                            </li>
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]"
                                @click="showContactUs = true">
                                Contact
                            </li>
                            <li class=" leading-8 font-[Merienda] text-lg px-2 rounded-sm hover:text-[#EB0000]">
                                <a href="#">Se désinscrire</a>
                            </li>
                        </ul>
                    </li>
                    <li @click="confirmLogout()"><a href="#" class="hover:text-[#EB0000] font-[Merienda] text-lg"
                            v-if="authStore.isAuthenticated">Se déconnecter</a></li>
                    <li><a href="/login" class="hover:text-[#EB0000] font-[Merienda] text-lg"
                            v-if="!authStore.isAuthenticated">Se connecter</a></li>
                </ul>
            </div>
        </div>
    </header>

    <LazyNousContactez v-model:visible="showContactUs" @close="showContactUs = false" :isVisible="showContactUs" />

</template>

<script>
import { useAuthStore } from '../../store/authStore'
// import { toast } from "vue3-toastify";
import ConfirmationLogout from '../popup/ConfirmationLogout.vue';
export default {
    components: {
        ConfirmationLogout,
    },
    setup() {
        const authStore = useAuthStore();

        return {
            authStore
        }
    },
    data() {
        return {
            showMenu: false,
            showContactUs: false
        }
    },
    methods: {
        confirmLogout() {
            if (confirm("Êtes - vous sûr de vouloir vous déconnecter ?")) {
                const auth = useAuthStore()
                auth.logout(); // Appeler la méthode de déconnexion du store Pinia
            }

            /*
            toast.dark(ConfirmationLogout, {d
                autoClose: false,
                position: "top-center",
                closeOnClick: false,
                hideProgressBar: true,
                draggable: false,
                progress: undefined,
                style: {
                    backgroundColor: '#000000', // Bleu
                },
            });
            */
        },
    }
}
</script>
<style>
/* Ajustement pour assurer que les éléments sont nets et bien définis */
.font-merienda {
    font-family: 'Merienda', cursive;
    /* Assure l'utilisation de la police Merienda */
}

/* Supprimer les marges ou espacements inutiles pour un alignement précis */
header span {
    line-height: 1;
    /* Pour éviter des écarts verticaux inutiles */
}

.mobile-menu {
    left: -200%;
    transition: 0.5s;
}

.mobile-menu.active {
    left: 0;
}

.mobile-menu ul li ul {
    display: none;
}

.mobile-menu ul li:hover ul {
    display: block;
}

@media screen and (max-width: 820px) {
    #menu_web {
        display: none;
    }

    #mobile-icon {
        float: right;
        font-size: 2em;
    }

}

@media screen and (min-width: 821px) {

    #mobile-icon,
    #mobile-menu {
        display: none;
    }
}

.cart-icon {
    width: 28px;
    height: auto;
    cursor: pointer;
    filter: invert(100%) sepia(100%) saturate(100%) hue-rotate(0deg);
}
</style>