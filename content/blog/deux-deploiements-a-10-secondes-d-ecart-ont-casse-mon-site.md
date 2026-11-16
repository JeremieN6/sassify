---
title: "Deux déploiements à 10 secondes d'écart ont cassé mon site"
description: "Deux déploiements lancés à 42 secondes d'écart ont mis mon site hors ligne pendant 30 minutes. La leçon n'est pas de déployer moins souvent."
slug: deux-deploiements-a-10-secondes-d-ecart-ont-casse-mon-site
date: 2026-11-16
tag: Coulisses techniques
pilier: coulisses-techniques
type: specifique
hookVideo: "C'est quoi la panne la plus absurde que t'as eue récemment ?"
statut: publie
---

Tester en local ne suffit pas. Ce qui casse en production, c'est rarement le code : c'est la séquence dans laquelle les opérations s'exécutent sur le serveur.

## Ce qui s'est passé

Le 30 septembre, je travaillais sur Skinalyze. J'avais 2 chantiers en cours en même temps et j'ai lancé 2 déploiements à 42 secondes d'intervalle depuis ma machine. Rien d'inhabituel en apparence : chaque déploiement compile le projet, copie les fichiers sur le VPS, redémarre l'application.

Sauf que quand le second déploiement a commencé à copier ses fichiers, le premier était encore en train d'installer ses dépendances. Les deux processus ont écrit dans le même répertoire `node_modules` en même temps. Le résultat : un dossier de dépendances à moitié du premier build, à moitié du second, cohérent avec aucun des deux. L'application a planté au redémarrage. Nginx renvoyait des 502 à tous les visiteurs.

La panne a duré environ 30 minutes, le temps de diagnostiquer, de réinstaller proprement les dépendances et de redémarrer. Ce n'est pas le genre d'erreur que les logs signalent clairement : ils disent juste que l'application ne répond plus.

Ce qui rend ça particulièrement mauvais : ces 30 minutes tombaient exactement pendant l'envoi des premiers mails des campagnes Brevo de 2 vagues de prospection. Les premiers clics sur les liens de ces mails, entre 16h19 et 16h39, ont atterri sur une page d'erreur. Probablement en grande majorité des scanners automatiques de messagerie, mais un humain qui clique dans ce créneau a vu un 502.

Seule vraie limite de cette situation : avec un seul VPS, sérialiser les déploiements au niveau de l'infrastructure demande un outillage que je n'ai pas mis en place. Pour l'instant, c'est une règle manuelle.

## Ce que j'ai changé

La correction est simple : les déploiements sont maintenant sérialisés. Le second attend que le premier soit terminé avant de commencer. Ce n'est pas sophistiqué, et ce n'est pas automatique côté infrastructure : c'est une discipline d'exécution, renforcée par un verrou dans le pipeline.

La vraie leçon n'est pas de déployer moins souvent. C'est d'accepter que 2 opérations qui semblent indépendantes peuvent se marcher dessus dès qu'elles touchent les mêmes fichiers en même temps. Ce type de collision n'apparaît pas dans les tests locaux parce qu'on ne lance jamais 2 builds simultanément sur sa machine. C'est un comportement qui n'existe qu'en conditions réelles, avec plusieurs processus actifs sur le même serveur.

Déployer souvent reste la bonne pratique. Déployer en parallèle sans verrou, non.