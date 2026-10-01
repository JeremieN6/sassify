---
title: "Mon premier client payant, toutes activités SaaS confondues"
description: "Neuf mois à construire, un abonnement Stripe actif — et ce n'était pas le produit sur lequel je bossais le plus."
slug: mon-premier-client-payant-toutes-activites-saas-confondues
date: 2026-10-01
tag: chiffres
pilier: chiffres
type: specifique
hookVideo: "C'était comment ton tout premier client payant en SaaS ?"
statut: brouillon
---

## Ce que ça fait, concrètement

Le 30 septembre 2026, Tifo a converti son premier client payant. Plan Pro, 9 euros par mois, abonnement actif côté Stripe. Vérifié.

C'est peu sur le papier. C'est beaucoup dans ma tête.

Pas parce que 9 euros change quoi que ce soit à ma situation financière. Mais parce que ça valide quelque chose que je n'avais pas encore : la preuve qu'un inconnu peut trouver un de mes produits, juger que ça vaut quelque chose, et sortir sa carte.

## Le canal qui m'a surpris

Depuis plusieurs semaines, je faisais du cold DM Instagram vers des clubs amateurs. C'était mon canal actif, celui sur lequel je passais du temps. Logiquement, j'aurais dû en parler comme de la source du premier client.

Sauf que ce n'est pas de là qu'il vient.

En croisant les données analytics, j'ai trouvé quelque chose d'inattendu : inscription sans referrer HTTP classique, un paramètre `utm_source=chatgpt.com`, depuis une app mobile iOS. Ce profil technique n'est pas compatible avec un DM Instagram — qui laisserait une trace différente dans les logs.

Le client n'a pas été démarché. Il a trouvé Tifo via un lien partagé ou recommandé dans une conversation ChatGPT, probablement depuis l'application mobile.

Je n'ai rien fait pour ça. Je n'avais aucune stratégie de visibilité dans les réponses des assistants IA. Ça s'est passé sans moi, et je l'aurais manqué si je n'avais pas pris le temps de vérifier l'attribution réelle plutôt que de supposer.

Un seul cas ne prouve rien. Mais ça ouvre une question que je n'avais pas encore posée sérieusement : est-ce que la visibilité dans les LLM mérite autant d'attention que le SEO classique, voire davantage ?

## La session de sa première utilisation

Ce que j'ai découvert ensuite m'a mis mal à l'aise.

En regardant les logs de sa première session, j'ai vu que la quasi-totalité de ses tentatives de génération avaient échoué. Erreur réseau côté navigateur, répétée. Il a quand même payé entre-temps.

La cause : un timeout nginx par défaut insuffisant pour le temps de traitement réel, puis une limite de taille de requête trop basse pour les formulaires avec plusieurs images. Deux paramètres de configuration que je n'avais pas testés en conditions réelles avant le lancement.

Corrigés le jour même. Client prévenu par email, sans détail technique.

Le lendemain matin, il est revenu consulter son compte.

Ce qui me reste de ça : quelqu'un suffisamment convaincu peut aller jusqu'au paiement même quand le produit ne marche presque pas devant lui. Ce n'est pas une raison de se satisfaire d'un produit cassé — c'est un rappel que les utilisateurs moins déterminés, eux, abandonnent silencieusement au même endroit, sans jamais remonter dans les métriques.

## Ce que je retiens

Tifo n'était pas le projet sur lequel je passais le plus de temps ces dernières semaines. C'est lui qui a converti en premier.

Le canal d'acquisition que je travaillais activement n'est pas celui qui a produit le premier client. C'est un canal auquel je n'avais pas pensé.

Et le produit qui a converti avait un bug majeur que je n'avais pas détecté avant qu'un vrai utilisateur l'expose.

Rien ne s'est passé comme prévu. C'est quand même un abonnement Stripe actif.