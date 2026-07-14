<template>
  <section class="py-24 border-t border-divider/60">
    <div class="max-w-6xl mx-auto px-6">
      <!-- Section label -->
      <div class="mb-16">
        <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">02 — Manifeste</span>
        <h2 class="font-display text-4xl md:text-5xl font-bold text-white mt-3">
          Ce que je construis,<br class="hidden md:block" /> et pourquoi.
        </h2>
      </div>

      <!-- 4 pillars grid -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div
          v-for="item in items"
          :key="item.number"
          class="group p-7 rounded-xl border border-divider bg-surface/50 hover:border-accent/25 hover:bg-surface transition-all duration-300"
        >
          <div class="text-accent/40 text-xs font-mono mb-4 tracking-widest">{{ item.number }}</div>
          <h3 class="font-display text-xl font-bold text-white mb-3 group-hover:text-accent/90 transition-colors duration-200">
            {{ item.title }}
          </h3>
          <p class="text-slate-400 text-sm leading-relaxed">{{ item.content }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const items = [
  {
    number: '01',
    title: "Qu'est-ce que Sassify ?",
    content:
      "Un laboratoire public. J'y construis et documente des produits SaaS réels, de l'idée au marché, en solo. Pas un incubateur, pas une agence. Un builder qui ship.",
  },
  {
    number: '02',
    title: 'Pourquoi le faire en public ?',
    content:
      "Parce que la plupart des success stories masquent les vrais chiffres et les vrais échecs. Ici, tout est documenté : les pivots, les décisions, les abandons, les revenus.",
  },
  {
    number: '03',
    title: 'Comment je fonctionne ?',
    content:
      "Des cycles courts, une exécution rapide, une obsession pour l'automatisation plutôt que l'opérationnel manuel. Chaque produit est pensé pour tourner avec le minimum d'intervention, de l'acquisition (génératioon de lead + séquences email automatisées) au pilotage (Sassify OS, mon infra interne avec agent IA).",
  },
  {
    number: '04',
    title: "L'ambition",
    content:
      "Construire un portefeuille de micro-SaaS rentables et automatisés. Prouver qu'un seul builder peut aller loin avec la bonne infrastructure, les bons outils et une exécution disciplinée.",
  },
]
</script>
