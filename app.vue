<template>
  <div class="min-h-screen bg-[#0A0A0F] text-slate-100 font-body">
    <TheNavbar />
    <main>
      <NuxtPage />
    </main>
    <TheFooter />
  </div>
</template>

<script setup lang="ts">
const route = useRoute()
let revealObserver: IntersectionObserver | null = null

function cleanupReveal() {
  if (revealObserver) {
    revealObserver.disconnect()
    revealObserver = null
  }
}

function setupScrollReveal() {
  cleanupReveal()

  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    return
  }

  const sections = Array.from(document.querySelectorAll('main section'))

  revealObserver = new IntersectionObserver(
    (entries) => {
      for (const entry of entries) {
        if (!entry.isIntersecting) {
          continue
        }

        entry.target.classList.add('reveal-visible')
        revealObserver?.unobserve(entry.target)
      }
    },
    {
      threshold: 0.12,
      rootMargin: '0px 0px -10% 0px',
    }
  )

  sections.forEach((section, index) => {
    section.classList.remove('reveal-visible')
    section.classList.add('reveal-on-scroll')
    section.style.setProperty('--reveal-delay', `${Math.min(index * 70, 280)}ms`)
    revealObserver?.observe(section)
  })
}

onMounted(async () => {
  await nextTick()
  setupScrollReveal()
})

watch(
  () => route.fullPath,
  async () => {
    await nextTick()
    setupScrollReveal()
  }
)

onBeforeUnmount(() => {
  cleanupReveal()
})
</script>
