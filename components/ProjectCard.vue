<template>
  <div
    class="group p-5 rounded-xl border border-divider bg-surface hover:border-accent/35 transition-all duration-300 flex flex-col gap-4"
  >
    <!-- Screenshot placeholder -->
    <div
      class="w-full aspect-video rounded-lg bg-surface-2 border border-divider overflow-hidden flex items-center justify-center"
    >
      <slot name="screenshot">
        <span class="font-display text-3xl font-bold text-surface-3 group-hover:text-accent/20 transition-colors duration-300 select-none">
          {{ name.charAt(0) }}
        </span>
      </slot>
    </div>

    <!-- Name + Status -->
    <div class="flex items-center justify-between gap-2">
      <h3 class="font-display text-lg font-bold text-white">{{ name }}</h3>
      <span :class="['text-[10px] px-2 py-0.5 rounded-full font-semibold border whitespace-nowrap', statusClass]">
        {{ statusLabel }}
      </span>
    </div>

    <!-- Description -->
    <p class="text-slate-400 text-sm leading-relaxed flex-1">{{ description }}</p>

    <!-- Stack tags -->
    <div class="flex flex-wrap gap-1.5">
      <span
        v-for="tag in stack"
        :key="tag"
        class="text-[10px] px-2 py-0.5 rounded bg-surface-2 border border-divider text-slate-500"
      >
        {{ tag }}
      </span>
    </div>

    <!-- CTA -->
    <div class="pt-1">
      <a
        v-if="url"
        :href="url"
        target="_blank"
        rel="noopener noreferrer"
        class="inline-flex items-center gap-1.5 text-accent text-sm font-semibold group/link"
      >
        Explorer
        <svg
          class="w-4 h-4 group-hover/link:translate-x-1 transition-transform duration-200"
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
        >
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
        </svg>
      </a>
      <span v-else class="text-slate-700 text-xs">Pas encore en ligne</span>
    </div>
  </div>
</template>

<script setup lang="ts">
const props = defineProps<{
  name: string
  description: string
  status: string
  statusLabel: string
  stack: string[]
  url?: string
}>()

const statusClass = computed(() => {
  const map: Record<string, string> = {
    active: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25',
    build: 'bg-blue-500/15 text-blue-400 border-blue-500/25',
    done: 'bg-lime-500/15 text-lime-400 border-lime-500/25',
    pivot: 'bg-amber-500/15 text-amber-400 border-amber-500/25',
    strategy: 'bg-orange-500/15 text-orange-400 border-orange-500/25',
    pause: 'bg-slate-500/15 text-slate-500 border-slate-500/25',
    later: 'bg-purple-500/15 text-purple-400 border-purple-500/25',
    brief: 'bg-cyan-500/15 text-cyan-400 border-cyan-500/25',
  }
  return map[props.status] ?? 'bg-slate-500/15 text-slate-500 border-slate-500/25'
})
</script>
