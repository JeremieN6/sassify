---
    title: "19 interviews plus tard, j'ai compris que je vendais le mauvais problème"
    slug: 19-interviews-plus-tard-j-ai-compris-que-je-vendais-le-mauvais-probleme
    date: 2026-08-27
    pilier: decisions
    type: specifique
    hookVideo: "C'est quoi la pire erreur que t'as faite sur FlySmart ?"
    statut: brouillon
    ---

    Il y a une chose que j'aurais pu éviter si j'avais interviewé des utilisateurs avant de coder. Ou même avant de définir le produit.

FlySmart est parti d'une intuition simple : les prix des billets d'avion varient beaucoup, et personne ne sait vraiment quand acheter. J'ai codé sur cette intuition. J'ai construit une landing page sur cette intuition. Et pendant un bon moment, j'ai pensé que le problème que je résolvais, c'était l'accès à l'information — donner aux gens des données sur l'historique des prix pour qu'ils prennent de meilleures décisions.

Sauf que ce n'était pas le vrai problème.

## Ce que les interviews ont changé

J'ai fait 19 entretiens avec des office managers et coordinateurs logistiques qui gèrent les déplacements professionnels de leur PME. Pas des directeurs achats dans des grands groupes avec un outil dédié — des gens qui gèrent les vols en plus de tout le reste, sans expertise voyage particulière.

Ce que j'attendais d'entendre : "je manque de données sur les prix". Ce que j'ai entendu : "le temps que tout le monde valide en interne, le prix a déjà changé".

Ce n'est pas la même chose.

La cible n'a pas un problème d'accès à l'information. Elle a accès à Google Flights, à Kayak, à des dizaines d'outils de comparaison. Le problème, c'est le jugement — savoir si le prix qu'elle voit là, maintenant, est un bon prix ou si ça va baisser dans dix jours. Et ce problème est rendu encore plus complexe par une friction organisationnelle réelle : quand la validation interne prend du temps, la fenêtre de bon prix se ferme.

Autrement dit : le problème n'est pas "je ne sais pas où chercher". C'est "je ne sais pas si c'est le bon moment d'acheter, et je dois convaincre trois personnes avant de pouvoir le faire".

## Ce que ça a changé concrètement

Première conséquence sur le produit : la promesse centrale a été reformulée. FlySmart ne prétend plus fournir des données de marché exhaustives — il dit clairement que son rôle est d'indiquer si c'est le bon moment d'acheter, sans que l'utilisateur soit expert du voyage.

Deuxième conséquence, plus structurante : j'ai réalisé que le court-courrier était le mauvais terrain. Quand l'écart entre "acheter maintenant" et "attendre" représente 17 euros sur un billet Paris-Marseille, personne ne va payer un abonnement pour ce service. L'euro économisé doit justifier l'abonnement. Sur du long-courrier — Paris-New York, Paris-Montréal — les premiers relevés réels montrent des écarts qui commencent à avoir du sens pour une PME.

Troisième conséquence : les chiffres génériques affichés sur la landing n'avaient aucune source vérifiable. Ils ont été retirés, pas remplacés par d'autres estimations. L'historique des prix que le produit promet, il faut le construire jour après jour — aucune API du marché ne le fournit. C'est en cours depuis mi-août.

## Ce que ça m'a appris sur la méthode

J'aurais pu construire FlySmart différemment si j'avais posé la bonne question dès le départ. Pas "est-ce que tu voudrais savoir quand acheter tes billets ?" — tout le monde dit oui à ça. Mais "quel est le moment exact où tu te retrouves bloqué dans le processus de réservation ?"

La réponse à cette deuxième question, c'est la validation interne qui prend trop de temps pendant que les prix bougent. C'est un problème concret, pas une intuition abstraite sur la volatilité des tarifs aériens.

Le produit pointe maintenant vers ce problème-là. Reste à vérifier que la solution y répond vraiment — et ça, seules les premières semaines de données réelles le diront.