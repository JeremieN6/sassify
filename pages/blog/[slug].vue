<template>
  <div class="pt-16 pb-24 min-h-screen">
    <div v-if="article" class="max-w-3xl mx-auto px-6">
      <!-- Back -->
      <div class="py-10">
        <NuxtLink
          to="/blog"
          class="inline-flex items-center gap-2 text-slate-500 text-sm hover:text-white transition-colors"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          Retour au blog
        </NuxtLink>
      </div>

      <!-- Article header -->
      <header class="mb-12 pb-10 border-b border-divider">
        <div class="flex items-center gap-3 mb-5">
          <span class="text-slate-600 text-xs font-mono">{{ formatDate(article.date) }}</span>
          <span
            v-if="article.tag"
            class="text-accent text-xs px-2 py-0.5 rounded-full bg-accent/10 border border-accent/20"
          >
            {{ article.tag }}
          </span>
        </div>
        <h1 class="font-display text-4xl md:text-5xl font-bold text-white leading-tight mb-5">
          {{ article.title }}
        </h1>
        <p v-if="article.description" class="text-slate-400 text-lg leading-relaxed">
          {{ article.description }}
        </p>
      </header>

      <!-- Article content -->
      <ContentRenderer
        :value="article"
        class="prose max-w-none prose-headings:font-display prose-a:text-accent prose-a:no-underline hover:prose-a:underline prose-code:text-indigo-300 prose-pre:bg-surface prose-pre:border prose-pre:border-divider"
      />
    </div>

    <!-- 404 fallback -->
    <div v-else class="flex items-center justify-center min-h-[60vh]">
      <div class="text-center space-y-3">
        <p class="font-display text-2xl font-bold text-white">Article introuvable.</p>
        <NuxtLink to="/blog" class="text-accent text-sm inline-block hover:underline">
          ← Retour au blog
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
const route = useRoute()

const { data: article } = await useAsyncData(`blog-${route.params.slug}`, () =>
  queryContent(`/blog/${route.params.slug as string}`).findOne()
)

if (!article.value) {
  throw createError({ statusCode: 404, statusMessage: 'Article introuvable' })
}

useHead({
  title: `${article.value?.title} — Sassify`,
  meta: [
    { name: 'description', content: article.value?.description ?? '' },
  ],
})

function formatDate(date: string) {
  if (!date) return ''
  return new Intl.DateTimeFormat('fr-FR', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
  }).format(new Date(date))
}
</script>
