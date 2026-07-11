<template>
  <div
    class="relative p-7 rounded-xl border border-accent/20 bg-gradient-to-br from-accent/5 via-transparent to-transparent overflow-hidden"
  >
    <!-- Background glow -->
    <div
      class="absolute top-0 right-0 w-56 h-56 bg-accent/6 rounded-full blur-[80px] pointer-events-none"
    />

    <div class="relative space-y-6">
      <!-- Header -->
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-accent/10 border border-accent/20 flex items-center justify-center flex-shrink-0">
          <!-- COO agent icon -->
          <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="1.5"
              d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"
            />
          </svg>
        </div>
        <div>
          <h3 class="font-display text-xl font-bold text-white">Sassify OS</h3>
          <p class="text-slate-600 text-xs">Infrastructure interne · Non disponible à la vente</p>
        </div>
      </div>

      <!-- Description -->
      <p class="text-slate-400 text-sm leading-relaxed">
        L'infrastructure qui pilote le portefeuille : vision, mission, framework de décision, et un agent IA
        <span class="text-slate-300 font-medium">"COO"</span>
        qui challenge mes priorités quand un nouveau projet ou une nouvelle décision se présente.
      </p>

      <!-- Decision framework glimpse -->
      <div class="space-y-2">
        <div class="text-xs font-mono text-slate-600 mb-3 tracking-widest uppercase">
          Extrait du framework de décision
        </div>
        <div
          v-for="rule in rules"
          :key="rule.condition"
          class="flex items-start gap-3 p-3 rounded-lg bg-surface/70 border border-divider/60"
        >
          <span class="text-accent text-[10px] font-mono font-bold mt-0.5 flex-shrink-0 tracking-widest">IF</span>
          <div class="flex-1 text-xs leading-relaxed">
            <span class="text-slate-400">{{ rule.condition }}</span>
            <span class="text-slate-600 mx-1">→</span>
            <span class="text-accent/80 font-semibold">{{ rule.action }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const rules = [
  {
    condition: 'nouveau projet potentiel',
    action: 'Challenger avec le COO agent avant tout code',
  },
  {
    condition: "un produit ne tourne pas seul",
    action: 'Identifier le bottleneck opérationnel → automatiser',
  },
  {
    condition: 'founder-market-fit absent',
    action: 'Mettre en pause, ne pas forcer',
  },
]
</script>
