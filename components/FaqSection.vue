<template>
  <section class="py-24 border-t border-divider/60">
    <div class="max-w-6xl mx-auto px-6">
      <div class="mb-16">
        <span class="text-accent text-xs font-semibold tracking-widest uppercase font-display">08 — FAQ</span>
        <h2 class="font-display text-4xl md:text-5xl font-bold text-white mt-3">Questions fréquentes.</h2>
      </div>

      <div class="max-w-5xl space-y-2">
        <div
          v-for="(item, i) in faqs"
          :key="i"
          class="border border-divider rounded-xl overflow-hidden"
        >
          <button
            @click="toggle(i)"
            class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left hover:bg-surface/60 transition-colors duration-200"
          >
            <span class="font-display font-semibold text-white text-sm">{{ item.question }}</span>
            <svg
              :class="[
                'w-4 h-4 text-slate-500 flex-shrink-0 transition-transform duration-200',
                open === i ? 'rotate-180' : '',
              ]"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <Transition name="accordion">
            <div v-if="open === i" class="px-6 pb-5 border-t border-divider/40">
              <p class="text-slate-400 text-sm leading-relaxed pt-4">{{ item.answer }}</p>
            </div>
          </Transition>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
const open = ref<number | null>(null)

function toggle(i: number) {
  open.value = open.value === i ? null : i
}

const faqs = [
  {
    question: "Qu'est-ce que Sassify exactement ?",
    answer:
      "Sassify est mon laboratoire public. J'y construis des produits SaaS réels en solo, et je documente tout le processus : les décisions, les pivots, les chiffres, les abandons. Pas un incubateur, pas une agence, un builder qui ship.",
  },
  {
    question: 'Est-ce un incubateur ?',
    answer:
      "Non. Il n'y a pas de programme, pas de sélection, pas d'investissement externe. Sassify, c'est moi — un solo builder qui construit plusieurs produits en parallèle avec une infrastructure unifiée.",
  },
  {
    question: 'Quels types de projets construis-tu ?',
    answer:
      "Des micro-SaaS B2B ou B2C avec des besoins réels identifiés. Chaque produit doit pouvoir fonctionner de manière largement automatisée, de l'acquisition à l'opérationnel.",
  },
  {
    question: "Comment puis-je suivre l'avancement ?",
    answer:
      "En t'abonnant via le formulaire en haut de la page. Je partage les updates significatives : pivots, lancements, chiffres, décisions. Pas de newsletter weekly vide — seulement quand il se passe quelque chose.",
  },
  {
    question: "Certains projets sont 'en pause', ils sont abandonnés ?",
    answer:
      "Non. 'En pause' signifie que je n'ai pas trouvé le bon levier (automation, founder-market-fit, canal d'acquisition) pour les faire avancer sans y consacrer du temps manuel. Ils reviendront quand le contexte sera favorable.",
  },
  {
    question: 'Peut-on collaborer ou rejoindre un projet ?',
    answer:
      "Je suis ouvert aux discussions. Si tu as une expertise complémentaire sur un projet spécifique et une approche automation-first, contacte-moi ici (contact@sassify.fr) ou sur X (Twitter - @jeremiecode).",
  },
  {
    question: "Est-ce que tu cherches des investisseurs ?",
    answer:
      "Pas activement. L'objectif est de bootstrapper jusqu'à la rentabilité. Si une opportunité de financement s'aligne avec la vision, ça pourrait changer, mais ce n'est pas l'axe prioritaire.",
  },
  {
    question: 'Fais-tu du coaching ou consulting ?',
    answer:
      "Non, pas dans le cadre de Sassify. Mon temps est dédié à la construction des produits. Si tu construis quelque chose de similaire, le blog est la meilleure ressource disponible.",
  },
]
</script>

<style scoped>
.accordion-enter-active,
.accordion-leave-active {
  transition: all 0.22s ease;
  overflow: hidden;
}
.accordion-enter-from,
.accordion-leave-to {
  opacity: 0;
  max-height: 0;
}
.accordion-enter-to,
.accordion-leave-from {
  opacity: 1;
  max-height: 300px;
}
</style>
