# CLAUDE.md -- Memoire Projet

> Ce fichier est lu automatiquement par l'IA au debut de chaque conversation.
> Mets-le a jour a la fin de chaque session de travail.

---

## Objectif Final
Refondre home.sassify.fr en full JS (Nuxt 3) avec une home narrative moderne, une route /tools, et une route /blog dans la meme app, sans dependance au projet Symfony backup.

---

## Stack Technique
- Nuxt 3
- Vue 3 (Composition API)
- Tailwind CSS
- Nuxt Content v2 (blog markdown)
- TypeScript
- Deploiement cible: VPS + Nginx

---

## Etat Actuel du Projet
**Phase** : Refonte frontend Nuxt initialisee
**Derniere session** : 2026-07-11
**Progression globale** : 45%

### Ce qui est fait :
- [x] Configuration MCP memoire
- [x] Nouveau projet Nuxt initialise dans le workspace principal
- [x] Design dark-first avec accent unique et structure narrative implemente
- [x] Routes creees: /, /tools, /blog, /blog/[slug]
- [x] Composants sectionnes (Hero, Manifeste, Timeline, Projets, Sassify OS, passerelle tools, archives, FAQ)
- [x] Composants UI demandes (TimelineMilestone, ProjectCard, ArchiveCard, SassifyOSBlock)
- [x] Blog Nuxt Content initialise avec un premier article placeholder
- [x] Dependances installees (npm install)

### Prochaines etapes :
- [ ] Lancer et verifier localement (le profile PowerShell bloque l execution automate)
- [ ] Remplacer les placeholders visuels par de vraies captures projets
- [ ] Ajuster les contenus finaux (abonnements, compteurs, liens live)
- [ ] Preparer config Nginx finale pour /, /tools, /blog sur la meme app Nuxt
- [x] Sitemap automatique branche sur les pages publiques et les articles blog

---

## Blocages et Points d Attention
- Le profile PowerShell interactif "Initialiser le MCP memoire ? (O/n)" intercepte les commandes terminal automatisees.
- Le projet backup Symfony dans backups/sassify est volontairement intouche.

---

## Decisions Prises
| Date | Decision | Raison |
|------|----------|--------|
| 2026-07-11 | Refaire en nouveau projet Nuxt dans sassify/ sans modifier backups/sassify | Eviter les regressions et separer clairement la migration de stack (Symfony -> full JS) |
| 2026-07-11 | Recreer /blog dans la nouvelle app Nuxt | Conserver une architecture full JS homogene et supprimer la dependance backend Symfony |

---

## Notes de Session
> Ajouter ici un resume a la fin de chaque session de travail.

- 2026-07-11: Demarrage de la refonte complete de home.sassify.fr en Nuxt 3. Mise en place du design system, des routes principales et de tous les composants de section demandes dans le brief. Objectif de cette etape: poser une base deployable et modulable pour iterer rapidement sur le contenu reel et les captures produit.

---

## Lecons Apprises
> Voir tasks/lessons.md pour le detail des corrections et patterns a eviter.
