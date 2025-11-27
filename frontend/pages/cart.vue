<template>

    <div class="bg-[#2b1b17] text-white font-merienda">

        <LazyLayoutMenu />

        <!-- SECTION HERO -->
        <section class="relative h-[90vh] bg-[#EB0000] bg-cover bg-center font-merienda"
            style="background-image: url('/images/banner.jpg')">
            <!-- Overlay noir semi-transparent -->
            <div class="absolute inset-0 bg-black opacity-50"></div>

            <!-- Contenu centré -->
            <div
                class="container mx-auto relative z-10 flex flex-col items-center justify-center h-full text-center px-4">
                <img src="/images/pirate.png" alt="pirate" class="w-32 h-32 mb-6 object-contain mx-auto" />

                <h1 class="text-4xl text-white mb-4">Ton coffre au trésor</h1>
            </div>
        </section>
        <!-- END SECTION HERO -->

        <div class="bg-[#170f09] p-5 mx-auto" v-show="cartItems.length > 0">

            <div
                class="flex flex-row justify-between  items-center h-[150px] gap-5 bg-[#170f09] text-white px-[80px] py-[90px] font-merienda">
                <!-- Partie image -->
                <div>
                    <img class="max-w-[310px] h-[150px] object-cover" src="/images/personnage.jpg" alt="Produit" />
                </div>

                <!-- Partie texte -->
                <div class="flex flex-col justify-start text-left w-full">
                    <p class="description text-lg gap-y-4 font-merienda leading-snug m-auto">
                        Chasse numérique.<br />
                        Le mystère de l’île maudite <br />
                        Départ 20/02/2025 à 20h00
                    </p>
                </div>

                <!-- Partie action -->
                <div class="flex flex-col justify-between items-end h-full w-full mb-[100px] font-merienda text-lg">
                    <div class="flex items-center space-x-2">
                        <span class="price">9,90 €</span>
                        <button class="bg-transparent border-none cursor-pointer text-white text-xl">
                            🗑️
                        </button>
                    </div>
                    <div class="flex flex-col items-end">
                        <label for="promo-input" class="mb-1">Code promo :</label>
                        <input type="text" id="promo-input"
                            class="w-[150px] h-5 px-1 py-0.5 bg-[#170f09] border border-white rounded" />
                    </div>
                </div>
            </div>

            <div class="cart-total-block flex flex-col items-end p-2.5 px-[50px] bg-[#170f09] font-merienda">
                <hr class="w-[1100px] border border-white" />
                <div class="total-line flex flex-row-reverse items-center mt-5">
                    <h3 class="total-title text-xl text-white m-0">Total : 24.80 €</h3>
                </div>

                <div class="flex gap-[50px] my-5 text-base">
                    <button
                        class="bg-[#EB0000] text-white px-[18px] py-[8px] rounded-[15px] hover:bg-[#c62828] cursor-pointer border-none">
                        Continuer mes achats
                    </button>
                    <button
                        class="bg-[#EB0000] text-white px-[18px] py-[8px] rounded-[15px] hover:bg-[#c62828] cursor-pointer border-none">
                        Valider le pannier
                    </button>
                </div>
            </div>
        </div>

        <div
            class="max-w-[1200px] w-full h-[300px] my-[60px] mx-auto p-5 bg-[#170f09] text-white flex flex-col border border-white rounded-[20px] font-merienda" v-show="cartItems.length == 0">
            <h2 class="text-center mt-2.5 text-white text-2xl">
                'Ton coffre est vide, moussaillon !'
            </h2>
            <div class="info-content">
                <div>
                    <img class="max-w-[200px] h-[200px] object-cover" src="/images/coffre_vide.png" alt="Info" />
                </div>
            </div>
        </div>

        <LazyLayoutFooter />
    </div>
</template>

<script setup>
import { ref } from "vue";

const cartItems = ref([]);
</script>
