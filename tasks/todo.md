# Todo -- Taches en cours

> Mis a jour au fil des sessions. Cocher les items termines.

---

## En cours
- [ ] Validation visuelle locale (desktop + mobile)
- [ ] Remplacement des placeholders visuels par captures reelles
- [ ] Finalisation des contenus / liens live et CTA abonnement

## Fait
- [x] Initialisation MCP memoire + structure projet
- [x] Decision architecture: nouveau projet Nuxt dans sassify/, sans toucher backups/sassify
- [x] Initialisation du scaffold Nuxt 3 + Tailwind + Nuxt Content
- [x] Creation des routes /, /tools, /blog, /blog/[slug]
- [x] Mise en place du sitemap automatique (pages publiques + articles blog, exclusion /admin/**)
- [x] Creation des composants layout (navbar/footer)
- [x] Creation des composants sections home
- [x] Creation des composants UI imposes par le brief (TimelineMilestone, ProjectCard, ArchiveCard, SassifyOSBlock)
- [x] Ajout d un premier article markdown dans content/blog
- [x] Installation des dependances npm

## Revue session (2026-07-11)
- But de la session: poser une base full JS moderne et modulaire pour la refonte complete de home.sassify.fr.
- Resultat: la structure applicative et les composants principaux sont en place, avec separation claire du projet backup.
- Risque restant: verification runtime non faite en terminal automatise a cause du profile PowerShell interactif.

## Revue session (2026-07-20)
- But de la session: brancher un sitemap maintenable sans fichier manuel.
- Resultat: sitemap auto-genere via Nuxt Sitemap avec articles blog inclus et routes admin exclues.
- Point de vigilance: verifier le build local apres integration du module.
