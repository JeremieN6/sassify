<template>
  <div class="relative flex gap-5">
    <!-- Vertical line + dot -->
    <div class="flex flex-col items-center flex-shrink-0">
      <div class="w-2.5 h-2.5 rounded-full border-2 border-accent bg-[#0A0A0F] mt-1 z-10" />
      <div v-if="!isLast" class="w-px flex-1 bg-divider/60 mt-1.5" />
    </div>

    <!-- Content -->
    <div :class="['flex-1', isLast ? 'pb-0' : 'pb-10']">
      <div class="flex flex-wrap items-center gap-2.5 mb-2">
        <h4 class="font-display text-base font-bold text-white leading-tight">{{ title }}</h4>
        <span :class="['text-[10px] px-2 py-0.5 rounded-full font-semibold border', statusClass]">
          {{ statusLabel }}
        </span>
      </div>
      <p class="text-slate-400 text-sm leading-relaxed">{{ description }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
type StatusType = 'launched' | 'pivot' | 'pause' | 'build' | 'active' | 'later' | 'brief' | 'infra'

const props = defineProps<{
  title: string
  description: string
  status: StatusType
  statusLabel: string
  isLast?: boolean
}>()

const statusClass = computed(() => {
  const map: Record<StatusType, string> = {
    launched: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25',
    active: 'bg-emerald-500/15 text-emerald-400 border-emerald-500/25',
    pivot: 'bg-amber-500/15 text-amber-400 border-amber-500/25',
    pause: 'bg-slate-500/15 text-slate-400 border-slate-500/25',
    build: 'bg-blue-500/15 text-blue-400 border-blue-500/25',
    later: 'bg-purple-500/15 text-purple-400 border-purple-500/25',
    brief: 'bg-cyan-500/15 text-cyan-400 border-cyan-500/25',
    infra: 'bg-indigo-500/15 text-indigo-400 border-indigo-500/25',
  }
  return map[props.status] ?? 'bg-slate-500/15 text-slate-400 border-slate-500/25'
})
</script>
