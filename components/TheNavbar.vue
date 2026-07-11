<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 border-b border-divider/60 bg-[#0A0A0F]/85 backdrop-blur-md"
  >
    <nav class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
      <!-- Logo -->
      <NuxtLink to="/" class="font-display text-lg font-bold tracking-tight text-white">
        Sassify<span class="text-accent">.</span>
      </NuxtLink>

      <!-- Desktop nav -->
      <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-400">
        <NuxtLink
          to="/"
          class="hover:text-white transition-colors duration-200"
          :class="{ 'text-white': route.path === '/' }"
        >
          Home
        </NuxtLink>
        <NuxtLink
          to="/tools"
          class="hover:text-white transition-colors duration-200"
          :class="{ 'text-white': route.path === '/tools' }"
        >
          Tools
        </NuxtLink>
        <NuxtLink
          to="/blog"
          class="hover:text-white transition-colors duration-200"
          :class="{ 'text-white': route.path.startsWith('/blog') }"
        >
          Blog
        </NuxtLink>
      </div>

      <!-- Desktop CTA -->
      <a
        href="#subscribe"
        class="hidden md:inline-flex items-center gap-2 px-4 py-2 rounded-md bg-accent text-white text-sm font-semibold hover:bg-accent-hover transition-colors duration-200"
      >
        S'abonner
      </a>

      <!-- Mobile menu toggle -->
      <button
        @click="menuOpen = !menuOpen"
        class="md:hidden text-slate-400 hover:text-white transition-colors"
        aria-label="Menu"
      >
        <svg v-if="!menuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
      </button>
    </nav>

    <!-- Mobile menu -->
    <Transition name="menu">
      <div v-if="menuOpen" class="md:hidden border-t border-divider/60 bg-[#0A0A0F]">
        <div class="px-6 py-5 flex flex-col gap-5">
          <NuxtLink to="/" class="text-sm font-medium text-slate-400 hover:text-white transition-colors" @click="menuOpen = false">Home</NuxtLink>
          <NuxtLink to="/tools" class="text-sm font-medium text-slate-400 hover:text-white transition-colors" @click="menuOpen = false">Tools</NuxtLink>
          <NuxtLink to="/blog" class="text-sm font-medium text-slate-400 hover:text-white transition-colors" @click="menuOpen = false">Blog</NuxtLink>
          <a
            href="#subscribe"
            class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-accent text-white text-sm font-semibold hover:bg-accent-hover transition-colors"
            @click="menuOpen = false"
          >
            S'abonner
          </a>
        </div>
      </div>
    </Transition>
  </header>
</template>

<script setup lang="ts">
const route = useRoute()
const menuOpen = ref(false)

// Close mobile menu on route change
watch(() => route.path, () => {
  menuOpen.value = false
})
</script>

<style scoped>
.menu-enter-active,
.menu-leave-active {
  transition: all 0.2s ease;
  overflow: hidden;
}
.menu-enter-from,
.menu-leave-to {
  opacity: 0;
  max-height: 0;
}
.menu-enter-to,
.menu-leave-from {
  opacity: 1;
  max-height: 300px;
}
</style>
