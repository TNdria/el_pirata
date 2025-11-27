<template>
  <div>
    <NuxtLayout>
      <NuxtPage />
    </NuxtLayout>
  </div>
</template>

<script setup>
// Configuration optimisée pour éviter le FOUC
useHead({
  link: [ 
    // Fonts critiques seulement
    { rel: "stylesheet", href: "https://fonts.googleapis.com/css2?family=Pirata+One&display=swap" },
    { rel: "stylesheet", href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" },
    // Bootstrap chargé de manière asynchrone pour éviter les conflits
    { rel: "preload", href: "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css", as: "style", onload: "this.onload=null;this.rel='stylesheet'" },
    // Fallback pour Bootstrap si preload échoue
    { rel: "stylesheet", href: "https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css", media: "print", onload: "this.media='all'" },
  ],
  script: [
    { src: "https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js", defer: true },
    {
      src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js',
      type: 'text/javascript',
      defer: true,
    },
  ],
  // Meta pour optimiser le rendu
  meta: [
    { name: 'viewport', content: 'width=device-width, initial-scale=1' },
    { name: 'theme-color', content: '#111111' }
  ]
});

// Préchargement des styles critiques
onMounted(() => {
  // S'assurer que les styles critiques sont appliqués immédiatement
  const criticalStyles = document.querySelector('link[href*="critical.css"]');
  if (criticalStyles) {
    criticalStyles.rel = 'stylesheet';
  }
});
</script>
