---
title: "Les automatisations Brevo qui mentaient sans prévenir — partie 2/3"
description: "Six vagues de prospection envoyées, et pendant des semaines, Brevo affichait tout vert. Rien n'était vert."
slug: les-automatisations-brevo-qui-mentaient-sans-prevenir-partie-2-3
date: 2026-10-19
tag: Coulisses techniques
pilier: coulisses-techniques
type: specifique
hookVideo: "Comment une automatisation peut planter sans qu'aucune erreur ne s'affiche ?"
statut: brouillon
---

Une automatisation silencieuse est plus dangereuse qu'une erreur franche. Au moins une erreur franche, tu la vois.

Sur Skinalyze, j'ai passé plusieurs vagues à croire que mes relances partaient. Les taux d'ouverture du premier email semblaient corrects, rien ne signalait de problème dans l'interface, et pourtant les relances ne touchaient personne. Aucune alerte, aucun compteur rouge. Juste le silence.

Voilà ce qui s'est réellement passé.

## Des branches conditionnelles qui ne pouvaient jamais être vraies

Le workflow était construit sur une logique simple : si le contact a ouvert l'email 1, il reçoit la relance A ; sinon, il reçoit la relance B. Sauf que la condition "a ouvert" référençait une ancienne campagne email, pas l'email d'automatisation en cours. Brevo fait une distinction entre une "campagne" (envoi manuel ou planifié depuis l'onglet Campagnes) et un "email d'automatisation" (envoyé depuis un workflow). Un email envoyé depuis un workflow n'est pas une campagne. La condition qui cherchait une campagne ne pouvait donc jamais être vraie, peu importe ce que les contacts faisaient.

Résultat : la branche "a ouvert" n'envoyait rien à personne, et la branche "n'a pas ouvert" ciblait uniquement les non-ouvreurs. Avec un taux d'ouverture de 37 % sur le premier mail, j'excluais d'emblée plus d'un tiers des contacts de toute relance, et le reste recevait un message pensé pour des gens qui n'avaient jamais vu le premier.

La capture d'écran du tableau de bord du 2 août 2026 est sans appel : 104 contacts entrés dans le workflow, **0** ayant atteint le second email.

## Un workflow dupliqué qui garde ses vieilles références

L'autre piège, découvert lors d'une vague suivante : quand tu dupliques un workflow dans Brevo, les conditions de branchement conservent les références de l'original. La liste d'appartenance, la campagne ciblée, tout reste figé sur l'ancienne version. Si tu ne vas pas vérifier chaque nœud manuellement, tu lances ce qui ressemble à un workflow neuf mais qui se comporte comme l'ancien.

C'est ce qui a fait qu'une vague entière a été routée en branche "non ouvert", alors que les contacts ouvraient bien. La condition comparait à la mauvaise liste.

(Ce type d'erreur ne s'applique qu'à des workflows avec plusieurs branches conditionnelles. Une séquence linéaire simple ne pose pas ce problème.)

## La correction

Après ces 2 incidents, j'ai abandonné la logique conditionnelle pour passer à une séquence linéaire : email 1, délai fixe, email 2, délai fixe, email 3. Pas de branche, pas de condition. Moins élégant sur le papier, mais le comportement est prévisible et vérifiable.

La règle que j'applique maintenant avant de lancer une vague : vérifier dans l'onglet "Activité" du workflow combien de contacts ont atteint chaque étape, pas seulement combien sont entrés dans le scenario. Si le compte tombe à 0 entre l'étape 1 et l'étape 2, il y a un problème avant d'avoir envoyé quoi que ce soit à une vraie cible.

Brevo ne ment pas exactement. Il fait exactement ce qu'on lui demande. Le problème, c'est qu'il ne vérifie pas si ce qu'on lui demande a du sens.