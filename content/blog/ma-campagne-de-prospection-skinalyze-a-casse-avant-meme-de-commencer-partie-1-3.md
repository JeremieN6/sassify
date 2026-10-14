---
title: "Ma campagne de prospection Skinalyze a cassé avant même de commencer — partie 1/3"
description: "Avant même qu'un institut lise mon email, j'avais déjà raté la prospection — et je ne le savais pas encore."
slug: ma-campagne-de-prospection-skinalyze-a-casse-avant-meme-de-commencer-partie-1-3
date: 2026-10-12
tag: Coulisses techniques
pilier: coulisses-techniques
type: specifique
hookVideo: "C'est quoi la toute première chose qui a fait rater ta prospection Skinalyze ?"
statut: publie
---

Quand j'ai lancé la première vague de prospection pour Skinalyze, j'avais fait le travail visible : une liste de leads qualifiés, un message rédigé, une automatisation Brevo configurée. Ce que je n'avais pas fait, c'est vérifier que les emails arrivaient quelque part.

## Le problème qu'on ne voit pas

La première vague ciblait un seul département. 22 leads. Un taux d'ouverture autour de 11 %. À l'époque, j'ai cherché le problème du côté du message : est-ce que l'objet était bon ? Est-ce que l'angle choisi parlait à la cible ?

Ce n'était pas ça.

SPF, DKIM, DMARC — les enregistrements d'authentification du sous-domaine d'envoi n'étaient pas configurés au moment du lancement. En clair : les emails n'arrivaient pas en boîte de réception. La quasi-totalité atterrissait ailleurs, sans que rien dans l'interface Brevo ne le signale de façon évidente. Un taux d'ouverture de 11 % sur une vague de prospection à froid peut sembler faible mais acceptable. En réalité, c'était le plafond de ce qui passait malgré tout les filtres.

Résultat : j'ai passé du temps à analyser un message que presque personne n'avait vu.

## La faute de frappe dans l'adresse expéditeur

Pendant que je cherchais à comprendre ce qui clochait, j'ai découvert autre chose. L'adresse de l'expéditeur contenait une faute de frappe — deux lettres transposées. Elle était là depuis le lancement, invisible à l'œil nu dans l'interface Brevo. Je l'ai trouvée en inspectant l'en-tête brut d'un email reçu, pas en regardant les paramètres de la campagne.

Ce genre d'erreur ne casse pas forcément la délivrabilité à elle seule. Mais elle contribue à dégrader la confiance des filtres anti-spam, et elle est difficile à trouver parce qu'elle ne génère aucune erreur visible nulle part.

## Les prenoms d'un autre projet

Il y a un troisième problème de cette phase initiale, celui-là plus difficile à anticiper.

Brevo fonctionne avec une base de contacts unique par compte. Quand on importe une liste pour un projet, les colonnes viennent enrichir ou écraser ce qui existe déjà dans la base globale. J'avais deux projets dans le même compte Brevo, avec dans les deux cas une colonne "prénom".

Le second import a silencieusement écrasé les prénoms du premier. Des emails de prospection Skinalyze, adressés à des gérantes d'instituts, sont partis avec en guise de prénom le nom d'une structure sans aucun rapport avec la beauté.

Ce n'est pas le type d'erreur qui déclenche une alerte. Elle ne produit pas de rebond, pas de message d'erreur. Elle passe, et elle fait juste une impression catastrophique si quelqu'un lit l'email.

## Ce que ça a appris

Avant même de se poser la question de la pertinence du message, du bon angle ou du bon moment d'envoi, il y a un pré-requis simple : s'assurer que l'email arrive, qu'il vient de qui il prétend venir, et qu'il s'adresse à la bonne personne avec les bonnes informations.

Ce n'est pas du tout spectaculaire comme problème. C'est aussi pour ça que ça prend du temps à trouver.

La partie 2 couvre ce qui s'est cassé côté automatisations Brevo — une série de pièges différents, plus subtils, et qui ont duré bien plus longtemps.