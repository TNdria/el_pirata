<template>
  <div class="flex-grow bg-[#160f08] text-white p-8 font-[Merienda]">
    <h2 class="text-lg font-normal font-[Merienda]">
      <span
        class="inline-flex items-center justify-center w-6 h-6 bg-[#53a95a] text-black rounded-full text-base mr-0.5 relative -translate-y-[1px]">✔</span>
      Paiement
    </h2>
    <hr class="border-none h-[2px] bg-[#26211b] my-4" />

    <label class="text-base font-normal font-[Merienda]">Ajoutez les coordonnées de votre carte pour effectuer des
      achats en toute
      sécurité.</label>
    <br />

    <label class="text-sm font-normal mt-4 block font-[Merienda]">Coordonnées de la carte</label>
    <div class="flex justify-between gap-20 mt-2">
      <div class="w-full relative max-w-[900px]">
        <input type="text" id="card-number" placeholder="Numéro"
          class="w-full py-3 pl-4 pr-12 text-base rounded-t-lg bg-white text-black box-border" required />
        <div class="absolute top-1/2 left-[520px] transform -translate-y-1/2 flex gap-2 text-[1.5rem] text-gray-600">
          <i class="fa-brands fa-cc-mastercard text-[#EB001B]"></i>
          <i class="fa-brands fa-cc-visa text-[#1A1F71]"></i>
          <i class="fa-brands fa-cc-amex text-[#2E77BB]"></i>
          <i class="fa-brands fa-cc-discover text-[#FF6000]"></i>
        </div>
      </div>
    </div>

    <div class="flex w-full max-w-[680px] mt-4">
      <input type="text" placeholder="MM/AA"
        class="flex-1 py-3 px-4 text-base bg-white text-black rounded-bl-lg outline-none" />
      <input type="text" placeholder="MM/AA"
        class="flex-1 py-3 px-4 text-base bg-white text-black rounded-br-lg outline-none border-l-0" />
    </div>

    <br />

    <label class="text-sm block">Titulaire de la carte</label>
    <input type="text" placeholder="Titulaire de la carte"
      class="w-full py-3 pl-4 pr-12 text-base bg-white text-black box-border my-2" required />

    <label class="text-sm block">Adresse de facturation</label>
    <input type="text" placeholder="Adresse de facturation"
      class="w-full py-3 pl-4 pr-12 text-base bg-white text-black box-border my-2" required />

    <div class="flex items-center gap-2 mt-5">
      <input type="checkbox" id="policy"
        class="w-[18px] h-[18px] rounded bg-white appearance-none flex items-center justify-center checked:bg-[#EB0000]" />
      <label for="policy" class="mt-1.5 text-sm">J'accepte <span class="text-[#EB0000] underline">la politique de sécurité
          et de confidentialité</span></label>
    </div>

    <button
      class="bg-[#EB0000] text-white border-none px-5 py-2.5 text-base cursor-pointer mt-12 rounded-md w-fit relative left-[550px]">Enregistrer</button>

    <br />

    <label class="text-sm block mt-4">Mode de paiement</label>
    <div class="bg-[#f8f8f8] rounded-lg p-2.5 shadow text-[#333] font-[Marcellus] mt-2 mr-1">
      <div class="flex justify-between text-base text-black">
        <span><i
            class="fa-brands fa-cc-mastercard text-[#EB001B] text-[1.5rem] align-middle inline-flex items-center mr-1.5"></i>
          ****5348</span>
        <span>Expire le II/26</span>
      </div>
      <hr class="border-t border-gray-300 my-2" />
      <div>
        <p class="text-base my-1">Nom sur la carte : Jhon Doe</p>
        <p class="text-base my-1">Adresse de facturation : 8344O, FR</p>
      </div>
    </div>

    <span class="text-[#EB0000] underline cursor-pointer mt-2 block w-fit relative left-[404px]">Consulter l'historique
      des paiements</span>

    <button
      class="bg-[#EB0000] text-white border-none px-5 py-2.5 text-base cursor-pointer mt-12 rounded-md w-fit relative left-[410px]">Sauvegarder
      les modifications</button>
  </div>
</template>
<script setup>
</script>
