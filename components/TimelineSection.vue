<template>
  <section class="py-24 border-t border-divider/60">
    <div class="max-w-6xl mx-auto px-6">
      <!-- Section label -->
      <div class="mb-16">
        <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">03 — Histoire</span>
        <h2 class="font-display text-4xl md:text-5xl font-bold text-white mt-3">La timeline.</h2>
        <p class="text-slate-500 mt-3 max-w-lg text-sm">
          Chaque jalon est réel. Les pivots, les pauses, les lancements — jamais "abandonné" sauf si c'est réellement le cas.
        </p>
      </div>

      <!-- Phases -->
      <div class="space-y-20">
        <div v-for="phase in phases" :key="phase.title">
          <!-- Phase header -->
          <div class="flex items-center gap-4 mb-10">
            <span class="font-mono text-xs text-accent/50 tracking-widest uppercase">{{ phase.label }}</span>
            <div class="flex-1 h-px bg-divider/60" />
            <h3 class="font-display text-2xl font-bold text-white">{{ phase.title }}</h3>
          </div>

          <!-- Milestones -->
          <div class="pl-4">
            <TimelineMilestone
              v-for="(milestone, i) in phase.milestones"
              :key="milestone.title"
              :title="milestone.title"
              :description="milestone.description"
              :status="milestone.status"
              :status-label="milestone.statusLabel"
              :is-last="i === phase.milestones.length - 1"
            />
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const phases = [
  {
    label: 'Phase 01',
    title: 'Origines',
    milestones: [
      {
        title: 'Premiers pas',
        description:
          "Une dizaine de micro-outils solo pour apprendre à shipper vite : Voyageur+, BabyDose, NutriMeal, Tajimo, QuickEsti, Datagraph... Le terrain d'entraînement.",
        status: 'launched' as const,
        statusLabel: 'Lancé',
      },
      {
        title: 'Tifo, v1',
        description:
          "Un générateur d'affiches de match pour clubs amateurs. Le point de départ du produit qui deviendra ensuite la brique média de Tifo.",
        status: 'launched' as const,
        statusLabel: 'Lancé',
      },
      {
        title: 'Plotline, v1 (PersonaFlow)',
        description:
          "Un outil de copywriting/gestion de persona. Le point de départ, avant le vrai pivot.",
        status: 'launched' as const,
        statusLabel: 'Lancé',
      },
      {
        title: 'SkinGlow',
        description:
          "Une app d'analyse de peau grand public, qui deviendra Skinalyze.",
        status: 'pivot' as const,
        statusLabel: 'Pivot',
      },
      {
        title: 'AstroInsight',
        description:
          "Astrologie + IA pour générer des analyses de personnalité et de compatibilité.",
        status: 'pivot' as const,
        statusLabel: 'Pivot → Stellara',
      },
    ],
  },
  {
    label: 'Phase 02',
    title: 'Structuration',
    milestones: [
      {
        title: 'Pivot Tifo',
        description:
          "D'un simple générateur d'affiches vers une plateforme de gestion de club amateur, avec un vérificateur de conformité administrative pré-match (éligibilité joueurs FFF). Le poster generator devient la porte d'entrée, la conformité devient le vrai moteur de rétention.",
        status: 'pivot' as const,
        statusLabel: 'Pivot',
      },
      {
        title: 'Pivot Skinalyze',
        description:
          "Repositionnement B2B vers une promesse claire: en 60 secondes, chaque client repart avec un diagnostic peau personnalisé, sans installation ni engagement.",
        status: 'pivot' as const,
        statusLabel: 'Pivot',
      },
      {
        title: 'Pivot Plotline',
        description:
          "Pivot vers une promesse simple: ton influenceuse IA, même visage, chaque post. L'image de référence est injectée automatiquement dans les générations.",
        status: 'active' as const,
        statusLabel: 'Démarrage aqucisition',
      },
      {
        title: 'AstroInsight → Stellara',
        description:
          "Rebrandée pour un nom plus fort, plus proche de sa niche. Son canal d'acquisition naturel — la création de contenu — ne colle pas (pour l'instant) à mon approche automation-first. Pas abandonné, juste priorisé plus tard.",
        status: 'later' as const,
        statusLabel: 'Plus tard',
      },
      {
        title: 'CSVtoPPT™',
        description:
          "Question de founder-market-fit. En attente d'un moyen de l'automatiser complètement plutôt que d'y consacrer du temps manuel.",
        status: 'pause' as const,
        statusLabel: 'En pause',
      },
      {
        title: 'Clippeak',
        description:
          "Idéation complète d'un outil de clipping automatique de VOD Twitch par détection de pics de chat, validé par une landing page avant tout code. Quelques streamers ont été contactés pour tester l'outil.",
        status: 'brief' as const,
        statusLabel: 'Brief validé',
      },
      {
        title: 'FlySmart',
        description:
          "Projet B2B pour agences, comités d'entreprise et influenceurs: vos clients achètent leurs billets au bon moment, pas au mauvais prix. Jusqu'à 40% d'économies sans effort opérationnel côté équipe.",
        status: 'active' as const,
        statusLabel: 'Acquisition active',
      },
    ],
  },
  {
    label: 'Phase 03',
    title: 'Infrastructure',
    milestones: [
      {
        title: 'CI/CD unifié',
        description:
          "Un pattern GitHub Actions cohérent déployé sur tous les projets, un script deploy.sh réutilisable.",
        status: 'infra' as const,
        statusLabel: 'Infra',
      },
      {
        title: 'Analytics unifié',
        description:
          "PostHog centralisé sur un seul projet, segmenté par produit.",
        status: 'infra' as const,
        statusLabel: 'Infra',
      },
      {
        title: 'SnapUI',
        description:
          "Un outil de scraping que j'utilisais en CLI pour préparer mes projets, transformé en interface web. Premier outil du catalogue /tools.",
        status: 'launched' as const,
        statusLabel: 'Lancé',
      },
      {
        title: 'Prospector',
        description:
          "L'infra de scraping et d'outreach automatisé construite pour Skinalyze et Tifo, packagée en outil réutilisable.",
        status: 'launched' as const,
        statusLabel: 'Lancé',
      },
      {
        title: 'Sassify OS',
        description:
          "Naissance de l'infra de pilotage interne : vision, mission, framework de décision, et un agent IA \"COO\" pour challenger les priorités.",
        status: 'infra' as const,
        statusLabel: 'Infra',
      },
    ],
  },
]
</script>
