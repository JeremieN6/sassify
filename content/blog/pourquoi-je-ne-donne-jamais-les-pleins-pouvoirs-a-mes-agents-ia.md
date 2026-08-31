---
title: "Pourquoi je ne donne jamais les pleins pouvoirs à mes agents IA"
description: "Mes agents IA codent, proposent, documentent — mais aucun ne peut déployer, modifier une config ou pousser en prod sans que je l'aie vu passer."
slug: pourquoi-je-ne-donne-jamais-les-pleins-pouvoirs-a-mes-agents-ia
date: 2026-08-31
tag: Coulisses techniques
pilier: coulisses-techniques
type: general
hookVideo: "Tu laisses vraiment une IA toucher à ton infra ?"
statut: brouillon
---

La question revient souvent quand je parle de mes projets : tu laisses vraiment une IA toucher à ton infra ?

La réponse courte : non. Et c'est une décision assumée, pas une méfiance de principe.

## Ce que les agents font chez moi

Sur chaque projet du portfolio — Plotline, Skinalyze, Tifo, les autres — j'utilise des agents IA pour coder, rédiger de la documentation, proposer des architectures, déboguer. C'est une part importante de ma vélocité en solo. Je ne ferais pas avancer autant de projets en parallèle sans ça.

Mais il y a une frontière que j'ai posée très tôt et que je n'ai pas bougée : la validation humaine explicite avant toute action irréversible.

Pas de déploiement sans que j'aie vu ce qui part. Pas de modification de configuration sans relecture. Pas de merge automatique sur main.

## Pourquoi cette règle, concrètement

Ce n'est pas de la paranoïa. C'est de la gestion de risque proportionnée à ma situation : je suis seul, sans filet, sur des produits en production réels avec de vrais utilisateurs.

Quand quelque chose casse sur Stellara à 23h, il n'y a pas d'équipe ops. Il y a moi. Et "ça a été déployé automatiquement par l'agent" n'est pas une explication qui m'aide à dormir.

L'agent IA commet des erreurs. Pas souvent, pas toujours graves, mais il en commet. Il peut proposer une migration qui casse un flux de paiement existant. Il peut supprimer une contrainte de base de données en pensant simplifier le schéma. Il peut générer du code fonctionnel en local qui se comporte différemment une fois exposé.

Ces erreurs sont rattrapables si je les lis avant qu'elles partent en prod. Elles ne le sont pas forcément après.

## Ce que ça change dans la pratique

Sur Plotline, cette logique a d'ailleurs directement façonné une décision produit : le pipeline éditorial n'envoie jamais de contenu sur les réseaux sans qu'une validation humaine explicite soit passée. C'est le même raisonnement appliqué à un autre domaine — on ne laisse pas un automatisme prendre une décision irréversible à votre place.

Sur FlySmart, la collecte de données tourne de façon automatisée chaque jour, mais c'est du read-only : elle observe, elle stocke, elle ne modifie rien. La décision de quoi afficher reste de mon côté.

Ce n'est pas que les agents soient mauvais. C'est que certaines actions méritent un humain dans la boucle, non pas comme garde-fou émotionnel, mais comme dernier point de contrôle technique.

## La limite de l'autonomie utile

Il y a probablement un niveau d'autonomie que je pourrais déléguer davantage sans risque réel. Des tâches répétitives, des environnements de staging, des scripts de maintenance bas risque. Je ne prétends pas avoir trouvé le bon curseur pour tout.

Mais je préfère errer du côté de la friction assumée plutôt que de la fluidité incontrôlée. En solo, le coût d'une erreur de prod, c'est du temps personnel, des utilisateurs frustrés, parfois de la réputation. Pas grand-chose à gagner à accélérer sur ce terrain-là.

L'agent IA, pour moi, c'est un collaborateur très productif qui n'a pas encore les droits d'accès qui vont avec.