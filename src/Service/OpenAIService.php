<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Psr\Log\LoggerInterface;

class OpenAIService
{
    private const OPENAI_API_URL = 'https://api.openai.com/v1/chat/completions';
    private const CACHE_DURATION = 1800; // 30 minutes

    private array $estimationCache = [];

    public function __construct(
        private HttpClientInterface $httpClient,
        #[Autowire('%env(OPENAI_API_KEY)%')] private string $apiKey,
        private LoggerInterface $logger
    ) {}

    /**
     * Appel générique à l'API OpenAI
     */
    public function callOpenAI(string $prompt, array $options = []): array
    {
        $defaultOptions = [
            'model' => 'gpt-4o-mini',
            'temperature' => 0.3,
            'max_tokens' => 2000,
            'response_format' => ['type' => 'json_object']
        ];
        
        $options = array_merge($defaultOptions, $options);
        
        try {
            $response = $this->httpClient->request('POST', self::OPENAI_API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $options['model'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un expert en estimation de projets web. Tu réponds toujours en JSON valide.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => $options['temperature'],
                    'max_tokens' => $options['max_tokens'],
                    'response_format' => $options['response_format']
                ]
            ]);

            $data = $response->toArray();
            
            if (!isset($data['choices'][0]['message']['content'])) {
                throw new \Exception('Réponse OpenAI invalide');
            }

            $content = $data['choices'][0]['message']['content'];
            $result = json_decode($content, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Réponse JSON invalide: ' . json_last_error_msg());
            }

            return $result;

        } catch (\Exception $e) {
            $this->logger->error('Erreur OpenAI: ' . $e->getMessage(), [
                'prompt' => substr($prompt, 0, 200) . '...',
                'options' => $options
            ]);
            
            throw new \Exception('Erreur lors de l\'appel à OpenAI: ' . $e->getMessage());
        }
    }

    /**
     * Génère une estimation de projet avec OpenAI (avec optimisations)
     */
    public function generateEstimation(array $projectData, string $userType): array
    {
        // 1. Vérifier le cache
        $cacheKey = $this->generateCacheKey($projectData, $userType);
        $cachedResult = $this->getCachedEstimation($cacheKey);

        if ($cachedResult) {
            $this->logger->info('Estimation trouvée en cache', ['cacheKey' => substr($cacheKey, 0, 50)]);
            return $cachedResult;
        }

        // 2. Calculer la complexité et sélectionner le modèle optimal
        $complexityData = $this->calculateComplexityAndModel($projectData, $userType);
        $model = $complexityData['model'];
        $complexityScore = $complexityData['complexityScore'];

        $this->logger->info('Modèle sélectionné', [
            'model' => $model,
            'complexityScore' => $complexityScore,
            'userType' => $userType
        ]);

        // 3. Générer le prompt optimisé
        $prompt = $this->buildOptimizedPrompt($projectData, $userType);

        // 4. Appel OpenAI avec modèle et tokens optimisés
        $maxTokens = $model === 'gpt-4' ? 2000 : 1500;

        try {
            $result = $this->callOpenAI($prompt, [
                'model' => $model,
                'temperature' => 0.2,
                'max_tokens' => $maxTokens
            ]);

            // Ajouter les métadonnées d'optimisation
            $result['optimization'] = [
                'model' => $model,
                'complexityScore' => $complexityScore,
                'fromCache' => false,
                'tokensLimit' => $maxTokens
            ];

        } catch (\Exception $e) {
            // Fallback vers GPT-3.5-turbo si GPT-4 échoue
            if ($model === 'gpt-4') {
                $this->logger->warning('Fallback vers GPT-3.5-turbo', ['error' => $e->getMessage()]);

                $result = $this->callOpenAI($prompt, [
                    'model' => 'gpt-3.5-turbo',
                    'temperature' => 0.2,
                    'max_tokens' => 1500
                ]);

                $result['optimization'] = [
                    'model' => 'gpt-3.5-turbo',
                    'complexityScore' => $complexityScore,
                    'fromCache' => false,
                    'tokensLimit' => 1500,
                    'fallback' => true
                ];
            } else {
                throw $e;
            }
        }

        // 5. Mettre en cache le résultat
        $this->setCachedEstimation($cacheKey, $result);

        return $result;
    }

    /**
     * Construit le prompt d'estimation selon le type d'utilisateur
     */
    private function buildEstimationPrompt(array $data, string $userType): string
    {
        $basePrompt = "Analyse ce projet et fournis une estimation détaillée en JSON avec cette structure exacte :\n\n";
        $basePrompt .= json_encode([
            'estimation' => [
                'totalDays' => 0,
                'totalCost' => 0,
                'confidence' => 'high|medium|low',
                'breakdown' => [
                    'development' => ['days' => 0, 'cost' => 0, 'description' => ''],
                    'design' => ['days' => 0, 'cost' => 0, 'description' => ''],
                    'testing' => ['days' => 0, 'cost' => 0, 'description' => ''],
                    'management' => ['days' => 0, 'cost' => 0, 'description' => ''],
                    'margin' => ['days' => 0, 'cost' => 0, 'description' => '']
                ],
                'recommendations' => [],
                'risks' => [],
                'assumptions' => []
            ]
        ], JSON_PRETTY_PRINT);

        if ($userType === 'freelance') {
            return $this->buildFreelancePrompt($data, $basePrompt);
        } else {
            return $this->buildEnterprisePrompt($data, $basePrompt);
        }
    }

    /**
     * Prompt spécifique pour les freelances
     */
    private function buildFreelancePrompt(array $data, string $basePrompt): string
    {
        $prompt = $basePrompt . "\n\n=== DONNÉES FREELANCE ===\n";
        
        // Section 1 : Basics
        if (isset($data['basics'])) {
            $prompt .= "PROJET: " . ($data['basics']['projectType'] ?? 'Non spécifié') . "\n";
            $prompt .= "TECHNOLOGIES: " . ($data['basics']['technologies'] ?? 'Non spécifiées') . "\n";
            $prompt .= "DESCRIPTION: " . ($data['basics']['description'] ?? 'Non fournie') . "\n";
        }

        // Section 2 : Constraints
        if (isset($data['constraints'])) {
            $prompt .= "DISPONIBILITÉ: " . ($data['constraints']['isFullTime'] ? 'Temps plein' : 'Temps partiel') . "\n";
            if ($data['constraints']['hasTjmTarget'] && $data['constraints']['tjmTarget']) {
                $prompt .= "TJM CIBLE: " . $data['constraints']['tjmTarget'] . "€/jour\n";
            }
            if ($data['constraints']['securityMargin']) {
                $prompt .= "MARGE SÉCURITÉ: " . $data['constraints']['securityMargin'] . "%\n";
            }
        }

        // Section 3 : Features
        if (isset($data['features']['selectedFeatures'])) {
            $prompt .= "FONCTIONNALITÉS: " . implode(', ', $data['features']['selectedFeatures']) . "\n";
        }

        // Section 5 : Objectives
        if (isset($data['objectives'])) {
            $prompt .= "OBJECTIFS: " . implode(', ', $data['objectives']['selectedObjectives'] ?? []) . "\n";
        }

        $prompt .= "\nCALCULE une estimation réaliste pour un freelance, en tenant compte de son niveau d'expérience et de ses objectifs.";
        
        return $prompt;
    }

    /**
     * Prompt spécifique pour les entreprises
     */
    private function buildEnterprisePrompt(array $data, string $basePrompt): string
    {
        $prompt = $basePrompt . "\n\n=== DONNÉES ENTREPRISE ===\n";
        
        // Section 1 : Basics
        if (isset($data['basics'])) {
            $prompt .= "TYPE PROJET: " . ($data['basics']['projectType'] ?? 'Non spécifié') . "\n";
            $prompt .= "PAGES/ÉCRANS: " . ($data['basics']['pageCount'] ?? 'Non spécifié') . "\n";
            $prompt .= "TECHNOLOGIES:\n";
            if (isset($data['basics']['technologies'])) {
                foreach ($data['basics']['technologies'] as $type => $tech) {
                    if ($tech) $prompt .= "  - $type: $tech\n";
                }
            }
        }

        // Section 2 : Structure
        if (isset($data['structure'])) {
            $prompt .= "ÉQUIPE: " . implode(', ', $data['structure']['teamComposition'] ?? []) . "\n";
            if (isset($data['structure']['teamProfiles'])) {
                $prompt .= "PROFILS:\n";
                foreach ($data['structure']['teamProfiles'] as $profile) {
                    if ($profile['type'] && $profile['count']) {
                        $prompt .= "  - " . $profile['type'] . ": " . $profile['count'] . " personne(s)\n";
                    }
                }
            }
        }

        // Section 3 : Functionalities
        if (isset($data['functionalities'])) {
            $prompt .= "FONCTIONNALITÉS: " . implode(', ', $data['functionalities']['selectedFeatures'] ?? []) . "\n";
            $prompt .= "COMPLEXITÉ: " . ($data['functionalities']['functionalComplexity'] ?? 'Non spécifiée') . "\n";
            $prompt .= "SCALABILITÉ: " . ($data['functionalities']['scalability'] ?? 'Non spécifiée') . "\n";
        }

        // Section 5 : Objectives
        if (isset($data['objectives'])) {
            $prompt .= "OBJECTIF: " . ($data['objectives']['projectObjective'] ?? 'Non spécifié') . "\n";
            if ($data['objectives']['budgetContext'] === 'defined' && $data['objectives']['budgetAmount']) {
                $prompt .= "BUDGET: " . $data['objectives']['budgetAmount'] . "€\n";
            }
        }

        // Section 6 : Pricing
        if (isset($data['pricing'])) {
            if ($data['pricing']['hasDailyCosts'] && isset($data['pricing']['dailyCosts'])) {
                $prompt .= "COÛTS JOURNALIERS:\n";
                foreach ($data['pricing']['dailyCosts'] as $cost) {
                    if ($cost['profile'] && $cost['dailyRate']) {
                        $prompt .= "  - " . $cost['profile'] . ": " . $cost['dailyRate'] . "€/jour\n";
                    }
                }
            }
            if ($data['pricing']['securityMargin']) {
                $prompt .= "MARGE SÉCURITÉ: " . $data['pricing']['securityMargin'] . "%\n";
            }
        }

        $prompt .= "\nCALCULE une estimation détaillée pour ce projet d'entreprise, en tenant compte de la complexité et des ressources.";
        
        return $prompt;
    }

    /**
     * Calcule la complexité du projet et sélectionne le modèle optimal
     */
    private function calculateComplexityAndModel(array $data, string $userType): array
    {
        $complexityScore = 0;

        // Scoring basé sur les fonctionnalités
        $features = [];
        if ($userType === 'freelance' && isset($data['features']['selectedFeatures'])) {
            $features = $data['features']['selectedFeatures'];
        } elseif ($userType === 'entreprise' && isset($data['functionalities']['selectedFeatures'])) {
            $features = $data['functionalities']['selectedFeatures'];
        }

        $complexFeatures = [
            'auth-sso', 'api-integration', 'ecommerce', 'roles-permissions',
            'erp-crm', 'gdpr-security', 'automated-tests'
        ];

        foreach ($features as $feature) {
            if (in_array($feature, $complexFeatures)) {
                $complexityScore += 2;
            } else {
                $complexityScore += 1;
            }
        }

        // Scoring basé sur le type de projet
        $projectType = '';
        if (isset($data['basics']['projectType'])) {
            $projectType = strtolower($data['basics']['projectType']);
        }

        if (in_array($projectType, ['saas', 'e-commerce', 'api'])) {
            $complexityScore += 3;
        } elseif (in_array($projectType, ['dashboard', 'app-mobile'])) {
            $complexityScore += 2;
        }

        // Scoring basé sur les technologies
        $techCount = 0;
        if (isset($data['basics']['technologies'])) {
            if (is_array($data['basics']['technologies'])) {
                $techCount = count($data['basics']['technologies']);
            } else {
                $techCount = substr_count($data['basics']['technologies'], ',') + 1;
            }
        }

        if ($techCount > 3) {
            $complexityScore += 2;
        }

        // Scoring spécifique entreprise
        if ($userType === 'entreprise') {
            if (isset($data['functionalities']['scalability']) && $data['functionalities']['scalability'] === 'yes') {
                $complexityScore += 2;
            }
            if (isset($data['functionalities']['functionalComplexity'])) {
                switch ($data['functionalities']['functionalComplexity']) {
                    case 'very-complex':
                        $complexityScore += 3;
                        break;
                    case 'complex':
                        $complexityScore += 2;
                        break;
                }
            }
        }

        // Sélection du modèle basée sur la complexité
        // GPT-4 pour projets complexes (score > 8), GPT-3.5-turbo pour le reste
        $model = $complexityScore > 8 ? 'gpt-4' : 'gpt-3.5-turbo';

        return [
            'complexityScore' => $complexityScore,
            'model' => $model
        ];
    }

    /**
     * Génère une clé de cache basée sur les données principales
     */
    private function generateCacheKey(array $data, string $userType): string
    {
        $keyData = [
            'userType' => $userType,
            'projectType' => $data['basics']['projectType'] ?? '',
            'technologies' => $data['basics']['technologies'] ?? '',
            'features' => $userType === 'freelance'
                ? ($data['features']['selectedFeatures'] ?? [])
                : ($data['functionalities']['selectedFeatures'] ?? []),
            'tjm' => $userType === 'freelance'
                ? ($data['constraints']['tjmTarget'] ?? 0)
                : 0,
            'budget' => $userType === 'entreprise'
                ? ($data['objectives']['budgetAmount'] ?? 0)
                : 0,
            'complexity' => $userType === 'entreprise'
                ? ($data['functionalities']['functionalComplexity'] ?? '')
                : ''
        ];

        // Trier les arrays pour cohérence
        if (is_array($keyData['features'])) {
            sort($keyData['features']);
        }

        return md5(json_encode($keyData));
    }

    /**
     * Récupère une estimation du cache
     */
    private function getCachedEstimation(string $cacheKey): ?array
    {
        if (!isset($this->estimationCache[$cacheKey])) {
            return null;
        }

        $cached = $this->estimationCache[$cacheKey];

        // Vérifier l'expiration
        if ((time() - $cached['timestamp']) > self::CACHE_DURATION) {
            unset($this->estimationCache[$cacheKey]);
            return null;
        }

        return $cached['result'];
    }

    /**
     * Met en cache une estimation
     */
    private function setCachedEstimation(string $cacheKey, array $result): void
    {
        $this->estimationCache[$cacheKey] = [
            'result' => $result,
            'timestamp' => time()
        ];

        // Nettoyage automatique du cache (garder max 100 entrées)
        if (count($this->estimationCache) > 100) {
            $oldestKey = array_key_first($this->estimationCache);
            unset($this->estimationCache[$oldestKey]);
        }
    }

    /**
     * Construit un prompt optimisé (plus compact)
     */
    private function buildOptimizedPrompt(array $data, string $userType): string
    {
        if ($userType === 'freelance') {
            return $this->buildOptimizedFreelancePrompt($data);
        } else {
            return $this->buildOptimizedEnterprisePrompt($data);
        }
    }

    /**
     * Prompt optimisé pour freelance (plus détaillé et calibré)
     */
    private function buildOptimizedFreelancePrompt(array $data): string
    {
        $freelanceType = $data['constraints']['freelanceType'] ?? 'forfait';

        // En-tête selon le type de freelance
        if ($freelanceType === 'regie') {
            $prompt = "Tu es un expert senior en estimation de projets web réalisés par des freelances en RÉGIE. Tu dois fournir une estimation **réaliste et argumentée** basée sur un TJM et le temps nécessaire.\n\n";
            $prompt .= "Tu réponds en **JSON strict**, exactement dans la structure fournie ci-dessous.\n\n";
        } else {
            $prompt = "Tu es un expert senior en pricing commercial freelance FORFAIT. Tu dois fournir un **prix de vente fixe recommandé réaliste et compétitif** pour le client.\n\n";
            $prompt .= "Tu réponds en **JSON strict**, exactement dans la structure fournie ci-dessous.\n\n";
        }

        // Contexte projet
        $prompt .= "### Contexte :\n";
        $prompt .= "- Type de projet : " . ($data['basics']['projectType'] ?? 'Non spécifié') . "\n";
        $prompt .= "- Description : " . ($data['basics']['description'] ?? 'Refonte/évolution') . "\n";
        $prompt .= "- Technologies : " . ($data['basics']['technologies'] ?? 'Non spécifiées') . "\n";

        if (isset($data['constraints'])) {
            $prompt .= "- Disponibilité : " . ($data['constraints']['isFullTime'] ? 'Temps plein' : 'Temps partiel') . "\n";

            if ($freelanceType === 'forfait') {
                // Informations client pour devis forfait (prix fixe négocié)
                if (isset($data['clientInfo']['clientType'])) {
                    $prompt .= "- Type de client : " . $this->getClientTypeLabel($data['clientInfo']['clientType']) . "\n";
                }
                if (isset($data['clientInfo']['clientBudgetRange'])) {
                    $prompt .= "- Budget indicatif : " . $this->getBudgetRangeLabel($data['clientInfo']['clientBudgetRange']) . "\n";
                }
                if (isset($data['clientInfo']['competitiveContext'])) {
                    $prompt .= "- Contexte concurrentiel : " . $this->getCompetitiveContextLabel($data['clientInfo']['competitiveContext']) . "\n";
                }
            } else {
                // Informations freelance pour estimation régie (TJM interne)
                if (isset($data['constraints']['tjmTarget'])) {
                    $prompt .= "- TJM cible : " . $data['constraints']['tjmTarget'] . "€/jour\n";
                }
                if (isset($data['constraints']['securityMargin'])) {
                    $prompt .= "- Marge de sécurité : " . $data['constraints']['securityMargin'] . "%\n";
                }
            }
        }

        // Fonctionnalités
        if (isset($data['features']['selectedFeatures']) && !empty($data['features']['selectedFeatures'])) {
            $prompt .= "- Fonctionnalités : " . implode(', ', $data['features']['selectedFeatures']) . "\n";
        }

        // Objectifs
        if (isset($data['objectives']['selectedObjectives']) && !empty($data['objectives']['selectedObjectives'])) {
            $prompt .= "- Objectifs : " . implode(', ', $data['objectives']['selectedObjectives']) . "\n";
        }

        // Contraintes spécifiques
        $prompt .= "\n### Contraintes :\n";
        $prompt .= "- Tu dois intégrer un temps pour le design, les tests et le déploiement\n";

        if (isset($data['deliverables'])) {
            if (isset($data['deliverables']['mockupsProvided']) && $data['deliverables']['mockupsProvided'] === false) {
                $prompt .= "- Maquettes à créer (ajouter temps design)\n";
            }
            if (isset($data['deliverables']['specsStatus']) && $data['deliverables']['specsStatus'] === 'to-define') {
                $prompt .= "- Spécifications à définir (ajouter temps analyse)\n";
            }
        }

        if ($freelanceType === 'forfait') {
            $prompt .= "- Utilise les benchmarks prix marché selon le type de client\n";
            $prompt .= "- Intègre une marge commerciale appropriée (30-50%)\n";
            $prompt .= "- Ajuste selon le contexte concurrentiel\n";
        } else {
            $prompt .= "- Sois réaliste : un projet WordPress complet ne se fait pas en 5 jours\n";
            $prompt .= "- Intègre la marge de sécurité si spécifiée\n";
        }

        // Règles spécifiques selon le type de freelance
        if ($freelanceType === 'regie') {
            $prompt .= "\n### Facturation RÉGIE (TJM × temps) :\n";
            $prompt .= "- Base-toi sur un TJM réaliste pour le marché\n";
            $prompt .= "- Startup/PME : 400-600€/jour\n";
            $prompt .= "- Grande entreprise : 600-800€/jour\n";
            $prompt .= "- Calcule le temps nécessaire précisément\n";
            $prompt .= "- Facturation transparente : TJM × jours\n";
            $prompt .= "- Le client paie le temps passé\n";
        } else {
            $prompt .= "\n### Prix FORFAIT (prix fixe marché) :\n";
            $prompt .= "- WordPress complet : 8k-15k€ selon complexité\n";
            $prompt .= "- E-commerce : 10k-25k€ selon fonctionnalités\n";
            $prompt .= "- Application web : 15k-50k€ selon envergure\n";
            $prompt .= "- Prix fixe négocié, indépendant du temps\n";
            $prompt .= "- Intègre marge commerciale (30-50%)\n";
        }

        $prompt .= "\n### Structure de réponse JSON :\n";
        $prompt .= "```json\n";
        $prompt .= json_encode([
            'estimation' => [
                'totalDays' => 0,
                'totalCost' => 0,
                'confidence' => 'high|medium|low',
                'breakdown' => [
                    'analysis' => ['days' => 0, 'cost' => 0, 'description' => 'Analyse des besoins et spécifications'],
                    'design' => ['days' => 0, 'cost' => 0, 'description' => 'Conception UI/UX et maquettes'],
                    'development' => ['days' => 0, 'cost' => 0, 'description' => 'Développement des fonctionnalités'],
                    'testing' => ['days' => 0, 'cost' => 0, 'description' => 'Tests et corrections'],
                    'deployment' => ['days' => 0, 'cost' => 0, 'description' => 'Mise en ligne et configuration']
                ],
                'recommendations' => [
                    'Prévoir des points de validation intermédiaires avec le client',
                    'Documenter les livrables pour faciliter la maintenance'
                ],
                'risks' => [
                    'Spécifications incomplètes pouvant allonger les délais',
                    'Changements de dernière minute dans les besoins'
                ],
                'freelanceAnalysis' => [
                    'type' => 'tjm_justification|profitability_analysis',
                    'title' => 'Justification TJM|Analyse Rentabilité',
                    'summary' => 'Résumé de l\'analyse',
                    'details' => [
                        'factor1' => 'Premier facteur d\'analyse',
                        'factor2' => 'Deuxième facteur d\'analyse',
                        'factor3' => 'Troisième facteur d\'analyse'
                    ],
                    'conclusion' => 'Conclusion finale',
                    'status' => 'justified|profitable|risky|unprofitable'
                ]
            ]
        ], JSON_PRETTY_PRINT);
        $prompt .= "\n```\n\n";

        $prompt .= "**IMPORTANT :**\n";
        $prompt .= "- Ne change jamais la structure du JSON\n";
        $prompt .= "- La somme des jours de chaque phase doit correspondre à totalDays\n";
        $prompt .= "- Si une valeur est inconnue, utilise \"description\": \"Non spécifié\" ou \"cost\": 0\n";
        $prompt .= "- Fournis des recommandations et risques spécifiques au projet\n";

        if ($freelanceType === 'regie') {
            $prompt .= "- Le coût doit refléter TJM × temps, facturation transparente\n";
            $prompt .= "\n### ANALYSE SPÉCIFIQUE RÉGIE - Justification TJM :\n";
            $prompt .= "Dans freelanceAnalysis, fournis une justification détaillée du TJM :\n";
            $prompt .= "- type: 'tjm_justification'\n";
            $prompt .= "- title: 'Justification de votre TJM'\n";
            $prompt .= "- summary: Une phrase résumant pourquoi le TJM est justifié\n";
            $prompt .= "- details: {\n";
            $prompt .= "    'complexity': 'Complexité technique : [niveau] - [explication courte]',\n";
            $prompt .= "    'technologies': 'Technologies : [type] - [justification]',\n";
            $prompt .= "    'experience': 'Expérience requise : [niveau] - [pourquoi]',\n";
            $prompt .= "    'market': 'Marché : [fourchette TJM] pour ce profil'\n";
            $prompt .= "  }\n";
            $prompt .= "- conclusion: 'Votre TJM de [X]€/jour est [statut] car [raison principale]'\n";
            $prompt .= "- status: 'justified|undervalued|overvalued'\n";
        } else {
            $prompt .= "- Le coût doit refléter un PRIX DE VENTE FORFAIT marché compétitif\n";
            $prompt .= "\n### ANALYSE SPÉCIFIQUE FORFAIT - Rentabilité :\n";
            $prompt .= "Dans freelanceAnalysis, fournis une analyse effort vs rentabilité :\n";
            $prompt .= "- type: 'profitability_analysis'\n";
            $prompt .= "- title: 'Analyse Effort vs Rentabilité'\n";
            $prompt .= "- summary: Une phrase résumant la rentabilité du projet\n";
            $prompt .= "- details: {\n";
            $prompt .= "    'effort': 'Effort estimé : [X] jours de travail effectif',\n";
            $prompt .= "    'price': 'Prix forfait : [X]€ négocié avec le client',\n";
            $prompt .= "    'tjm_implicit': 'TJM implicite : [X]€/jour ([prix]/[jours])',\n";
            $prompt .= "    'margin': 'Marge sécurité : [X]% incluse dans le prix'\n";
            $prompt .= "  }\n";
            $prompt .= "- conclusion: 'Ce projet est [statut] avec un TJM implicite de [X]€/jour [explication]'\n";
            $prompt .= "- status: 'profitable|risky|unprofitable'\n";
        }

        return $prompt;
    }

    /**
     * Prompt optimisé pour entreprise (compact)
     */
    private function buildOptimizedEnterprisePrompt(array $data): string
    {
        $prompt = "Tu es un consultant expert en estimation de projets web pour entreprises.\n";
        $prompt .= "Ton objectif est de fournir une estimation structurée, fiable et exploitable par une équipe produit ou un décideur technique.\n\n";
        $prompt .= "Tu dois répondre **uniquement en JSON**, sans aucun commentaire en dehors du format.\n\n";

        // Contexte projet
        $prompt .= "### Contexte Projet :\n";
        $prompt .= "- Type : " . ($data['basics']['projectType'] ?? 'Non spécifié') . "\n";

        if (isset($data['functionalities']['selectedFeatures']) && !empty($data['functionalities']['selectedFeatures'])) {
            $prompt .= "- Fonctionnalités principales : " . implode(', ', $data['functionalities']['selectedFeatures']) . "\n";
        }

        if (isset($data['functionalities']['functionalComplexity'])) {
            $prompt .= "- Complexité fonctionnelle : " . $data['functionalities']['functionalComplexity'] . "\n";
        }

        if (isset($data['functionalities']['scalability'])) {
            $prompt .= "- Scalabilité requise : " . $data['functionalities']['scalability'] . "\n";
        }

        if (isset($data['objectives']['budgetAmount'])) {
            $prompt .= "- Budget connu : " . $data['objectives']['budgetAmount'] . "€\n";
        }

        if (isset($data['objectives']['projectObjective'])) {
            $prompt .= "- Objectif stratégique : " . $data['objectives']['projectObjective'] . "\n";
        }

        // Contraintes
        $prompt .= "\n### Contraintes :\n";
        $prompt .= "- L'estimation doit inclure les phases clés : conception, développement, QA, gestion\n";
        $prompt .= "- Propose une estimation avec un niveau de confiance (\"high\", \"medium\", \"low\")\n";
        $prompt .= "- Intègre les risques probables (techniques, humains, planning)\n";
        $prompt .= "- Si le budget est bas par rapport à la charge, mentionne-le dans \"recommendations\"\n";

        // Format JSON
        $prompt .= "\n### Format attendu :\n";
        $prompt .= "```json\n";
        $prompt .= json_encode([
            'estimation' => [
                'totalDays' => 0,
                'totalCost' => 0,
                'confidence' => 'high|medium|low',
                'breakdown' => [
                    'analysis' => ['days' => 0, 'cost' => 0, 'description' => 'Analyse des besoins et architecture'],
                    'design' => ['days' => 0, 'cost' => 0, 'description' => 'Conception UI/UX et prototypage'],
                    'development' => ['days' => 0, 'cost' => 0, 'description' => 'Développement des fonctionnalités'],
                    'testing' => ['days' => 0, 'cost' => 0, 'description' => 'Tests et assurance qualité'],
                    'management' => ['days' => 0, 'cost' => 0, 'description' => 'Gestion de projet et coordination']
                ],
                'recommendations' => [
                    'Mettre en place une méthodologie agile avec sprints de 2 semaines',
                    'Prévoir des tests utilisateurs en cours de développement'
                ],
                'risks' => [
                    'Complexité technique sous-estimée',
                    'Changements de périmètre en cours de projet'
                ]
            ]
        ], JSON_PRETTY_PRINT);
        $prompt .= "\n```\n\n";

        $prompt .= "Tu dois t'assurer que la somme des jours de chaque phase correspond bien à totalDays.\n";
        $prompt .= "Le JSON doit être strictement valide, sans commentaires, et directement parsable.\n";

        return $prompt;
    }

    private function getClientTypeLabel(string $value): string
    {
        $labels = [
            'startup' => 'Startup / Jeune entreprise',
            'pme' => 'PME (10-250 salariés)',
            'grande-entreprise' => 'Grande entreprise (250+ salariés)',
            'association' => 'Association / ONG',
            'particulier' => 'Particulier'
        ];
        return $labels[$value] ?? $value;
    }

    private function getBudgetRangeLabel(string $value): string
    {
        $labels = [
            'low' => '< 5 000€',
            'medium' => '5 000€ - 15 000€',
            'high' => '15 000€ - 50 000€',
            'enterprise' => '50 000€+'
        ];
        return $labels[$value] ?? $value;
    }

    private function getCompetitiveContextLabel(string $value): string
    {
        $labels = [
            'low' => 'Peu de concurrence',
            'medium' => 'Concurrence modérée',
            'high' => 'Forte concurrence'
        ];
        return $labels[$value] ?? $value;
    }
}
