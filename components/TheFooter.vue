<template>
  <footer class="border-t border-divider/60 py-12">
    <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
      <div class="flex flex-col sm:flex-row items-center gap-4">
        <NuxtLink to="/" class="font-display text-base font-bold text-white">
          Sassify<span class="text-accent">.</span>
        </NuxtLink>
        <span class="hidden sm:block text-divider">·</span>
        <span class="text-slate-600 text-xs text-center sm:text-left">
          Le laboratoire public d'un solo builder
        </span>
      </div>

      <nav class="flex items-center gap-6 text-sm text-slate-500">
        <NuxtLink to="/" class="hover:text-white transition-colors duration-200">Home</NuxtLink>
        <NuxtLink to="/tools" class="hover:text-white transition-colors duration-200">Tools</NuxtLink>
        <NuxtLink to="/blog" class="hover:text-white transition-colors duration-200">Blog</NuxtLink>
      </nav>

      <p class="text-slate-700 text-xs font-mono">
        © {{ year }} Sassify
      </p>
    </div>
  </footer>
</template>

<script setup lang="ts">
const year = new Date().getFullYear()
</script>
