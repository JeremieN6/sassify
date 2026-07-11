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

### [2026-07-11] Profile PowerShell interactif et commandes automatisees
**Probleme** : Les commandes de run (npm run dev / npx nuxt dev) lancees par l agent ne demarraient pas, et la commande etait consommee comme reponse a une invite interactive.
**Cause racine** : Un profile PowerShell de projet lance un prompt "Initialiser le MCP memoire ? (O/n)" avant execution normale, ce qui bloque l automatisation.
**Solution** : Noter explicitement cette contrainte, demander a l utilisateur de repondre au prompt dans un terminal manuel pour les executions interactives.
**Regle** : Quand un profile shell injecte un Read-Host, valider runtime via terminal utilisateur ou neutraliser le profile avant d automatiser des commandes de lancement.
