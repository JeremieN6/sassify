<template>
  <section class="py-24 border-t border-divider/60">
    <div class="max-w-6xl mx-auto px-6">
      <div class="mb-12">
        <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">07 — Archives</span>
        <h2 class="font-display text-4xl md:text-5xl font-bold text-white mt-3">Les débuts.</h2>
        <p class="text-slate-500 mt-3 max-w-xl text-sm leading-relaxed">
          Une dizaine de micro-outils construits pour apprendre à ship fast, certains encore en ligne, d'autres arrêtés. Ils font partie de l'histoire.
        </p>
      </div>

      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-1">
        <ArchiveCard v-for="tool in tools" :key="tool.name" :name="tool.name" :url="tool.url" />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const tools = [
  { name: 'Voyageur+', url: undefined as string | undefined },
  { name: 'Datagraph' },
  { name: 'Podcast IA' },
  { name: 'QuickEsti' },
  { name: 'Tajimo' },
  { name: 'Match Meuble' },
  { name: 'BabyDose' },
  { name: 'NutriMeal' },
  { name: 'Air Fryer Calculator' },
  { name: 'BotCrypto' },
  { name: 'HTML Template Automation' },
  { name: 'PreviewFast' },
  { name: 'SkinCareAI' },
]
</script>
