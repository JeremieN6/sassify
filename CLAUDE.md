# CLAUDE.md -- Memoire Projet

> Ce fichier est lu automatiquement par l'IA au debut de chaque conversation.
> Mets-le a jour a la fin de chaque session de travail.

---

## Objectif Final
home.sassify.fr : le site "building in public" de Jeremie (Nuxt 3), avec une home narrative, une route /tools, et un blog (/blog) qui documente publiquement la construction de son portefeuille de SaaS sous la marque Sassify. Le blog est alimente par une usine a contenu automatisee (backlog -> generation Claude -> validation humaine Telegram -> publication), et chaque article publie peut declencher une video courte generee via le pipeline video de Plotline (autre projet, meme proprietaire).

---

## Stack Technique
- Nuxt 3, Vue 3 (Composition API), Tailwind CSS, TypeScript
- Nuxt Content v2 (blog markdown, frontmatter a plat, parseur maison dans `scripts/lib/markdown-frontmatter.cjs`)
- SDK Anthropic JS (`claude-sonnet-4-6`) pour la generation d'articles et de scripts video
- Bot Telegram maison (`node-telegram-bot-api` en dependance, mais `scripts/telegram-bot.cjs` reimplemente son propre polling `getUpdates` a la main plutot que d'utiliser la lib) pour la validation humaine
- Deploiement : VPS + Nginx, script `.github/workflows/deploy.yml` sur push vers `main`

---

## Conventions
- Les scripts d'automatisation (`scripts/*.cjs`) sont en CommonJS, executes via `node scripts/xxx.cjs`, jamais via un framework de tache.
- Le frontmatter des articles est plat (`cle: valeur`), jamais de listes ni d'objets imbriques -- lu via `scripts/lib/markdown-frontmatter.cjs` (gere aussi les fichiers CRLF, edites sous Windows).
- Toute generation Claude (article ou script video) respecte STRICTEMENT les garde-fous de `content/knowledge-base.md`. Un sujet qui ne peut pas etre traite sans les violer -- quel que soit l'angle -- ne genere rien : le modele repond par un `REFUS:` explicite, jamais par un contenu edulcore.
- `content/knowledge-base.md` est ecrit a la main (identite, ton, garde-fous). `content/knowledge-base-generated.md` est reconstruit automatiquement par `scripts/build-knowledge-base.cjs` a partir des `STORY.md` des autres projets (`config/projects.json`) -- ne jamais l'editer a la main, ne jamais y dupliquer du contenu statique.
- Le projet backup Symfony dans `backups/sassify` est volontairement intouche.

---

## Etat Actuel du Projet
**Phase** : Usine a contenu (articles + scripts video) en fonctionnement, pont video vers Plotline branche
**Derniere session** : 2026-09-07
**Progression globale** : 65%

### Ce qui est fait :
- [x] Refonte Nuxt 3 : home narrative, /tools, /blog (`content/blog`), sitemap automatique
- [x] Backlog de sujets (`content/backlog.json`, statuts `a_faire` / `genere` / `bloque` / `ecarte`)
- [x] Generation d'articles (`scripts/generate-article.cjs`) : prend le premier sujet `a_faire`, ecrit en `statut: brouillon`, ne genere rien de nouveau tant qu'un brouillon attend une decision
- [x] Base de connaissance a deux niveaux : statique (garde-fous, identite) + generee (etat reel de chaque projet, tiree des `STORY.md`)
- [x] Validation humaine via bot Telegram maison (`scripts/telegram-bot.cjs`) : boutons Publier/Rejeter par article, commande `/status` pour lister brouillons et sujets bloques, gestion des sujets ecartes du backlog
- [x] Echappatoire manuelle sans Telegram : `scripts/decide-article.cjs <slug> <publish|reject>`
- [x] Generation de script video parle a partir d'un article publie (`scripts/generate-video-script.cjs`), deux modes de duree (`court` 10s / `long` 40-60s, bascule par `VIDEO_SCRIPT_MODE`)
- [x] Pont vers Plotline : le script video pousse un job de generation reelle via `POST /api/external/video-jobs` (persona Jeremie, decor resolu depuis `config/decors.json` selon le pilier de l'article), traçabilite dans le frontmatter de `content/video-scripts/{slug}.md` (`plotlineStatus`, `plotlineContentId`)
- [x] Notification de fin de generation : le bot Telegram verifie a chaque tour de boucle les jobs video en attente et previent quand c'est pret (ou en echec) -- la validation et la publication de la video restent entierement dans Plotline, ce ping n'est qu'une alerte

### Prochaines etapes :
- [ ] Premier test de bout en bout reel (vrai article -> vrai script -> vraie video Omni Flash -> vrai ping Telegram) -- tout est cable et verifie par morceaux, jamais enchaine en conditions reelles
- [ ] Video 40-60s (mode `long`) : bloque cote Plotline tant que l'extension de scene Omni Flash n'est pas branchee (Omni Flash ne genere que 10s par appel pour l'instant)
- [ ] Remettre a jour les contenus visuels de la home (captures projets, compteurs, liens live)

---

## Blocages et Points d Attention
- Le profile PowerShell interactif "Initialiser le MCP memoire ? (O/n)" intercepte les commandes terminal automatisees -- executer les commandes de lancement depuis un terminal manuel si besoin.
- Le projet backup Symfony dans `backups/sassify` est volontairement intouche.
- `.env.local` contient des secrets sensibles (cle SSH de deploiement, tokens Telegram/GitHub/Anthropic, `PLOTLINE_API_KEY`) -- ne jamais l'afficher en entier, se limiter aux noms de variables (`grep -oE "^[A-Z_]+="`) quand une simple verification de presence suffit.
- Le pont video vers Plotline repose sur trois variables dans `.env.local` : `PLOTLINE_API_KEY` (cle partagee, doit correspondre exactement a `EXTERNAL_VIDEO_JOBS_API_KEY` cote Plotline), `PLOTLINE_INFLUENCER_ID` (persona Jeremie), `PLOTLINE_BASE_URL` (`https://plotline.sassify.fr`). Sans `PLOTLINE_API_KEY` ou `PLOTLINE_INFLUENCER_ID`, le script video se contente d'un avertissement et continue -- il n'est jamais bloquant pour la publication de l'article.
- Le VPS execute un cron qui declenche la generation d'article (rapporte par l'utilisateur : tous les lundis a 9h) -- non verifie cote code, la planification vit hors du depot.

---

## Decisions Prises
| Date | Decision | Raison |
|------|----------|--------|
| 2026-07-11 | Refaire en nouveau projet Nuxt dans sassify/ sans modifier backups/sassify | Eviter les regressions et separer clairement la migration de stack (Symfony -> full JS) |
| 2026-07-11 | Recreer /blog dans la nouvelle app Nuxt | Conserver une architecture full JS homogene et supprimer la dependance backend Symfony |
| 2026-09-07 | Le script video (10s, Omni Flash) reste au format court pour l'instant, l'objectif reel (40-60s) est documente mais pas actif | Chaque generation Omni Flash coute cher ; ne tester qu'un format a la fois le temps de valider le pipeline |
| 2026-09-07 | La validation et la publication de la video generee restent entierement dans Plotline (boutons Valider/Publier existants), sassify n'envoie qu'un ping Telegram quand c'est pret | Eviter de dupliquer une logique de validation/publication deja eprouvee cote Plotline, et ne jamais donner a un secret partage service-a-service le pouvoir de publier directement sur les reseaux sociaux |
| 2026-09-07 | La verification des jobs video en attente vit dans la boucle deja existante de `telegram-bot.cjs`, pas dans un nouveau cron VPS | Le process tourne deja en continu (polling Telegram 30s) ; ajouter un cron separe aurait demande une configuration VPS supplementaire non verifiable a distance |

---

## Notes de Session
> Ajouter ici un resume a la fin de chaque session de travail.

- 2026-07-11: Demarrage de la refonte complete de home.sassify.fr en Nuxt 3. Mise en place du design system, des routes principales et de tous les composants de section demandes dans le brief. Objectif de cette etape: poser une base deployable et modulable pour iterer rapidement sur le contenu reel et les captures produit.
- 2026-09-07 : Construction du pont entre sassify et Plotline pour la generation video. `generate-video-script.cjs` resout desormais le pilier de l'article vers un decor (`config/decors.json`), pousse le job a Plotline (`POST /api/external/video-jobs`, persona Jeremie) et trace `plotlineStatus`/`plotlineContentId` dans le frontmatter du script. `telegram-bot.cjs` verifie ces jobs a chaque tour de sa boucle existante et envoie un ping Telegram des que la video est prete (ou en echec), sans jamais decider a la place de Plotline. Choix de validation tranche avec l'utilisateur : tout reste dans Plotline plutot que de reconstruire un flux Telegram parallele. Chaine verifiee bout en bout par morceaux (statut Plotline reel + envoi Telegram reel) sans lancer de veritable generation, pour ne pas consommer de credit avant que le flux soit valide.

---

## Lecons Apprises
> Voir tasks/lessons.md pour le detail des corrections et patterns a eviter.
