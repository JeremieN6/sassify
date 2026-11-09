---
title: "Un article publié sans mon accord : ce que ça m'a appris sur mon propre pipeline"
description: "J'avais posé une règle non négociable : rien ne se publie sans ma validation. Et pourtant, le pipeline a publié sans moi."
slug: un-article-publie-sans-mon-accord-ce-que-ca-m-a-appris-sur-mon-propre-pipeline
date: 2026-11-09
tag: Coulisses techniques
pilier: coulisses-techniques
type: specifique
hookVideo: "C'est quoi le bug le plus bête que t'as trouvé dans ton propre système ?"
statut: brouillon
---

La règle fondatrice de Plotline, celle sur laquelle tout le reste repose, c'est qu'aucun contenu ne part en ligne sans une validation humaine explicite. Pas une validation "par défaut", pas un silence qui vaut accord : un clic délibéré, à chaque fois. Sur ce point, le produit n'a jamais transigé, même quand ça ralentissait le développement.

Et pourtant, un contenu est parti sans mon accord.

## Ce qui s'est passé

Le pipeline complet fonctionne comme ça : plan éditorial en texte, révision avant production, génération, puis studio de validation avant publication programmée. À aucun moment je ne cherche à court-circuiter cette étape. Je l'ai conçue précisément parce que je veux garder le dernier mot.

Le bug n'était pas dans la logique de validation elle-même. Il était dans la gestion de fin d'abonnement d'un fournisseur d'IA : un modèle retiré du jour au lendemain, sans préavis. Le pipeline, lui, continuait à considérer la génération comme terminée (parce qu'il avait reçu une réponse, même cassée), ce qui permettait au contenu de passer à l'étape suivante, celle du planning de publication, sans que j'aie vu quoi que ce soit de valide.

La validation humaine était bien là dans l'interface. Sauf que ce que je validais n'était pas ce qui partait vraiment en ligne.

## Ce que ça a changé

1er correctif : tracer le nom du modèle utilisé dans chaque génération, pour qu'une panne de fournisseur soit diagnostiquée en 30 secondes plutôt qu'en cherchant d'où vient l'anomalie.

2e correctif : un modèle disparu a maintenant sa propre catégorie d'erreur, distincte d'un timeout ou d'un refus de contenu. Ce n'est pas le même problème, ça ne mérite pas le même message.

La règle de validation, elle, n'a pas bougé d'un millimètre. Ce que j'ai appris, c'est qu'une règle métier sans traçabilité sur la chaîne qui la précède est une règle qui peut être contournée accidentellement. Le cas qui m'avait rendu vulnérable ici (une dépendance externe qui disparaît silencieusement) peut se reproduire avec n'importe quel fournisseur d'IA : modèle déprécié, quota épuisé sans alerte, réponse vide acceptée comme valide.

Cette limite tient toujours : je n'ai pas encore eu de génération réussie confirmée depuis le correctif. Tant que ce premier passage complet n'est pas vérifié, l'incident n'est pas vraiment clos.

Mettre une règle non négociable dans son système ne suffit pas. Il faut aussi rendre visible tout ce qui peut la contourner sans la violer.