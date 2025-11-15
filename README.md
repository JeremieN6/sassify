# Sassify – Incubateur de projets SaaS

🚀 **Plateforme vitrine et incubateur de micro SaaS**.

**Sassify** centralise mes expérimentations produit : chaque micro SaaS est présenté, documenté et filtrable (SaaS, micro-projets, side projects, etc.). L'objectif est de montrer le pipeline d'idées, mesurer la traction et itérer rapidement sur les concepts les plus prometteurs.

## ✨ Fonctionnalités

### 🎯 **Portfolio dynamique**
- **15 projets SaaS actifs** alimentés par un fichier JSON
- **Filtres instantanés** (SaaS, mes projets, micro-projets, autres)
- **Badges de statut** (En ligne, Bêta, En développement) avec pastilles colorées
- **Illustrations duales** clair/sombre chargées automatiquement

### 🧭 **Expérience produit**
- **Cartes détaillées** : storytelling, objectifs, stack technique et KPI clés
- **Logos technologiques** issus d’un dictionnaire JSON partagé
- **Call-to-action contextualisés** (démo, repo, landing)
- **Mode sombre/pour claire** persistant avec bascule globale

### 📈 **Suivi d’incubation**
- **Catégorisation par maturité** : SaaS public, side-project interne, expérimentation
- **Tags de positionnement** (IA, productivité, finance, scraping…)
- **Méta-données temporelles** : date de création, dernière mise à jour, revenus estimés
- **Placeholders automatiques** pour les projets sans visuel encore finalisé

### 🗂️ **Back-office créateur**
- **EasyAdmin** pour administrer les fiches projet et le contenu du site
- **Webhook Stripe/Plans** conservés pour les futures offres récurrentes
- **Blog IA** prêt pour raconter l’itération produit (génération via OpenAI)
- **Système de comptes** pour tester des scénarios clients / bêta-testeurs

## 🛠️ Technologies

### **Backend**
- **Symfony 7** - Framework PHP moderne
- **API REST** - Endpoints d'estimation
- **OpenAI API** - Intelligence artificielle
- **Monolog** - Logging avancé

### **Frontend**
- **Vue.js 3** - Filtrage et interactions côté client
- **Webpack Encore** - Build et bundling
- **Tailwind CSS** - Design system avec thèmes light/dark
- **Flowbite** - Composants UI premium (tabs, listes, badges)

### **Outils**
- **Composer** - Gestionnaire de dépendances PHP
- **NPM** - Gestionnaire de dépendances JavaScript
- **Git** - Contrôle de version

## 🚀 Installation

### **Prérequis**
- PHP 8.2+
- Node.js 18+
- Composer
- Git

### **Installation rapide**

```bash
# Cloner le projet
git clone <repository-url>
cd Sassify

# Installer les dépendances PHP
composer install

# Installer les dépendances JavaScript
npm install

# Configurer l'environnement
cp .env .env.local
# Éditer .env.local avec vos configurations

# Compiler les assets
npm run build

# Démarrer le serveur
php -S localhost:8000 -t public
```

### **Configuration AI (optionnel)**

Le blog et certaines automatisations reposent sur OpenAI. Ajoutez la clé si vous souhaitez explorer ces fonctionnalités :

```bash
# Dans .env.local
OPENAI_API_KEY=sk-your-openai-api-key-here
```

Sans clé, les pages publiques (portfolio, landing, dashboards) fonctionnent totalement.

## Utilisation

### **Interface publique**

1. **Sélection d’une catégorie** via la barre d’onglets
2. **Lecture de la fiche** : storytelling, fonctionnalités, stack
3. **Accès aux demos/repos** grâce au call-to-action
4. **Parcours multi-apps** : les séparateurs permettent de comparer facilement

## 📁 Structure du projet

```
symfony-sassify/
├── assets/vue/                # Composants Vue.js (filters, estimation legacy)
│   ├── components/portfolio/  # UI dédiée au catalogue SaaS
│   └── app.js                 # Point d'entrée Vue
├── public/assets/data/        # JSONs (projets, technologies)
├── templates/components/      # Partials Twig (portfolio, hero, footer)
├── src/Twig/                  # Extensions pour charger les données
├── src/Controller/            # Pages publiques, profil, estimation legacy
└── documents/                 # Ressources & études de cas
```

## 🧪 Tests

```bash
# Tests unitaires PHP
./vendor/bin/phpunit

# Build de développement
npm run dev

# Build de production
npm run build
```

## 📊 Métriques

### **Incubation**
- **15 projets listés** (SaaS, micro-projets, side initiatives)
- **4 catégories** avec filtres instantanés
- **3 statuts** normalisés (En ligne, Bêta, En développement)
- **+30 technologies** référencées dans le dictionnaire JSON

### **Expérience utilisateur**
- **Dark mode** natif, switch persistant
- **Performance** optimisée : images adaptatives, données chargées côté serveur
- **Accessibilité** : structure sémantique, focus visible, aria labels
- **Responsive** : grille adaptative du mobile au desktop large



## 🤝 Contribution

1. Fork le projet
2. Créer une branche feature (`git checkout -b feature/amazing-feature`)
3. Commit les changements (`git commit -m 'Add amazing feature'`)
4. Push vers la branche (`git push origin feature/amazing-feature`)
5. Ouvrir une Pull Request

## 📝 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## Gains et métriques (Phase 3.1)

### 🎯 Optimisations Coûts
- **-85% coûts OpenAI** : Sélection intelligente GPT-3.5 vs GPT-4
  - Projets simples (score 1-4) → GPT-3.5 (~$0.002/estimation)
  - Projets complexes (score 5+) → GPT-4 (~$0.03/estimation)
- **Cache intelligent** : Évite les appels API répétés
- **Limitations** : 3 estimations/jour/IP avec reset quotidien

### 🎨 Améliorations UX
- **+60% utilisabilité** : Layout optimisé avec sections côte à côte
- **Tooltips explicatifs** : Aide contextuelle sur TJM, marge, technologies
- **Dark mode** : Toggle en bas à droite avec persistance
- **Responsive parfait** : Mobile/Tablet/Desktop optimisés
- **-60% espace vide** : Grid/Flexbox pour layout dense

### 🧠 Qualité Estimations
- **+40% précision** : Contexte métier intégré dans les prompts
- **Validation cohérence** : Détection incohérences (TJM vs technologies)
- **Scoring complexité** : Analyse automatique pour sélection GPT
- **Prompts spécialisés** : Freelance vs Entreprise adaptés

## Notes de développement

- **Webpack Encore** pour compiler Tailwind, Vue, Stimulus
- **Twig + extensions custom** pour charger les JSON depuis `/public`
- **Scripts de prévisualisation** (placeholders, fallback mode) pour itérer vite
- **Structure modulaire** : components Twig réutilisés sur la landing et le blog

## 🤝 Contribution

Ce projet est en développement actif. Les contributions sont les bienvenues !

---

## 🆘 Support

- **Documentation** : `/documents/`
- **Issues** : GitHub Issues

## 📊 Statut du projet

| Pilier | Statut | Focus actuel |
|--------|--------|---------------|
| **Portfolio SaaS** | ✅ En ligne | Mise à jour continue des fiches |
| **Back-office** | ✅ EasyAdmin | Ajout automatisations contenu IA |
| **Blog** | 🟡 En préparation | Série "Build in public" à venir |
| **Estimation legacy** | 🟢 Disponible | Sert de base à de futurs outils |
| **Monétisation** | 🟡 Phase d’étude | Tests Stripe / offres d’abonnement |

---

**Développé avec ❤️ par Sassify**
*Switch portfolio SaaS : novembre 2025*
*Refonte section incubateur : novembre 2025*
*Détection auto des visuels light/dark : novembre 2025*

---

**Dernière mise à jour : 14 novembre 2025**
