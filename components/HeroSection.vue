<template>
  <section class="relative min-h-screen flex items-center pt-16 pb-16 overflow-hidden">
    <!-- Radial glow -->
    <div
      class="absolute top-1/4 left-1/4 w-[600px] h-[600px] bg-accent/8 rounded-full blur-[140px] pointer-events-none"
    />

    <div class="relative max-w-6xl mx-auto px-6 w-full grid grid-cols-1 lg:grid-cols-2 gap-16 lg:gap-20 items-center py-16">
      <!-- Left: Headline + CTA -->
      <div class="space-y-8">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-accent/30 bg-accent/8 text-accent text-xs font-semibold tracking-widest uppercase font-display">
          Journal de bord · Solo builder
        </div>

        <h1 class="font-display text-5xl md:text-6xl xl:text-7xl font-bold leading-[1.0] text-white">
          Le laboratoire<br />
          public d'un<br />
          <span class="text-accent">solo builder</span>
        </h1>

        <p class="text-slate-400 text-lg leading-relaxed max-w-md">
          Ici je documente la construction de vrais produits SaaS — Skinalyze, Tifo, Plotline, FlySmart — de l'idée au marché.
          Les décisions, les pivots, les chiffres, les abandons.
        </p>

        <!-- Subscribe form -->
        <div id="subscribe" class="flex flex-col sm:flex-row gap-3 max-w-md">
          <input
            type="email"
            placeholder="ton@email.com"
            class="flex-1 px-4 py-3 rounded-lg bg-surface border border-divider text-white placeholder:text-slate-600 focus:outline-none focus:border-accent/60 transition-colors text-sm"
          />
          <button
            class="px-6 py-3 rounded-lg bg-accent text-white font-semibold text-sm hover:bg-accent-hover transition-colors whitespace-nowrap font-display"
          >
            S'abonner
          </button>
        </div>

        <p class="text-slate-600 text-xs flex items-center gap-2">
          <span class="text-amber-400">⭐</span>
          Rejoins les builders qui suivent l'aventure · Aucune carte requise
        </p>
      </div>

      <!-- Right: Project status grid -->
      <div class="grid grid-cols-2 gap-3">
        <div
          v-for="project in projects"
          :key="project.name"
          class="p-4 rounded-lg border border-divider bg-surface/80 hover:border-accent/30 transition-all duration-300 flex flex-col gap-3"
        >
          <div class="flex items-start justify-between gap-2">
            <span class="font-display font-bold text-white text-sm leading-tight">{{ project.name }}</span>
            <span :class="['text-[10px] px-1.5 py-0.5 rounded-full font-semibold whitespace-nowrap flex-shrink-0', statusClass(project.status)]">
              {{ project.statusLabel }}
            </span>
          </div>
          <p class="text-slate-500 text-xs leading-relaxed flex-1">{{ project.tagline }}</p>
          <div class="flex flex-wrap gap-1">
            <span
              v-for="tag in project.stack"
              :key="tag"
              class="text-[10px] px-1.5 py-0.5 rounded bg-surface-2 text-slate-600"
            >
              {{ tag }}
            </span>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const projects = [
  {
    name: 'Skinalyze',
    tagline: 'Diagnostic peau personnalisé en 60 secondes',
    status: 'active',
    statusLabel: 'Acquisition',
    stack: ['Nuxt', 'Brevo'],
  },
  {
    name: 'Tifo',
    tagline: 'Affiches de match pro générées en secondes',
    status: 'active',
    statusLabel: 'Acquisition',
    stack: ['Nuxt', 'PostHog'],
  },
  {
    name: 'FlySmart',
    tagline: 'Analyse de prix vols B2B jusqu\'à 40% d\'économies',
    status: 'active',
    statusLabel: 'Acquisition',
    stack: ['Nuxt', 'B2B'],
  },
  {
    name: 'Plotline',
    tagline: 'Ton influenceuse IA. Même visage. Chaque post.',
    status: 'build',
    statusLabel: 'Démarrage aqucisition',
    stack: ['Gemini', 'Vue'],
  },
  {
    name: 'Clippeak',
    tagline: 'Clipping auto Twitch, premiers streamers contactés',
    status: 'brief',
    statusLabel: 'Brief validé',
    stack: ['Node.js'],
  },
  {
    name: 'Stellara',
    tagline: 'Rapport natal complet en quelques secondes',
    status: 'later',
    statusLabel: 'Plus tard',
    stack: [],
  },
  {
    name: 'CSVtoPPT™',
    tagline: "CSV → PowerPoint · en attente d'automation",
    status: 'pause',
    statusLabel: 'En pause',
    stack: [],
  },
]

function statusClass(status: string) {
  const map: Record<string, string> = {
    active: 'bg-emerald-500/15 text-emerald-400',
    build: 'bg-blue-500/15 text-blue-400',
    pivot: 'bg-amber-500/15 text-amber-400',
    pause: 'bg-slate-500/15 text-slate-500',
    later: 'bg-purple-500/15 text-purple-400',
    brief: 'bg-cyan-500/15 text-cyan-400',
  }
  return map[status] ?? 'bg-slate-500/15 text-slate-500'
}
</script>
