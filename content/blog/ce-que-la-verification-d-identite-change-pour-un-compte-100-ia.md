---
title: "Ce que la vérification d'identité change pour un compte 100% IA"
description: "Sur Plotline, j'ai rapidement compris que la cohérence visuelle d'une influenceuse IA ne dépend pas que de la qualité des images — elle dépend de ce qu'on vérifie avant de les générer."
slug: ce-que-la-verification-d-identite-change-pour-un-compte-100-ia
date: 2026-09-07
tag: Coulisses techniques
pilier: coulisses-techniques
type: general
hookVideo: "Pourquoi un compte IA peut se faire bloquer, même honnête ?"
statut: brouillon
---

## Le vrai problème avec les identités IA : pas la génération, la cohérence

Quand on parle d'influenceurs IA, le débat tourne souvent autour de la qualité des images. Est-ce que le visage est réaliste ? Est-ce que le style est cohérent ?

C'est une question pertinente, mais ce n'est pas celle qui m'a posé le plus de problèmes sur Plotline.

La vraie question, c'est : **comment vérifier que ce que tu publies correspond vraiment à qui tu as dit être** — avant de publier.

---

## Ce qu'on sous-estime au départ

Un compte IA, contrairement à un compte humain, n'a pas de mémoire naturelle. Une créatrice humaine qui publie tous les jours reproduit inconsciemment ses habitudes : même angle de visage, même registre de langue, même façon de cadrer une tenue.

Un pipeline de génération, lui, repart de zéro à chaque publication si on ne le contraint pas. Les premiers tests sur Plotline l'ont montré très vite : sans verrouillage explicite des caractéristiques d'une identité, deux publications de la même "ambassadrice" à une semaine d'intervalle donnaient des résultats visuellement incohérents. Pas catastrophiques. Juste suffisamment différents pour casser la crédibilité du compte.

Et un compte peu crédible, même honnête sur sa nature IA, se fait filtrer — par l'algorithme ou par les abonnés.

---

## La décision prise sur Plotline

La règle qui s'est imposée assez naturellement : **aucun contenu ne part en génération sans que l'identité de l'ambassadrice soit validée à cette étape**.

Pas seulement "qui est-ce qu'on génère", mais "est-ce que ce qu'on va produire est cohérent avec ce qui a déjà été publié sous ce nom". C'est ça, la vérification d'identité dans ce contexte. Moins un contrôle de sécurité qu'une contrainte éditoriale : est-ce qu'on reste dans le personnage ?

Concrètement, ça a conduit à intercaler une étape de revue entre la proposition de contenu et la génération réelle. L'idée est soumise d'abord en texte seul, et la production (image, vidéo) ne démarre que si l'idée est approuvée. Ce n'était pas prévu comme ça à l'origine, mais ça a une conséquence directe sur les coûts : une erreur de direction éditoriale coûte une requête de texte, pas une série de vidéos.

---

## Ce que ça change en pratique

Un compte 100% IA sans vérification d'identité structurée, c'est un compte qui dérive. La promesse faite aux abonnés — "c'est toujours la même personne, avec le même univers" — tient à un fil. Et ce fil n'est pas la qualité technique des images, c'est la rigueur du process avant génération.

La validation humaine obligatoire, que Plotline applique sans exception avant chaque publication, sert exactement ça : pas à surveiller l'IA par méfiance, mais à maintenir la cohérence que l'IA seule ne garantit pas encore naturellement.

Ce n'est pas une limitation temporaire en attendant que les modèles s'améliorent. C'est une décision assumée : la continuité d'une identité IA, pour l'instant, passe par un œil humain à chaque étape.

---

L'outil n'est pas encore ouvert à d'autres utilisateurs — tout ce que je décris vient de mon propre usage sur mes propres comptes. Mais c'est justement parce que je suis l'unique utilisateur que ces décisions sont très concrètes : chaque choix d'architecture produit, je le ressens directement à la publication suivante.