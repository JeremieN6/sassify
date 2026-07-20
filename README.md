# Sassify Home (Nuxt 3)

Refonte full JS de home.sassify.fr.

Objectif: remplacer la home Symfony historique par une application Nuxt 3 moderne, narrative et modulaire, avec:
- /: accueil narratif
- /tools: catalogue applicatif (SnapUI, Prospector, futurs outils)
- /blog: blog reconstruit en Nuxt Content (Markdown)

Le projet de reference legacy reste intact dans backups/sassify et n est pas modifie ici.

## Pourquoi ce projet

Ce depot sert de base frontend pour documenter publiquement la construction de produits SaaS (Skinalyze, Tifo, Plotline, etc.) avec:
- une structure de sections claire
- un design dark-first coherent
- des composants reutilisables par section
- une architecture full JS sans backend Symfony pour cette app

## Stack technique

- Nuxt 3
- Vue 3 (Composition API)
- TypeScript
- Tailwind CSS
- Nuxt Content v2 (blog Markdown)
- Google Fonts (Syne + Inter)

## Structure actuelle

- app.vue: shell global (navbar, page, footer)
- pages/index.vue: page home narrative
- pages/tools.vue: route catalogue outils
- pages/blog/index.vue: listing des articles
- pages/blog/[slug].vue: detail article
- components/: sections + UI components
- content/blog/: articles markdown
- assets/css/main.css: tokens visuels, grain, grille, style prose

## Sections de la home

Les sections sont composees dans pages/index.vue:
- HeroSection
- ManifestSection
- TimelineSection
- ProjectsSection
- SassifyOSSection
- ToolsGatewaySection
- ArchivesSection
- FaqSection

UI components dedies:
- TimelineMilestone
- ProjectCard
- ArchiveCard
- SassifyOSBlock

## Scripts npm

- npm run dev: demarrage local
- npm run build: build de production
- npm run preview: previsualisation du build
- npm run generate: generation statique

## Installation et lancement local

1. Installer les dependances:

   npm install

2. Lancer en dev:

   npm run dev

Note: un profile PowerShell local peut afficher un prompt d initialisation MCP memoire avant execution.

## Blog (Nuxt Content)

Le blog est gere via des fichiers markdown dans content/blog.

Exemple de frontmatter:

---
title: Mon article
description: Resume court
date: 2026-07-11
tag: Meta
---

Le listing lit queryContent('/blog') et trie par date descendante.

## Sitemap

Le sitemap est genere automatiquement par @nuxtjs/sitemap.

- Les pages Nuxt publiques sont detectees automatiquement.
- Les articles markdown dans content/blog sont ajoutes via une source serveur dediee.
- Les routes /admin/** sont exclues du sitemap et marquees noindex.

Il n y a pas de fichier sitemap a versionner ou a ajouter dans .gitignore: la sortie est generee par Nuxt au build / runtime.

## Deploiement VPS / Nginx (cible)

Approche recommandee:
1. Build Nuxt: npm run build
2. Lancer le serveur Nuxt (node .output/server/index.mjs) derriere Nginx
3. Router:
   - / et /tools vers l app Nuxt
   - /blog vers l app Nuxt (route interne Nuxt Content)

Si un ancien blog Symfony doit coexister temporairement, gerer la bascule par location Nginx explicite.

## Contraintes projet

- Ne pas modifier backups/sassify
- Pas de dependance a flowbite.s3.amazonaws.com
- Pas de dependance a placehold.co
- Pas de stock photos par defaut: preferer captures reelles ou blocs data/typographiques

## Prochaines etapes

- Valider le rendu desktop/mobile en local
- Remplacer les placeholders visuels par vraies captures produits
- Finaliser les liens live et CTA d abonnement
- Finaliser la config Nginx de production
