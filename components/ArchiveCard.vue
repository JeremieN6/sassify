<template>
  <component
    :is="url ? 'a' : 'div'"
    :href="url"
    :target="url ? '_blank' : undefined"
    :rel="url ? 'noopener noreferrer' : undefined"
    class="group flex items-center gap-3 px-4 py-2.5 rounded-lg border border-transparent hover:border-divider hover:bg-surface/60 transition-all duration-200"
  >
    <div class="w-1.5 h-1.5 rounded-full bg-slate-700 group-hover:bg-accent/50 transition-colors flex-shrink-0" />
    <span class="text-slate-500 text-sm group-hover:text-slate-300 transition-colors duration-200">{{ name }}</span>
    <svg
      v-if="url"
      class="w-3 h-3 text-slate-700 group-hover:text-accent/50 ml-auto opacity-0 group-hover:opacity-100 transition-all duration-200"
      fill="none"
      stroke="currentColor"
      viewBox="0 0 24 24"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
    </svg>
  </component>
</template>

<script setup lang="ts">
defineProps<{
  name: string
  url?: string
}>()
</script>
