> Généré automatiquement le 2026-08-27T15:29:13.441Z — ne jamais éditer ce fichier à la main

## flysmart

# STORY.md -- Memoire Narrative Business

> Ce fichier raconte le POURQUOI du projet : objectif, pivots, decisions
> business et ce que le terrain a appris. Il sert de matiere premiere aux
> articles de blog sassif.fr. Il ne contient pas de detail d'implementation
> -- ca, c'est le role de CLAUDE.md.
>
> A mettre a jour apres toute session impliquant une decision business, un
> pivot, un changement de statut ou un apprentissage terrain significatif.

---

## Objectif produit

FlySmart aide les PME francaises sans agence de voyage dediee a savoir
QUAND acheter leurs billets d'avion professionnels, plutot que de les
aider a trouver OU les acheter. La cible n'est pas un voyageur ni un
service achats structure : c'est l'office manager ou le coordinateur
logistique d'une PME de 50 a 400 salaries, qui gere les deplacements a
la main, en plus du reste de son travail, sans expertise voyage.

La promesse tient en une phrase : savoir si c'est le bon moment
d'acheter, sans etre expert du voyage.

---

## Statut actuel

La landing page a ete entierement retravaillee (structure, contenu,
honnetete des chiffres affiches) et presente desormais le produit dans
un ordre qui montre la valeur avant le prix. Le vrai moteur de donnees,
lui, en est a son tout premier jour de collecte reelle (12/08/2026) :
FlySmart promet une recommandation basee sur l'historique des prix, mais
cet historique n'existe pas encore -- aucune API du marche ne le fournit,
il doit etre construit jour apres jour. Les demonstrations produit sur
la landing sont donc, pour l'instant, assumees comme des maquettes
("valeurs d'illustration"), en attendant que la collecte produise assez
de profondeur pour les remplacer par des chiffres reels.

Premier jalon concret attendu : le 18/08/2026, quand les premiers
departs suivis passeront de J+15 a J+7, donnant la toute premiere mesure
d'un meme vol observe a deux moments d'achat differents.

---

## Historique des pivots

### 2026-08-11 -- Du widget B2B au SaaS direct PME
**Contexte** : le positionnement d'origine visait les agences de voyage,
comites d'entreprise et blogueurs voyage, via un widget integrable
remunere a la commission ("+1% commission"). Ce positionnement vivait
encore dans les metadonnees du site (title, description, keywords) et
dans plusieurs textes de la landing (mention de script a integrer, de
personnalisation aux couleurs du client, de compatibilite CMS).
**Decision** : abandon complet de ce positionnement au profit d'un SaaS
vendu directement aux PME qui gerent leurs propres deplacements.
**Resultat** : toute trace du widget/agences/CE/blogueurs retiree du
layout, de la page d'accueil, de la navigation, du footer et des textes
de demonstration. Le produit se presente maintenant comme un outil de
decision pour l'equipe qui reserve, pas comme une brique a integrer sur
le site d'un tiers.

### 2026-08-11 -- Du court-courrier au long-courrier
**Contexte** : la premiere liste de routes suivies couvrait dix
destinations, principalement du court et moyen-courrier au depart de
Paris (Marseille, Nice, Toulouse, Bordeaux, Barcelone, Milan, Amsterdam,
Londres, Francfort, New York). Une premiere mesure reelle a montre des
ecarts de prix impressionnants en pourcentage (+40 a +47%) mais
derisoires en valeur absolue : 17 a 27 euros d'ecart.
**Decision** : concentrer les trois routes actives (contrainte par le
quota de l'API de prix, voir plus bas) sur du long-courrier
(Paris-New York, Paris-Dubai, Paris-Montreal), sur l'hypothese que
l'ecart en euros y est bien plus significatif.
**Resultat** : premiere mesure long-courrier confirmant l'hypothese --
jusqu'a 289 euros d'ecart sur New York contre 46 euros sur Dubai (route
la plus plate mesuree). C'est l'euro economise qui justifie un
abonnement, pas le pourcentage affiche.

### 2026-08-11 -- Construire l'historique plutot que le supposer
**Contexte** : le produit promettait une "fourchette historique" et un
volume de "tarifs observes" -- des donnees qui n'ont jamais existe,
puisqu'aucune API de vol testee (Amadeus, FlightSky) n'expose
d'historique de prix reel.
**Decision** : mettre en place une collecte quotidienne automatisee
(un releve par route et par horizon d'achat, chaque jour) plutot que de
chercher une source externe qui n'existe pas.
**Resultat** : pipeline de collecte en production depuis le 12/08/2026.
L'historique du produit est desormais un actif construit en interne, pas
achete. Consequence directe : toute donnee affichee sur la landing qui
n'est pas encore mesuree a du etre retiree ou explicitement marquee
comme illustration, plutot qu'estimee.

---

## Ce que la cible attend / a appris

- **L'euro compte plus que le pourcentage.** Un "+47%" sur un aller-retour
  a 40 euros ne convainc personne de payer un abonnement ; un ecart de
  200 a 300 euros sur un vol long-courrier, si.
- **La cible n'a pas de solution aujourd'hui, elle a une habitude.**
  Les personnes interrogees ne comparent pas mal : elles n'ont simplement
  pas le temps de bien comparer, et ne savent pas si le prix va encore
  bouger. Le probleme n'est pas l'acces a l'information, c'est le
  jugement sur le bon moment.
- **La validation interne est un point de friction reel**, pas un detail
  de process : "le temps que tout le monde valide, le prix a deja
  change" revient comme une plainte recurrente, independamment de la
  taille de la PME.
- **Un chiffre affiche sans source ne convainc pas, il expose.** Les
  premieres versions de la landing affichaient des statistiques
  generiques (economies moyennes, nombre de routes couvertes, note de
  satisfaction) sans origine verifiable. Elles ont ete retirees plutot
  que remplacees par une autre estimation.

---

## Garde-fous de contenu

Ce fichier peut etre lu par n'importe quel agent ou session future, pas
seulement celle qui redige un article de blog. Il ne doit jamais
contenir :
- de detail exploitable d'un incident de securite (cle exposee,
  vulnerabilite, faille de permission) ;
- de mecanique interne precise donnant une feuille de route a un
  concurrent (algorithme de scoring, seuils de decision exacts) ;
- de chiffre financier precis non explicitement source ailleurs dans le
  projet (revenu, marge, cout d'acquisition) ;
- de ton condescendant envers la cible client.

---

## Dernière mise à jour

2026-08-27 -- Ajout de la section "Garde-fous de contenu" (defense en
profondeur : ces regles existaient deja au niveau du prompt de
generation d'article, elles sont desormais aussi portees par le fichier
source lui-meme). Contenu narratif inchange depuis le 2026-08-12.


---

## skinalyze

# STORY.md -- Memoire narrative -- Skinalyze

> Memoire business/narrative du projet, distincte de CLAUDE.md (memoire
> technique). Sert a raconter le projet (articles, retros) sans exposer de
> detail d'implementation. Mise a jour apres toute session avec une decision
> business, un pivot, un changement de statut, ou un apprentissage terrain --
> pas apres un changement purement technique.

---

## Objectif produit
Donner a chaque institut de beaute, spa ou marque cosmetique un outil de
diagnostic de peau par IA, restitue en 60 secondes selon trois grilles de
lecture (dermatologique, cosmetique, bien-etre), pour aider le professionnel
a mieux orienter et convertir ses clients en prestations et produits.

## Statut actuel
Produit B2B en ligne, en production, sur `skinalyze.sassify.fr`. Le parcours
complet fonctionne de bout en bout : formulaire d'acces pilote, creation de
compte, diagnostic IA, rapport PDF personnalisable au logo de l'institut. Le
projet est en phase d'acquisition et d'iteration continue sur la landing
page, la conversion et le suivi analytique -- pas encore en phase de
croissance a grande echelle.

## Historique des pivots

### Mai 2026 -- Bascule B2C vers B2B, refonte Next.js
**Contexte** : le produit existait auparavant dans une version orientee
grand public (B2C), sur une stack differente.
**Decision** : archiver l'ancienne branche principale B2C
(`archive/main-v1-b2c-20260523`) et faire de la refonte Next.js 16, pensee
pour un usage professionnel (instituts, spas, marques), la nouvelle base du
produit.
**Resultat** : le positionnement B2B (aide a la vente pour le professionnel,
pas diagnostic pour le grand public) est celui qui est en production
aujourd'hui.

> Note : l'historique du produit avant mai 2026 (raisons precises du pivot
> B2C -> B2B, apprentissages qui l'ont motive) n'est pas documente ici faute
> de source fiable au moment de la redaction. A completer si cette
> information redevient pertinente.

## Ce que la cible attend / a appris
- Les instituts veulent un outil sans friction : pas d'installation, pas de
  compte a faire creer a leurs propres clients, un lien suffit.
- La confidentialite des photos clients est une objection potentielle prise
  au serieux des la conception : analyse en temps reel puis suppression
  immediate, aucune image conservee.
- La personnalisation (logo, marque de l'institut sur le rapport) compte
  pour les offres superieures (Pro/Business) -- ce n'est pas qu'un outil
  d'analyse, c'est aussi un support de vente a l'image de l'institut.
- Le formulaire d'acces a ete volontairement simplifie (3 champs) apres
  constat qu'aucune contrainte reelle (type d'adresse email) ne justifiait
  la friction initiale.

## Garde-fous de contenu
A respecter par quiconque redige un contenu externe (article, post, etude de
cas) a partir de ce fichier ou du reste du projet :
- Ne jamais detailler publiquement un incident de securite/infrastructure de
  maniere exploitable (ex : configuration serveur precise, mecanisme exact
  d'une panne passee). Un incident peut etre raconte en termes de lecon
  business ("on a appris a isoler nos services"), jamais avec assez de detail
  technique pour guider une attaque ou reveler la topologie d'hebergement.
- Ne jamais reveler de mecanique interne donnant une feuille de route
  exploitable a un concurrent (roadmap precise, architecture detaillee du
  moteur de diagnostic, cout d'infrastructure).
- Ne jamais citer un chiffre financier ou business precis (taux de
  conversion, nombre de clients, revenu) sans que sa source soit
  explicitement identifiee dans ce fichier ou marquee "a verifier".
- Ne jamais adopter un ton condescendant envers la cible (instituts de
  beaute, spas) -- ce sont des professionnels experts de leur metier, pas des
  utilisateurs a eduquer sur les bases.

## Derniere mise a jour
2026-08-27


---

## tifo

# STORY.md -- Memoire Narrative Projet

> Ce fichier raconte le projet pour un public exterieur (article de blog, retro, pitch).
> Il ne contient pas de detail d'implementation technique -- voir CLAUDE.md pour ca.

---

## Objectif produit
Tifo permet a un club, un supporter ou un media sportif de generer en quelques clics une affiche visuelle professionnelle pour annoncer un transfert, un mercato ou un evenement football, sans passer par un graphiste.

---

## Statut actuel
Le produit est fonctionnel de bout en bout : inscription, generation d'affiches avec logos de clubs auto-remplis, abonnement payant (Stripe), back-office admin pour gerer les comptes et les essais gratuits, et un blog automatise pour l'acquisition SEO. Le projet est en phase de lancement : la mise en production reelle, VPS, paiement, verification email est ok. Les premiers clubs ont été contacté sur Insta, le marketing a donc commencé via cold DM Insta. Quelques inscriptions ont déjà été enregistrés.

---

## Historique des pivots

### [2026-05-13] Fiabiliser l'identification visuelle des clubs
**Contexte** : les premieres versions de la recherche de club renvoyaient des logos et resultats absurdes (un club francais amateur pouvait se voir attribuer le logo d'Arsenal), rendant le produit peu credible des la premiere utilisation.
**Decision** : abandonner le fournisseur de donnees initial pour une source plus fiable (Wikidata), avec verification stricte de la correspondance de nom avant d'accepter un logo, et refus explicite d'afficher une image generique (comme une photo de stade) quand le vrai logo est introuvable.
**Resultat** : la fonctionnalite coeur du produit (habiller une affiche avec le bon logo) devient fiable, ce qui conditionnait directement la qualite percue du produit.

### [2026-05-26] Segmenter l'offre d'essai plutot que l'ouvrir a tous
**Contexte** : une offre commerciale de 90 jours d'essai gratuit sur le plan "Club" avait ete pensee pour cibler des clubs specifiques, mais l'implementation initiale l'activait automatiquement pour tout nouvel inscrit.
**Decision** : revenir a une inscription standard sur le plan de base, et reserver l'attribution de l'offre premium a une action manuelle du backoffice.
**Resultat** : evite de diluer une offre commerciale ciblee et de fausser les metriques d'acquisition/conversion des le lancement.

### [2026-05-26 -> 2026-08-27] Construire les fondations produit avant le lancement public
**Contexte** : entre fin mai et mi-aout, le projet est passe d'un prototype avec authentification et generation basique a un produit avec facturation complete, gestion des essais, back-office admin et acquisition SEO (blog automatise).
**Decision** : prioriser la solidite operationnelle (facturation, gestion des utilisateurs, contenu SEO recurrent) avant l'ouverture publique, plutot que d'ajouter de nouvelles fonctionnalites de generation.
**Resultat** : le produit dispose maintenant des briques necessaires a une exploitation reelle (facturation, support client via l'admin, acquisition organique), mais n'a pas encore ete teste en conditions reelles de paiement ni deploye en production.

---

## Ce que la cible attend / a appris
- Un supporter ou un club veut un visuel credible immediatement reconnaissable (bon logo, bonne mise en page) : la fiabilite du logo s'est averee etre un prerequis de confiance, pas un detail cosmetique.
- Une offre commerciale segmentee (essai gratuit cible) doit rester pilotee manuellement tant que le produit n'a pas de mecanisme d'eligibilite automatique fiable.

---

## Garde-fous de contenu
- Ne jamais publier de detail technique exploitable (architecture interne, cles/API, mecanique anti-abus) dans un article externe base sur ce fichier.
- Ne jamais citer de chiffre business precis (taux de conversion, revenu, nombre d'utilisateurs) sans indiquer sa source ou le marquer explicitement "a verifier" -- aucun chiffre de ce type n'est encore verifie a la date de derniere mise a jour.
- Ne pas adopter un ton condescendant envers les clubs ou supporters cibles ; parler de leurs besoins reels (credibilite visuelle, simplicite) plutot que de mecaniques internes.
- Ce projet n'est pas encore lance publiquement : ne pas presenter de metriques d'usage ou de temoignages clients comme s'ils existaient.

---

## Derniere mise a jour
2026-08-27 -- Creation initiale du fichier, reconstitue a partir de l'historique git (commits du 2026-05-13 au 2026-07-16), de CLAUDE.md et de tasks/todo.md.


---

## plotline

# STORY.md -- Memoire narrative de Plotline

> Memoire business et narrative du projet, complementaire de CLAUDE.md.
> CLAUDE.md repond a "qu'est-ce qu'un agent qui code doit savoir".
> Ce fichier repond a "pourquoi ce projet a pris cette forme".
>
> Detail d'implementation -> CLAUDE.md. Raison business, pivot,
> apprentissage terrain -> ici.

---

## Objectif produit

Permettre a un createur d'influenceurs virtuels de faire tourner plusieurs
identites IA credibles sur Instagram et TikTok, sans que la coherence visuelle
se degrade au fil des publications et sans jamais publier quoi que ce soit sans
validation humaine pour le moment. A terme, une fois que plusieurs publications seront validées, celle ci pourra devenir automatique.

---

## Statut actuel

**Phase** : produit fonctionnel, en production sur plotline.sassify.fr, utilise
par son auteur sur ses propres marques et influenceuses.

Le pipeline complet est operationnel de bout en bout : planification editoriale,
generation d'images et de videos, revue, puis publication programmee sur
Instagram et X.

**Pas d'utilisateur externe a ce jour.** Tout le retour d'usage vient de l'auteur
utilisant l'outil pour ses propres comptes. C'est une information structurante :
chaque decision produit prise jusqu'ici repose sur un seul utilisateur reel, tres
au fait du fonctionnement interne.

---

## Historique des pivots

### 2026-05-26 -- La validation devait passer par Telegram

**Contexte** : le cadrage initial prevoyait un bot Telegram pour valider chaque
contenu avant publication, afin de pouvoir approuver depuis son telephone sans
ouvrir l'outil.

**Decision** : inscrire la validation humaine comme regle non negociable du
produit, quel qu'en soit le canal.

**Resultat** : la regle a tenu, le canal non. Voir le pivot suivant.

### 2026-08-10 -- Telegram abandonne au profit d'une validation dans l'outil

**Contexte** : le flux PENDING vers Valider vers Publier, construit dans
l'application pour d'autres raisons, remplissait deja exactement le role prevu
pour Telegram.

**Decision** : abandonner le bot Telegram. Aucune ligne n'avait ete ecrite.

**Resultat** : une dependance externe et un canal de moins a maintenir, sans rien
perdre du controle editorial. La regle fondatrice, jamais de publication sans
validation humaine explicite, reste intacte.

### 2026-08-10 -- Une ambassadrice peut representer plusieurs marques

**Contexte** : le modele initial liait chaque ambassadrice a une seule marque. En
pratique, il devenait impossible de reutiliser une influenceuse existante pour
une seconde marque du meme compte : il fallait la recreer a l'identique.

**Decision** : passer le lien en plusieurs-a-plusieurs, tout en interdisant
explicitement de relier deux profils appartenant a des comptes differents.

**Resultat** : une ambassadrice devient un actif reutilisable, ce qui correspond a
la realite du metier ou une influenceuse travaille couramment avec plusieurs
marques. Le cloisonnement entre comptes reste, lui, une frontiere de securite.

### 2026-08-10 -- Le plan editorial est relu avant toute generation

**Contexte** : le produit devait pouvoir generer des batchs de contenus. Generer
d'abord et trier ensuite engageait des credits d'image et de video avant que
l'auteur ait vu la moindre idee.

**Decision** : inserer une etape de revue en texte seul entre la proposition et
la production. L'IA redige les idees, l'auteur ajuste ou ecarte, et seules les
idees retenues partent en generation.

**Resultat** : le cout d'une erreur editoriale passe d'une serie de videos a une
requete de texte. Cette contrainte economique a faconne l'interface : le bouton
d'approbation annonce explicitement le nombre de contenus qui vont etre produits.

### 2026-08-10 -- La publication automatique TikTok est ecartee

**Contexte** : TikTok figurait comme cible de publication depuis le cadrage
initial, au meme titre qu'Instagram.

**Decision** : ne pas implementer la publication par API.

**Resultat** : d'apres l'observation de l'auteur, TikTok favorise les comptes qui
publient depuis son application mobile ; passer par l'API serait donc
contre-productif pour la portee des contenus. TikTok reste une cible en tant que
format vertical, mais la publication y demeure manuelle et assumee comme telle.

### 2026-08-10 -- Le troisieme fournisseur video est mis hors service

**Contexte** : trois fournisseurs de generation video etaient integres pour
diversifier les rendus.

**Decision** : mettre le troisieme hors service derriere un interrupteur, en
conservant le code.

**Resultat** : ce fournisseur fonctionne sur des credits prepayes, tombes a zero,
et aucune generation n'avait jamais abouti de bout en bout. Deux fournisseurs
couvrent les besoins. Le modele prepaye est identifie comme un risque : il
s'epuise sans preavis et transforme une fonctionnalite en panne silencieuse.

### 2026-08-14 -- Inscription et connexion par Google

**Contexte** : l'inscription passait uniquement par email et mot de passe, avec
verification d'adresse. Objectif affiche : fluidifier l'entree dans le produit
avant toute ouverture a des utilisateurs externes.

**Decision** : ajouter Google, en ecrivant l'echange a la main plutot qu'avec un
module tout fait, et ne rattacher automatiquement un compte existant que si
Google atteste que l'adresse est verifiee.

**Resultat** : l'inscription tient en un clic et l'email de verification
disparait, puisque Google l'a deja faite. La demarche a ete documentee comme
recette reutilisable, le meme besoin existant sur les autres projets de l'auteur.

---

## Ce que la cible attend / a appris

**Cible** : createurs et petites agences qui font vivre une ou plusieurs
identites IA sur les reseaux, et marques qui veulent une ambassadrice virtuelle
sans production photo.

**Ce que l'usage reel a montre jusqu'ici** (source : usage par l'auteur, aucune
etude externe) :

- La coherence du visage entre publications est le critere qui fait ou defait la
  credibilite d'une identite IA. C'est ce qui justifie tout l'appareillage de
  verrouillage d'identite, largement plus lourd qu'une simple generation d'image.
- Le cout de generation est une contrainte de conception, pas un detail
  d'exploitation. Il a dicte la revue avant production, et la conservation du
  rendu precedent en cas d'echec.
- Un compte de type agence se comporte differemment d'un compte de marque :
  beaucoup d'influenceuses et peu de marques d'un cote, l'inverse de l'autre.
  L'interface doit servir les deux sans imposer le vocabulaire de l'un a l'autre.

**Ce qui reste inconnu et devra etre valide aupres de vrais utilisateurs** :
tarification, volume de publication reellement souhaite, appetit pour la
publication automatique par rapport a un export manuel, et interet du
planificateur editorial pour quelqu'un qui n'a pas construit l'outil.

---

## Garde-fous de contenu

Ce fichier vit dans le depot et peut etre lu par n'importe quelle session ou
agent futur, pas seulement lors de la redaction d'un article. Les regles
suivantes s'appliquent a tout contenu produit a partir de ce fichier :

- **Aucun detail exploitable d'incident de securite.** Les decisions de securite
  peuvent etre citees dans leur principe, jamais la maniere de les contourner.
- **Aucune mecanique interne donnant une feuille de route a un concurrent** :
  pas de detail sur la construction des prompts, sur la chaine de verrouillage
  d'identite, ni sur le choix precis des fournisseurs et de leurs reglages.
- **Aucun chiffre financier ou metrique non source.** Ce projet n'a a ce jour ni
  revenu, ni utilisateur externe, ni mesure d'usage. Tout chiffre apparaissant
  dans un contenu doit pouvoir etre rattache a une source dans le depot, sinon
  il ne doit pas etre ecrit.
- **Aucun ton condescendant envers la cible.** Les createurs d'influenceurs IA
  sont un public souvent moque ; le produit ne se raconte pas a leurs depens.
- **Aucune donnee personnelle reelle** : pas d'adresse email, pas de nom de
  compte client, pas de capture montrant des identifiants.

---

## Derniere mise a jour

2026-08-27 -- Creation du fichier.

Constat de depart : CLAUDE.md etait a jour au 2026-08-14, mais annoncait comme
prochaine etape le lien marque/ambassadrice en plusieurs-a-plusieurs, alors que
celui-ci etait livre depuis le 2026-08-10 et deja consigne dans le tableau des
decisions du meme fichier. Corrige a cette occasion.


---

## stellara

# STORY.md -- Memoire Narrative

> Ce fichier raconte le projet pour un public exterieur (article de blog,
> presentation, retro). Il ne remplace pas CLAUDE.md : aucun detail
> d'implementation ici, seulement le pourquoi et le contexte business.

---

## Objectif produit
Stellara aide les gens a comprendre leur theme astral grace a des rapports
personnalises generes par IA, avec un parcours gratuit (horoscope du jour,
lexique astro, blog) pensé pour amener vers un rapport payant et un
abonnement.

---

## Statut actuel
Le produit est en croissance active. Le socle technique (backend, paiement,
compte utilisateur) est stable et le produit s'etend maintenant sur
plusieurs fronts en parallele : contenu gratuit pour attirer du trafic
(horoscope du jour, lexique, blog), un canal d'affiliation pour recruter
des apporteurs d'affaires, et un suivi plus fin de l'origine des leads pour
savoir quels canaux marchent vraiment.

---

## Historique des pivots

### Migration vers une stack full JS (mai 2026)
**Contexte** : le backend reposait sur une stack separee du frontend,
compliquant la maintenance.
**Decision** : faire converger tout le backend vers Nuxt/Nitro, avec une
migration progressive plutot qu'une bascule brutale, pour ne pas casser la
production.
**Resultat** : le socle (comptes, abonnements, paiement) tourne desormais
sur cette base unifiee ; la migration continue domaine par domaine.

### Ouverture d'un canal d'affiliation (juin 2026)
**Contexte** : le produit avait besoin de canaux d'acquisition au-dela du
trafic direct.
**Decision** : construire un systeme d'affiliation complet (suivi des
clics, des ventes, remises pour l'acheteur, dashboard admin) plutot que de
passer par un outil tiers.
**Resultat** : ouvre la voie a des partenaires qui relaient Stellara contre
commission, avec un suivi interne complet des performances.

### Contenu gratuit comme porte d'entree (juin-juillet 2026)
**Contexte** : convertir un visiteur froid directement vers un rapport
payant est difficile.
**Decision** : investir dans du contenu gratuit a forte valeur perçue
(horoscope du jour quotidien, lexique astro, fiches detaillees par signe,
articles de blog evenementiels comme l'eclipse d'aout 2026), positionne
discretement dans le header/footer plutot qu'en avant-plan agressif.
**Resultat** : un flux de contenu regulier qui sert a la fois le SEO et la
familiarisation progressive avec la marque, avant de proposer l'abonnement.
Une piste encore a l'etude : un horoscope plus detaille reserve aux
abonnes payants.

### Fiabilisation du deploiement (13 juillet 2026)
**Contexte** : plusieurs incidents de deploiement le meme jour ont montre
que le pipeline n'etait pas assez robuste pour la frequence de mise a jour
du produit.
**Decision** : figer temporairement sur un runtime unique et ajouter des
verifications de sante avant de considerer un deploiement reussi, quitte a
revenir en arriere plusieurs fois dans la journee pour retrouver un etat
stable.
**Resultat** : un pipeline plus previsible, mais qui merite d'etre re-teste
en conditions reelles avant d'etre considere definitivement solide.

### Mesurer l'origine des leads (23 juillet 2026)
**Contexte** : avec plusieurs canaux actifs en parallele (blog, affilies,
horoscope), il devenait impossible de savoir lequel convertissait
reellement.
**Decision** : ajouter un suivi de la source d'acquisition sur chaque lead
et chaque rapport genere.
**Resultat** : permet desormais d'orienter les efforts (contenu, affiliation,
produit) en fonction de donnees plutot que d'intuition.

---

## Ce que la cible attend / a appris
- Les visiteurs reagissent bien a un point d'entree gratuit et discret
  (horoscope du jour) plutot qu'a une demande immediate de paiement.
- La friction sur la capture d'email doit rester minimale : un patch
  recent a d'ailleurs decouple l'envoi vers l'outil d'emailing du reste du
  parcours technique, precisement pour ne jamais perdre un contact a cause
  d'un probleme technique cote serveur.
- L'affiliation est encore jeune : pas encore de recul chiffre verifie sur
  sa contribution reelle au chiffre d'affaires (a mesurer via le tracking
  d'acquisition mis en place en juillet).

---

## Garde-fous de contenu
- Ne jamais publier de chiffre financier precis (revenus, taux de
  conversion, cout d'acquisition) sans indiquer sa source ou le marquer
  "a verifier" -- aucun chiffre de ce type n'est aujourd'hui source dans
  le projet.
- Ne pas detailler la mecanique interne de l'affiliation, du tracking
  d'acquisition ou du pipeline de deploiement de facon a fournir une
  feuille de route exploitable par un concurrent.
- Ne pas mentionner d'incident de production (comme les allers-retours du
  13 juillet 2026) avec des details techniques exploitables (noms de
  scripts, commandes) dans un contenu externe -- rester au niveau du
  "on a renforce la fiabilite du deploiement".
- Garder un ton respectueux envers la cible (personnes interessees par
  l'astrologie) : ne jamais laisser transparaitre un ton condescendant ou
  sceptique sur le sujet dans un contenu publie sous le nom de Stellara.

---

## Derniere mise a jour
2026-08-27 -- creation initiale du fichier, contenu reconstruit et
verifie a partir de l'historique git reel (voir CLAUDE.md pour le detail
technique correspondant).


---

## csvtoppt

# STORY.md — Mémoire narrative du projet

> Ce fichier raconte le projet pour un lecteur externe (article de blog, retour
> d'expérience). Il ne contient aucun détail d'implémentation — ceux-ci vivent
> dans `CLAUDE.md`.

---

## Objectif produit

Permettre à quiconque a un fichier CSV ou Excel de repartir, en quelques
minutes et sans compétence en data analyse, avec une présentation PowerPoint
qui raconte ce que dit son dataset (graphiques + texte d'interprétation).

## Statut actuel

Le produit est en ligne et fonctionnel : un utilisateur dépose un CSV/XLSX, le
service l'analyse, génère des graphiques et des textes d'interprétation, et
renvoie un .pptx téléchargeable. Le modèle économique Free/Pro (paiement
Stripe) est en place, avec des quotas différenciés selon le plan. Le projet est
actuellement dans une phase de fiabilisation (qualité des graphiques, richesse
des textes générés) plutôt que d'ajout de nouvelles fonctionnalités majeures.

## Historique des pivots

### 2026-01 — Mise en ligne initiale
**Contexte** : construction du pipeline de bout en bout (ingestion → analyse →
graphiques → slides) et de l'API qui l'expose.
**Décision** : livrer une v1 avec le parcours complet (upload, paiement Stripe,
auth) plutôt que d'itérer longtemps sans utilisateur réel.
**Résultat** : produit en ligne dès début janvier, avec facturation Stripe et
authentification opérationnelles dès le départ.

### 2026-04-16 — Fiabilité d'infrastructure (base de données gratuite)
**Contexte** : la base de données (hébergée sur une offre gratuite) se met en
veille en cas d'inactivité prolongée, ce qui aurait cassé le service côté
utilisateur sans prévenir.
**Décision** : mettre en place un ping automatique régulier plutôt que de
payer un plan supérieur à ce stade.
**Résultat** : disponibilité maintenue sans coût d'infrastructure additionnel.

### 2026-04-26 — De la génération de texte "générique" à l'interprétation
**Contexte** : les textes générés automatiquement pour chaque slide étaient
jugés trop pauvres, peu différenciants par rapport à un simple export de
graphiques.
**Décision** : enrichir la génération de texte pour qu'elle interprète
réellement les corrélations et tendances du dataset, et diversifier les
fournisseurs d'IA utilisés pour cette génération (au lieu d'un seul).
**Résultat** : rapports jugés "plus fournis, avec de l'interprétation" —
c'est le cœur de la proposition de valeur (ne pas juste tracer des graphiques,
mais expliquer ce qu'ils montrent).

### 2026-04-26 — Formalisation du modèle Free/Pro
**Contexte** : les règles de ce qui est permis en gratuit vs payant devaient
être appliquées de façon cohérente à la fois par l'API et par le moteur de
génération.
**Décision** : formaliser les limites (nombre de générations, taille de
dataset, nombre de slides, présence d'un filigrane) comme des règles métier
centrales plutôt que des vérifications éparpillées.
**Résultat** : modèle Free/Pro clair et cohérent, base pour toute évolution
future de la grille tarifaire.

## Ce que la cible attend / a appris

- La valeur perçue ne vient pas du graphique en lui-même mais de
  l'interprétation qui l'accompagne — d'où l'investissement continu sur la
  qualité des textes générés plutôt que sur le nombre de types de graphiques.
- La fiabilité perçue du service (disponibilité, cohérence des graphiques avec
  les données réellement croisées) a nécessité plusieurs itérations de
  correction après la mise en ligne initiale — un produit qui "marche en démo"
  ne suffit pas à garantir un usage réel sans accroc.

## Garde-fous de contenu

- Ne jamais publier de détail exploitable sur un incident de sécurité (auth,
  paiement, accès aux données utilisateurs) dans ce fichier.
- Ne pas détailler ici la mécanique interne de la pipeline (noms de modules,
  algorithmes de détection, prompts IA) — cela donne une feuille de route à un
  concurrent ; ce niveau de détail reste dans `CLAUDE.md`.
- Ne pas indiquer de chiffre financier précis (revenus, nombre d'utilisateurs
  payants, coûts d'infrastructure) sans source vérifiable identifiée dans le
  projet ; à défaut, marquer explicitement "à vérifier".
- Garder un ton factuel et respectueux envers les utilisateurs/clients cibles,
  jamais condescendant.

## Dernière mise à jour

2026-08-27 — Création initiale du fichier, reconstruite à partir de
l'historique git réel (voir `CLAUDE.md` → section "Decisions Prises" pour les
sources techniques correspondantes).


---

## clippeak

# STORY.md -- Memoire Narrative

> Ce fichier raconte le projet pour un public exterieur (article de blog, presentation,
> nouvel arrivant non technique). Il complete CLAUDE.md, qui reste la memoire technique.
> A mettre a jour apres toute decision business, pivot ou apprentissage terrain --
> pas apres un simple changement de code.

---

## Objectif produit
Donner a voir, sans backend de production reel, ce a quoi ressemblerait un service qui transforme automatiquement une VOD Twitch en clips prets a poster -- pour valider l'interet du produit et collecter des retours qualifies avant de construire le vrai moteur de traitement.

---

## Statut actuel
Clippeak est une landing page interactive complete et deployee en continu : un visiteur peut coller une URL Twitch, regarder une simulation de traitement (~58 secondes, avec etapes et estimation), telecharger un ZIP de demonstration, puis donner son avis via un formulaire de feedback. Un espace admin protege par mot de passe permet de consulter les retours collectes.

Le produit reel de clipping n'existe pas encore : tout le "traitement" est simule cote front pour tester la promesse et le parcours avant d'investir dans le moteur automatise.

---

## Historique des pivots

### 2026-07-01/02 -- D'une landing monolithique a un parcours structure
**Contexte** : la premiere version de la landing a ete livree d'un bloc, sans respecter la structure section par section attendue dans le brief initial.
**Decision** : refonte complete en composants dedies par section, avec une navigation par ancres et un CTA principal clair.
**Resultat** : parcours conforme au brief, plus facile a faire evoluer section par section par la suite.

### 2026-07-02 -- Le feedback devient un moment du parcours, pas une barriere
**Contexte** : une premiere version verrouillait le bouton de telechargement du ZIP tant que le formulaire de feedback n'etait pas rempli.
**Decision** : le formulaire reste visible et encourage juste apres le resultat de la simulation, mais ne bloque plus la sortie -- le feedback devient une conversion volontaire a forte intention plutot qu'une contrainte.
**Resultat** : parcours plus fluide, coherent avec l'objectif de recueillir des avis sinceres plutot que des reponses forcees.

### 2026-07-02 -- Simplification du formulaire de feedback
**Contexte** : le formulaire posait deux questions qui se recoupaient sur la maniere dont l'utilisateur recevrait son ZIP.
**Decision** : suppression de la question redondante, conservation d'une seule question source de verite sur la plateforme de reception preferee.
**Resultat** : formulaire plus court, donnees plus propres a analyser cote admin.

### 2026-07-02 -- Choix d'un deploiement continu sur infrastructure propre
**Contexte** : le projet avait besoin d'un chemin de mise en production simple pour que la landing soit accessible en dehors du poste de dev.
**Decision** : mise en place d'un pipeline GitHub Actions qui deploie automatiquement sur `main` vers un VPS via SSH, gere par pm2.
**Resultat** : chaque merge sur `main` se retrouve en production sans etape manuelle.

---

## Ce que la cible attend / a appris
- A ce stade, aucune donnee de feedback reelle issue d'utilisateurs externes n'a ete analysee dans ce document -- l'espace admin existe pour cela, mais aucun chiffre (taux de completion, volume de retours, satisfaction) n'a encore ete releve et documente ici. Toute statistique citee ailleurs sur ce projet doit etre verifiee directement dans l'espace admin avant d'etre reutilisee.
- Le parcours a ete concu en partant du principe que forcer une action (feedback obligatoire pour telecharger) degrade la qualite du signal recueilli -- c'est une hypothese de design assumee, pas encore validee par des donnees d'usage reelles.

---

## Garde-fous de contenu
- Ne jamais publier de detail exploitable sur la configuration de deploiement (nom d'hote VPS, chemins serveur, secrets, structure des workflows CI) au-dela de ce qui est deja public dans le depot.
- Ne jamais transcrire de chiffre business (taux de conversion, volume de feedbacks, satisfaction) sans indiquer sa source verifiable (ex. export de l'espace admin a telle date) ou le marquer explicitement "a verifier".
- Ne pas presenter le flow de traitement de VOD comme fonctionnel en production : c'est une simulation assumee tant que le moteur reel n'est pas construit. Toute communication externe doit rester honnete sur ce point.
- Garder un ton factuel et respectueux envers les utilisateurs ayant laisse un feedback ; ne jamais citer de retour individuel de maniere identifiable sans consentement explicite.

---

## Derniere mise a jour
2026-08-27 -- Creation initiale du fichier, reconstituee a partir de l'historique git (12 commits, 2026-07-01 au 2026-07-31) et de `tasks/lessons.md`, en remise a niveau conjointe avec CLAUDE.md.


---

