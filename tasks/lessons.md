# Lessons Learned

> Ce fichier est mis a jour apres CHAQUE correction faite par l utilisateur.
> But : ne plus refaire les memes erreurs. Relu au debut de chaque session.

---

## Format

### [DATE] Titre du probleme
**Probleme** : Description de ce qui a mal tourne.
**Cause racine** : Pourquoi c est arrive.
**Solution** : Ce qui a ete fait pour corriger.
**Regle** : La regle a suivre desormais pour eviter ce cas.

---

## Lecons

<!-- Les entrees seront ajoutees ici au fil du temps -->

### [2026-07-20] Sitemap généré manuellement au lieu d'être automatisé
**Probleme** : Le projet n'avait pas de sitemap exploitable pour le référencement et aucune règle claire pour les nouvelles pages ou les articles blog.
**Cause racine** : Le site dépendait uniquement des routes existantes sans source dédiée pour les contenus Nuxt Content et sans exclusion explicite des routes d'administration.
**Solution** : Ajout de @nuxtjs/sitemap, source serveur pour les articles blog et règles de route pour exclure /admin/**.
**Regle** : Pour un sitemap Nuxt, preferer une generation automatique branchee sur les routes publiques et les contenus, pas un fichier XML versionné à la main.

### [2026-07-11] Profile PowerShell interactif et commandes automatisees
**Probleme** : Les commandes de run (npm run dev / npx nuxt dev) lancees par l agent ne demarraient pas, et la commande etait consommee comme reponse a une invite interactive.
**Cause racine** : Un profile PowerShell de projet lance un prompt "Initialiser le MCP memoire ? (O/n)" avant execution normale, ce qui bloque l automatisation.
**Solution** : Noter explicitement cette contrainte, demander a l utilisateur de repondre au prompt dans un terminal manuel pour les executions interactives.
**Regle** : Quand un profile shell injecte un Read-Host, valider runtime via terminal utilisateur ou neutraliser le profile avant d automatiser des commandes de lancement.

### [2026-07-11] URLs et promesses produit incorrectes dans la section Projets
**Probleme** : Certaines URLs de projets etaient incorrectes ou absentes, et des descriptions ne correspondaient pas au positionnement reel des produits.
**Cause racine** : Utilisation de valeurs par defaut / placeholders au lieu d attendre la source de verite metier.
**Solution** : Remplacer toutes les URLs par les domaines reels fournis et mettre a jour les descriptions avec la promesse marketing validee par l utilisateur.
**Regle** : Pour toute section produit publique, ne jamais publier d URL ou de copy sans validation explicite des domaines et messages cle avec l utilisateur.

### [2026-07-14] Mapping des URLs archives et narration timeline insuffisants
**Probleme** : La section archives n exposait pas tous les liens reels et la timeline ne racontait pas clairement l origine Datagraph -> CSVtoPPT.
**Cause racine** : Donnees historiques laissees en placeholders et absence d alignement narratif avec la source utilisateur.
**Solution** : Associer chaque projet archive a son domaine valide, ajouter le jalon Datagraph en phase 03 et reformuler la narration avec une syntaxe claire, orientee valeur.
**Regle** : Quand l utilisateur fournit un mapping URL + storytelling, appliquer exhaustivement les correspondances et harmoniser le recit dans la timeline sans simplifier le fond.

### [2026-09-28] Un brouillon d article etait visible publiquement des le cron, avant tout clic "publier" sur Telegram
**Probleme** : l utilisateur a constate au moins deux semaines de suite qu un article se retrouvait publie "tout seul", sans avoir clique sur le bouton "publier" de Telegram. Verifie en base : l article `skinalyze-m-adapter-a-une-cible-qui-ne-fonctionne-pas-comme-moi.md` (statut `brouillon`) etait deja accessible en clair sur `/blog` et directement sur `/blog/skinalyze-...`.
**Cause racine** : `generate-article.cjs` fait `git add/commit/push` de l article des sa generation (`statut: brouillon`), AVANT tout passage par Telegram. `.github/workflows/deploy.yml` deploie sur tout push sur `main`, sans condition. Or `pages/blog/index.vue` et `pages/blog/[slug].vue` interrogeaient Nuxt Content (`queryContent(...)`) SANS AUCUN `.where({ statut: ... })` -- le champ `statut` du frontmatter ne servait qu a piloter les boutons Telegram et le declenchement video, jamais a filtrer ce que le site affiche. Le clic "publier" ne faisait que changer `brouillon` en `publie` dans le fichier et lancer la video -- sans effet sur la visibilite publique, deja acquise depuis le push du cron. Le `pm2 restart sassify-telegram-bot` manque a l appel n a aucun rapport avec cette fuite : elle se produit meme bot parfaitement demarre, puisque le probleme est cote rendu du site, pas cote bot.
**Solution** : ajout de `.where({ statut: 'publie' })` sur les deux requetes (`pages/blog/index.vue`, `pages/blog/[slug].vue`). Verifie sur un build reel : l URL directe d un brouillon renvoie desormais 404, et il disparait de la liste.
**Regle** : un champ de statut editorial (frontmatter, DB...) qui pilote une notification ou un declenchement (ici Telegram/video) n implique PAS qu il filtre aussi l affichage public -- verifier explicitement que CHAQUE requete de lecture publique applique le meme filtre, surtout quand la publication git/deploiement se declenche avant la validation humaine plutot qu apres.
