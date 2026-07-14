<template>
  <section class="py-24 border-t border-divider/60">
    <div class="max-w-6xl mx-auto px-6">
      <div
        class="flex flex-col md:flex-row items-start md:items-center justify-between gap-8 p-8 rounded-xl border border-divider bg-surface hover:border-accent/30 transition-all duration-300 group"
      >
        <div class="flex-1">
          <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">
            06 — /tools
          </span>
          <h2 class="font-display text-3xl font-bold text-white mt-3 mb-3">
            Les outils que j'utilise au quotidien
          </h2>
          <p class="text-slate-400 max-w-lg text-sm leading-relaxed">
            SnapUI, Prospector, et les prochains, le détail de l'infrastructure qui fait tourner Skinalyze, Tifo, Plotline et FlySmart.
          </p>
        </div>

        <NuxtLink
          to="/tools"
          class="flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 rounded-lg border border-accent/40 text-accent font-semibold text-sm hover:bg-accent hover:text-white hover:border-accent transition-all duration-200 group/link font-display"
        >
          Voir les outils
          <svg
            class="w-4 h-4 group-hover/link:translate-x-1 transition-transform duration-200"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
          </svg>
        </NuxtLink>
      </div>
    </div>
  </section>
</template>
