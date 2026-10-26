---
title: "Prospecter à l'aveugle : le prix de ne pas mesurer — partie 3/3"
description: "6 semaines de prospection, des taux d'ouverture encourageants, et aucune idée de ce qui se passait après le clic. Voilà ce que coûte de ne pas mesurer."
slug: prospecter-a-l-aveugle-le-prix-de-ne-pas-mesurer-partie-3-3
date: 2026-10-26
tag: Décisions
pilier: decisions
type: specifique
hookVideo: "Qu'est-ce qui t'a fait réaliser que tu pilotais totalement à l'aveugle ?"
statut: brouillon
---

Lancer une campagne sans tracking, ce n'est pas "avancer vite" : c'est se priver de la seule information qui compte. J'ai fait ça pendant 6 semaines sur Skinalyze, et voici ce que ça m'a coûté concrètement.

## Le contexte

Les parties 1 et 2 de cette série ont documenté les erreurs de délivrabilité et les pièges d'automatisation Brevo. Celle-ci clôt la boucle sur le problème le plus silencieux des 3 : j'ai envoyé 4 à 5 vagues de prospection sur des instituts de beauté franciliens sans aucun outil analytique en place côté site. Ma visibilité s'arrêtait au taux de clic Brevo. Ce qui se passait après le clic, sur la landing, sur le formulaire, sur le parcours de conversion, était une boîte noire complète.

## La décision et la galère

Quand j'ai finalement mis en place un suivi analytique, j'ai fait l'erreur de ne pas le tester de bout en bout avant de lancer la vague suivante. La configuration semblait correcte, les événements semblaient bien définis. J'ai envoyé la campagne.

Résultat : 2 semaines de silence total côté analytics. Aucun événement enregistré. Pas parce que personne ne visitait le site, mais parce qu'un correctif légitime côté serveur avait cassé le proxy de tracking sans produire la moindre erreur visible. J'ai relancé une campagne sur une mesure que je croyais opérationnelle et qui ne l'était pas.

La règle que j'aurais dû appliquer : après tout changement touchant la chaîne de mesure, vérifier qu'un événement réel arrive avant de considérer l'outil prêt, pas seulement que la configuration semble correcte.

C'est une règle simple. Je l'ai apprise à l'envers.

## Le résultat concret

Quand j'ai enfin eu une mesure fiable, sur la vague Paris (~140 contacts délivrés), le tableau de bord m'a montré quelque chose que je n'aurais pas pu voir avant : environ 78 % des 36 clics enregistrés étaient automatiques, produits par des robots de sécurité des messageries dans les secondes suivant la livraison. Sur les 11 contacts ayant cliqué, 6 n'avaient eu que des clics automatiques. Les 5 autres avaient probablement cliqué eux-mêmes, soit environ 4 % des destinataires délivrés. Certains avaient regardé la vidéo de démonstration, l'un avait rouvert l'exemple de rapport plusieurs jours de suite. Aucun n'avait utilisé le formulaire.

Sans tracking, j'aurais interprété un taux de clic Brevo de 25 % comme un signal positif. Avec le tracking, je vois que 4 % des gens ont probablement cliqué pour de vrai, et que personne n'est allé jusqu'au formulaire. Ce ne sont pas les mêmes conclusions, et elles ne mènent pas aux mêmes décisions.

La limite réelle de ma situation : avec si peu de visiteurs humains, je ne peux pas encore savoir si le frein est le message, la landing ou le prix. Il m'aurait fallu ce niveau de mesure dès le début pour accumuler assez de signal utile.

Un taux de clic Brevo sans distinction des clics automatiques n'est pas une métrique de prospection, c'est un bruit de fond.