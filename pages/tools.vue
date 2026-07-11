<template>
  <div class="pt-16 pb-24 bg-grid min-h-screen">
    <div class="max-w-6xl mx-auto px-6">
      <!-- Header -->
      <div class="py-20">
        <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">Catalogue applicatif</span>
        <h1 class="font-display text-5xl md:text-6xl font-bold text-white mt-3 mb-5 leading-tight">
          Les outils<br class="hidden md:block" /> que je construis.
        </h1>
        <p class="text-slate-400 max-w-xl text-lg">
          Chaque outil ici a été construit pour résoudre un vrai problème rencontré en buildant Skinalyze, Tifo ou Plotline — puis rendu accessible.
        </p>
      </div>

      <!-- Tools grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div
          v-for="tool in tools"
          :key="tool.name"
          class="group p-6 rounded-xl border border-divider bg-surface hover:border-accent/40 transition-all duration-300 flex flex-col gap-5"
        >
          <!-- Icon + Status -->
          <div class="flex items-start justify-between gap-3">
            <div class="w-10 h-10 rounded-lg bg-accent/10 border border-accent/20 flex items-center justify-center flex-shrink-0">
              <svg
                class="w-5 h-5 text-accent"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
                v-html="tool.iconPath"
              />
            </div>
            <span
              :class="[
                'text-xs px-2 py-0.5 rounded-full font-medium border',
                tool.available
                  ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25'
                  : 'bg-slate-500/15 text-slate-400 border-slate-500/25',
              ]"
            >
              {{ tool.available ? 'Disponible' : 'Coming soon' }}
            </span>
          </div>

          <!-- Name + Description -->
          <div class="flex-1">
            <h2 class="font-display text-xl font-bold text-white mb-2">{{ tool.name }}</h2>
            <p class="text-slate-400 text-sm leading-relaxed">{{ tool.description }}</p>
          </div>

          <!-- Stack tags -->
          <div class="flex flex-wrap gap-1.5">
            <span
              v-for="tag in tool.stack"
              :key="tag"
              class="text-[11px] px-2 py-0.5 rounded bg-surface-2 border border-divider text-slate-400"
            >
              {{ tag }}
            </span>
          </div>

          <!-- CTA -->
          <div>
            <a
              v-if="tool.url"
              :href="tool.url"
              target="_blank"
              rel="noopener noreferrer"
              class="inline-flex items-center gap-2 text-accent text-sm font-semibold group/link"
            >
              Accéder
              <svg
                class="w-4 h-4 group-hover/link:translate-x-1 transition-transform"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </a>
            <span v-else class="text-slate-600 text-sm">En développement</span>
          </div>
        </div>

        <!-- Future tools placeholder -->
        <div class="p-6 rounded-xl border border-dashed border-divider flex flex-col items-center justify-center gap-3 min-h-48 text-center">
          <div class="w-8 h-8 rounded-full border border-divider flex items-center justify-center">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
          </div>
          <p class="text-slate-600 text-sm">Prochain outil en construction</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
useHead({
  title: 'Tools — Sassify',
  meta: [
    {
      name: 'description',
      content:
        "Les outils construits pour faire tourner l'infrastructure Sassify : SnapUI, Prospector, et les prochains.",
    },
  ],
})

const tools = [
  {
    name: 'SnapUI',
    description:
      "Outil de scraping visuel. Utilisé en CLI pour préparer les projets : extraction de structure DOM, tokens visuels, screenshot automatisé. Transformé en interface web accessible.",
    available: true,
    stack: ['Node.js', 'Puppeteer', 'Vue 3'],
    url: undefined as string | undefined,
    iconPath:
      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7h18M3 12h18M3 17h12"/>',
  },
  {
    name: 'Prospector',
    description:
      "Infrastructure de scraping et d'outreach automatisé construite pour Skinalyze et Tifo. Scraping Google Places → enrichissement CSV → intégration Brevo pour les séquences email. Packagée en outil réutilisable.",
    available: true,
    stack: ['Node.js', 'Google Places API', 'Brevo', 'CSV'],
    url: undefined as string | undefined,
    iconPath:
      '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>',
  },
]
</script>
