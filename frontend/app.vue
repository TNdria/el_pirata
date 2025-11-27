<template>
    <NuxtPage />
</template>

<script setup>
useHead({
  link: [
    { rel: "preconnect", href: "https://fonts.bunny.net" },
    { rel: "stylesheet", href: "https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" },
    { rel: "stylesheet", href: "https://fonts.googleapis.com/css2?family=Pirata+One&display=swap" },
    { rel: "stylesheet", href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" },
    {
      rel: "stylesheet",
      href: "https://cdn.tailwindcss.com",
    },
    // Google Fonts : Pirata One, Roboto, Dancing Script, Merienda, Poppins
    {
      rel: "stylesheet",
      href: "https://fonts.googleapis.com/css2?family=Pirata+One&family=Roboto:wght@400;700&display=swap",
    },
    {
      rel: "stylesheet",
      href: "https://fonts.googleapis.com/css2?family=Dancing+Script:wght@400;700&display=swap",
    },
    {
      rel: "stylesheet",
      href: "https://fonts.googleapis.com/css2?family=Merienda&family=Poppins:wght@400;700&display=swap",
    },
    // FontAwesome
    {
      rel: "stylesheet",
      href: "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css",
    },
  ],
  script: [
    { src: "https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js", defer: true },
    {
      src: "https://accounts.google.com/gsi/client",
      async: true,
      defer: true,
    },
    { src: "https://cdn.tailwindcss.com" },
    {
      src: 'https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js',
      type: 'text/javascript',
    },
    {
      src: 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js',
      type: 'text/javascript',
    },
  ]
});
</script>
