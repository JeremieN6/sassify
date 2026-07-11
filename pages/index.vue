<template>
  <div class="bg-grid">
    <HeroSection />
    <ManifestSection />
    <TimelineSection />
    <ProjectsSection />
    <SassifyOSSection />
    <ToolsGatewaySection />
    <ArchivesSection />
    <FaqSection />
  </div>
</template>

<script setup lang="ts">
useHead({
  title: "Sassify — Le laboratoire public d'un solo builder",
  meta: [
    {
      name: 'description',
      content:
        "Je construis de vrais produits SaaS — Skinalyze, Tifo, Plotline — de l'idée au marché. Les décisions, les pivots, les chiffres, les abandons.",
    },
  ],
})
</script>
