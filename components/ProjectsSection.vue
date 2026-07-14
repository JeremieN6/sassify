<template>
  <section class="py-24 border-t border-divider/60">
    <div class="max-w-6xl mx-auto px-6">
      <!-- Section label -->
      <div class="mb-16">
        <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">04 — Projets</span>
        <h2 class="font-display text-4xl md:text-5xl font-bold text-white mt-3">
          En cours de construction.
        </h2>
        <p class="text-slate-500 mt-3 text-sm">
          Les produits actifs du portefeuille, avec leur état réel.
        </p>
      </div>

      <!-- Projects grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <ProjectCard
          v-for="project in projects"
          :key="project.name"
          v-bind="project"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const projects = [
  {
    name: 'Skinalyze',
    description:
      "En 60 secondes, chaque client repart avec un diagnostic peau personnalisé. Co-analysé par un dermatologue, un cosmétologue et un coach bien-être. Sans installation. Sans engagement.",
    status: 'active',
    statusLabel: 'Acquisition active',
    stack: ['Nuxt 3', 'Brevo', 'Google Places API', 'PostHog'],
    url: 'https://skinalyze.sassify.fr',
  },
  {
    name: 'Tifo',
    description:
      "Une affiche pro avant chaque match de ton club. Tifo génère automatiquement des affiches visuelles qui s'adaptent à l'enjeu du match, derby, finale, match décisif (maintien, relégation, titre en jeu). En quelques secondes, pour tous les réseaux. Feature/Pivot potentiel : analyse de feuille de match pour prévention d'erreur.",
    status: 'active',
    statusLabel: 'Acquisition active',
    stack: ['Nuxt 3', 'Node.js', 'PostHog'],
    url: 'https://tifo.sassify.fr',
  },
  {
    name: 'FlySmart',
    description:
      "Projet B2B orienté agences, comités d'entreprise et influenceurs. Promesse: aider leurs clients à acheter leurs billets au bon moment avec jusqu'à 40% d'économies. Priorité actuelle: cadrer une stratégie go-to-market B2B solide avant l'acquisition.",
    status: 'strategy',
    statusLabel: 'GTM B2B à définir',
    stack: ['Nuxt 3', 'B2B'],
    url: 'https://flysmart.sassify.fr/',
  },
  {
    name: 'Plotline',
    description:
      "Ton influenceuse IA. Même visage. Chaque post. Plotline injecte automatiquement l'image de référence de ton influenceuse dans chaque génération pour que la cohérence visuelle ne dépende plus de toi.",
    status: 'done',
    statusLabel: 'Fin de développement',
    stack: ['Gemini', 'Vue 3', 'Node.js'],
    url: 'https://plotline.sassify.fr',
  },
  {
    name: 'Clippeak',
    description:
      "Clipping automatique de VOD Twitch par détection de pics de chat. Brief validé, landing page testée avant tout code. Quelques streamers contactés pour tester l'outil.",
    status: 'brief',
    statusLabel: 'Brief validé',
    stack: ['Node.js', 'Twitch API'],
    url: 'https://clippeak.sassify.fr',
  },
  {
    name: 'Stellara',
    description:
      "Découvrez ce que les étoiles révèlent de vous. Générez votre rapport natal complet : positions planétaires, maisons astrologiques et analyse personnalisée en quelques secondes.",
    status: 'later',
    statusLabel: 'Plus tard',
    stack: ['Vue 3', 'OpenAI'],
    url: 'https://stellara.sassify.fr',
  },
  {
    name: 'CSVtoPPT™',
    description:
      "Transformez vos CSV en PPT en quelques secondes. Uploadez votre fichier CSV ou Excel et obtenez instantanément une présentation PowerPoint formatée, structurée et prête à présenter. En pause : founder-market-fit à confirmer, automation complète à trouver avant relance.",
    status: 'pause',
    statusLabel: 'En pause',
    stack: ['Python', 'Node.js'],
    url: 'https://csvtoppt.fr',
  },
]
</script>
