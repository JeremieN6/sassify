<template>
  <div class="pt-16 pb-24 min-h-screen">
    <div class="max-w-4xl mx-auto px-6">
      <!-- Header -->
      <div class="py-20">
        <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">Blog</span>
        <h1 class="font-display text-5xl md:text-6xl font-bold text-white mt-3 mb-4 leading-tight">
          Journal de bord.
        </h1>
        <p class="text-slate-400 text-lg">Décisions, pivots, chiffres et leçons — en direct du labo.</p>
      </div>

      <!-- Articles list -->
      <div class="space-y-4">
        <NuxtLink
          v-for="article in articles"
          :key="article._path"
          :to="article._path"
          class="group block p-6 rounded-xl border border-divider bg-surface hover:border-accent/40 transition-all duration-300"
        >
          <div class="flex items-center gap-3 mb-3">
            <span class="text-slate-600 text-xs font-mono">{{ formatDate(article.date) }}</span>
            <span
              v-if="article.tag"
              class="text-accent text-xs px-2 py-0.5 rounded-full bg-accent/10 border border-accent/20"
            >
              {{ article.tag }}
            </span>
          </div>
          <h2
            class="font-display text-xl font-bold text-white mb-2 group-hover:text-accent transition-colors duration-200"
          >
            {{ article.title }}
          </h2>
          <p class="text-slate-400 text-sm leading-relaxed">{{ article.description }}</p>
          <div
            class="flex items-center gap-2 mt-4 text-accent text-sm font-semibold opacity-0 group-hover:opacity-100 transition-opacity duration-200"
          >
            Lire l'article
            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </div>
        </NuxtLink>

        <div v-if="!articles?.length" class="py-20 text-center">
          <p class="text-slate-600 text-sm">Les premiers articles arrivent bientôt.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
useHead({
  title: 'Blog — Sassify',
  meta: [
    {
      name: 'description',
      content: 'Décisions, pivots, chiffres et leçons — en direct du labo Sassify.',
    },
  ],
})

const { data: articles } = await useAsyncData('blog-list', () =>
  queryContent('/blog').sort({ date: -1 }).find()
)

function formatDate(date: string) {
  if (!date) return ''
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(new Date(date))
}
</script>
