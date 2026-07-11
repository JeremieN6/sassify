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
      "Diagnostics peau B2B pour instituts de beauté. Pipeline d'acquisition automatisé : scraping Google Places → CSV → séquences Brevo.",
    status: 'active',
    statusLabel: 'Acquisition active',
    stack: ['Nuxt 3', 'Brevo', 'Google Places API', 'PostHog'],
    url: undefined as string | undefined,
  },
  {
    name: 'Tifo',
    description:
      "Gestion de club de foot amateur + conformité administrative pré-match. Vérificateur d'éligibilité joueurs FFF intégré.",
    status: 'active',
    statusLabel: 'Acquisition active',
    stack: ['Nuxt 3', 'Node.js', 'PostHog'],
    url: 'https://tifo.nanocorp.app',
  },
  {
    name: 'Plotline',
    description:
      "L'influenceuse IA au visage cohérent, poste après poste. Référence de visage, reproduction de scène image-to-JSON, génération via Gemini.",
    status: 'build',
    statusLabel: 'En build',
    stack: ['Gemini', 'Vue 3', 'Node.js'],
    url: undefined as string | undefined,
  },
  {
    name: 'Clippeak',
    description:
      "Clipping automatique de VOD Twitch par détection de pics de chat. Brief validé, landing page testée avant tout code.",
    status: 'brief',
    statusLabel: 'Brief validé',
    stack: ['Node.js', 'Twitch API'],
    url: undefined as string | undefined,
  },
  {
    name: 'Stellara',
    description:
      "Astrologie + IA. Rebrandée depuis AstroInsight. Canal d'acquisition à revoir — automation-first incompatible avec la création de contenu pour l'instant.",
    status: 'later',
    statusLabel: 'Plus tard',
    stack: ['Vue 3', 'OpenAI'],
    url: undefined as string | undefined,
  },
  {
    name: 'CSVtoPPT™',
    description:
      "CSV/Excel → PowerPoint prêt à présenter. En pause : founder-market-fit à confirmer, automation complète à trouver avant relance.",
    status: 'pause',
    statusLabel: 'En pause',
    stack: ['Python', 'Node.js'],
    url: undefined as string | undefined,
  },
]
</script>
