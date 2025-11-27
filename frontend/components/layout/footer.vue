<template>
    <footer class="w-full bg-black text-white py-10 ">
        <div class="mx-auto px-4">
          
            <div class="grid  grid-cols-1 md:grid-cols-4">
                <!-- Colonne de gauche -->
                <div class="text-left md:text-left mt-2 w-full">
                    <div class="space-y-3 mb-6">
                        <a href="/documents/regles-du-jeu"
                            class="block text-[18px] hover:text-gray-300 transition">Règles du jeu</a>
                        <a href="/documents/qui-sommes-nous"
                            class="block text-[18px] hover:text-gray-300 transition">Qui sommes-nous ?</a>
                        <a href="/documents/faq" class="block text-[18px] hover:text-gray-300 transition">FAQ</a>
                    </div>
                    <!-- Réseaux sociaux -->
                    <div class="flex justify-between">

                        <div class="flex justify-center md:justify-start space-x-6">
                            <a href="https://web.facebook.com/profile.php?id=100092185284129&_rdc=1&_rdr#"
                                class="text-2xl hover:text-gray-300 transition">
                                <i class="fab fa-facebook-square"></i>
                            </a>

                            <a href="https://www.snapchat.com/add/elpirata_off?invite_id=ElrPx61w&locale=fr_FR&share_id=CK3Fh2MXQCKcjGRLtXSC0Q&sid=622c513c3cb14e7dbe7dd45dc849cf6e&fbclid=IwAR2YqefBEnW9gDcGqYLd4ybcPGgcuLbTT_fWTA9tiN6jdLYWblX0QpuhUgE"
                                class="text-2xl hover:text-gray-300 transition">
                                <i class="fab fa-snapchat"></i>
                            </a>
                            <a href="https://www.tiktok.com/@elpirata_officiel?_t=8gyk7xbnf1v&_r=1&fbclid=iwar1hztoduwunlhuuncrgplmretnfh3rdirua1bwofg4w_zqio9zawkyjws4"
                                class="text-2xl hover:text-gray-300 transition">
                                <i class="fab fa-tiktok"></i>
                            </a>
                            <a href="https://www.instagram.com/elpirata_officiel/?igshid=MzMyNGUyNmU2YQ%3D%3D&utm_source=qr&fbclid=IwAR2nqKxtebhj4L3P93WUhH-EJuUyyuYWzeBTyn9njtOlbQaclx7YsZN0m4M"
                                class="text-2xl hover:text-gray-300 transition">
                                <i class="fab fa-instagram"></i>
                            </a>
                        </div>

                    </div>
                </div>
                <!-- <div class="hidden md:block"></div> -->
                <div class="text-left md:border-t-0 pt-6 md:pt-0 mt-2">
                    <div class="space-y-3">
                        <a href="/documents/cgv" class="block text-[18px] hover:text-gray-300 transition">Conditions
                            Générales d'Utilisation
                            (CGU)</a>
                        <a href="/documents/cgu" class="block text-[18px] hover:text-gray-300 transition">Conditions
                            Générales de Vente
                            (CGV)</a>
                        <a href="/documents/politique-remboursement"
                            class="block text-[18px] hover:text-gray-300 transition">Politique de remboursement</a>
                    </div>
                </div>
                <div class="text-left md:border-t-0 pt-6 md:pt-0 mt-2">
                    <div class="space-y-3">
                        <a href="/documents/mentions-legales"
                            class="block text-[18px] hover:text-gray-300 transition">Mentions légales</a>
                        <a href="/documents/politique-confidentialite"
                            class="block text-[18px] hover:text-gray-300 transition">Politique de confidentialité</a>
                        <a href="/documents/charte-ethique-engagements"
                            class="block text-[18px] hover:text-gray-300 transition">Charte éthique & engagements</a>
                    </div>
                </div>
                <div class="md:border-t-0 pt-6 md:pt-0 mt-2">
                    <div class="space-y-3 mb-2">
                        <a href="/documents/politique-cookies"
                            class="block text-[18px] hover:text-gray-300 transition">Politique de cookies</a>
                        <span class="block text-[18px] hover:text-gray-300 transition">Du mardi au vendredi</span>
                        <span class="block text-[18px] hover:text-gray-300 transition">Ouverture de 10h à 16h</span>
                        <span class="block text-[18px] hover:text-gray-300 transition">0678615358</span>
                    </div>
                    <div class="text-center">
                        <button @click="showContactUs = true"
                            class="inline-block bg-white text-black font-semibold py-2 px-6 rounded-lg hover:bg-gray-200 transition duration-200 text-right">
                            Contact
                        </button>
                    </div>
                </div>

                <!-- <div class="hidden md:block"></div> -->
            </div>

            <!-- Séparation et copyright -->
            <div class="mt-8 pt-6 border-t border-white/20">
                <p class="text-[18px] text-center">
                    Copyright © 2025 alpirata Tous droits réservés
                </p>
            </div>
        </div>
    </footer>

    <LazyNousContactez v-model:visible="showContactUs" @close="showContactUs = false" :isVisible="showContactUs" />

</template>

<script setup>
import { ref } from "vue";

const showContactUs = ref(false);

</script>

<style>
/* Pour assurer la réactivité */
@media (max-width: 768px) {
    .footer-content {
        flex-direction: column;
        gap: 2rem;
        text-align: center;
    }

    .social-icons {
        justify-content: center;
    }
}

.footer {
    background-color: #000000;
    color: #ffffff;
    padding: 2rem 0;
    font-family: 'Kalam', cursive;
    /* Pour le style d'écriture manuscrite */
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    display: flex;
    justify-content: space-between;
    padding: 0 2rem;
}

.footer-column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.footer-column a {
    color: #ffffff;
    text-decoration: none;
    font-size: 1.1rem;
}

.social-icons {
    display: flex;
    gap: 1rem;
    font-size: 1.5rem;
}

.social-icons a {
    color: #ffffff;
}

.copyright {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.copyright p {
    font-size: 0.9rem;
}


/* Définition de classes utilitaires pour nos polices */
.font-merienda {
    font-family: 'merienda', cursive;
}

.font-poppins {
    font-family: 'Poppins', sans-serif;
}
</style>