<?php

namespace App\Service;

use App\Entity\Blog;
use App\Service\OpenAIService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class BlogGeneratorService
{
    public function __construct(
        private OpenAIService $openAIService,
        private EntityManagerInterface $entityManager,
        private LoggerInterface $logger
    ) {}

    /**
     * Génère un article de blog avec OpenAI en conservant la structure exacte
     */
    public function generateBlogArticle(?string $topic = null): Blog
    {
        // Sujets prédéfinis si aucun sujet n'est fourni
        $defaultTopics = [
            "L'importance de l'estimation précise dans les projets web en 2025",
            "Les tendances du développement web à surveiller cette année",
            "Comment optimiser les coûts de développement d'une application",
            "Les erreurs courantes dans la gestion de projet web",
            "Guide complet pour choisir les bonnes technologies web",
            "L'impact de l'IA sur le développement web moderne",
            "Stratégies pour améliorer l'UX/UI de votre application",
            "Les meilleures pratiques de sécurité web en 2025"
        ];

        $selectedTopic = $topic ?: $defaultTopics[array_rand($defaultTopics)];

        // Prompt optimisé pour conserver la structure exacte de ChatGPT
        $prompt = $this->buildStructuredPrompt($selectedTopic);

        try {
            // Appel OpenAI avec des paramètres optimisés pour la génération de contenu
            $result = $this->openAIService->callOpenAI($prompt, [
                'model' => 'gpt-4o-mini',
                'temperature' => 0.3, // Plus strict pour la structure
                'max_tokens' => 3000, // Plus de tokens pour un article complet
                'response_format' => ['type' => 'json_object']
            ]);

            // Post-traitement : rejet si <div> ou <span> présents, ou remplacement automatique
            if (isset($result['contenu']) && preg_match('/<(div|span)[^>]*>/i', $result['contenu'])) {
                // Option stricte : rejet
                throw new \Exception('La réponse OpenAI contient des <div> ou <span>, prompt non respecté.');
                // Option tolérante :
                // $result['contenu'] = preg_replace('/<div[^>]*>/', '<h2>', $result['contenu']);
                // $result['contenu'] = str_replace('</div>', '</h2>', $result['contenu']);
            }

            // Validation de la réponse
            $this->validateResponse($result);

            // Création de l'entité Blog
            $article = $this->createBlogEntity($result);

            // Sauvegarde en base
            $this->entityManager->persist($article);
            $this->entityManager->flush();

            $this->logger->info('Article de blog généré avec succès', [
                'titre' => $article->getTitre(),
                'slug' => $article->getSlug()
            ]);

            return $article;
        } catch (\Exception $e) {
            $this->logger->error('Erreur lors de la génération d\'article', [
                'topic' => $selectedTopic,
                'error' => $e->getMessage()
            ]);

            throw new \Exception('Impossible de générer l\'article : ' . $e->getMessage());
        }
    }

    /**
     * Construit un prompt structuré pour l'audience tech
     */
    private function buildStructuredPrompt(string $topic): string
    {
        return "🚨 INTERDICTION ABSOLUE d’utiliser <div> ou <span> ou toute balise autre que <h2>, <p>, <ul>, <ol> ! Si tu utilises une autre balise, la réponse sera rejetée. 🚨\n\n" .
            "UTILISE UNIQUEMENT CES BALISES HTML :\n" .
            "- <h2>Titre principal</h2> (5-6 sections dans l'article)\n" .
            "- <p>Paragraphe de texte avec <strong>mots-clés</strong></p>\n" .
            "- <ul><li>Point de liste</li></ul>\n" .
            "- <ol><li>Étape numérotée</li></ol>\n\n" .
            "❌ INTERDIT : <div>, <span>, ou tout autre balise\n" .
            "✅ OBLIGATOIRE : <h2>, <p>, <ul>, <ol> uniquement\n\n" .
            "EXEMPLE EXACT À REPRODUIRE :\n" .
            "<h2>Titre de section</h2>\n" .
            "<p>Paragraphe d'introduction avec <strong>mots-clés</strong>.</p>\n" .
            "<ul><li>Point important</li><li>Autre point</li></ul>\n" .
            "<p>Paragraphe de transition.</p>\n\n" .
            "---\n\n" .
            "Tu es un expert en rédaction web spécialisé dans les projets tech. Rédige un article sur : \"$topic\".\n\n" .
            "AUDIENCE : Entreprises tech, CTOs, agences web, développeurs freelances\n" .
            "CONTEXTE : QuickEsti est une plateforme d'estimation de projets web qui aide les développeurs freelances, agences et entreprises à évaluer précisément leurs projets.\n\n" .
            "STRUCTURE : 5-6 sections avec <h2>\n" .
            "STYLE : Professionnel, exemples concrets, conseils pratiques. Ajoute quelques emojis pour rendre l'article plus attrayant. Invente des exemples (avec des chiffres, nom d'entreprise, etc.) avant/après utiles pour illustrer les points clés (parfois).\n\n" .
            "RÈGLE ABSOLUE : Utilise UNIQUEMENT <h2>, <p>, <ul>, <ol>. JAMAIS de <div> ni <span>.\n\n" .
            "Écris un article de 800-1400 mots avec 5-6 sections <h2>. Intègre naturellement les mots-clés : estimation projet, développement web, coût développement, gestion projet, ROI, budget tech, agile, agence web, freelance.\nA la fin ajoute un call-to-action pour inciter à utiliser QuickEsti ou un conseil actionnable pour améliorer les projets web.\n\n" .
            "RÉPONSE JSON EXACTE :\n" .
            "{\n    \"titre\": \"Titre SEO optimisé (50-60 caractères)\",\n    \"slug\": \"slug-seo-automatique\",\n    \"contenu\": \"HTML complet avec structure préservée\",\n    \"auteur\": \"Jérémie N.\",\n    \"metaDescription\": \"Description SEO 150-160 caractères\",\n    \"motsCles\": [\"estimation-projet\", \"développement-web\", \"gestion-projet\"]\n}\n\n" .
            "IMPÉRATIF : L'article doit parler directement aux préoccupations de l'audience tech (coûts, délais, qualité, ROI) avec des exemples du secteur. Préserve exactement le formatage HTML généré.";
    }

    /**
     * Valide la réponse OpenAI
     */
    private function validateResponse(array $result): void
    {
        $requiredFields = ['titre', 'slug', 'contenu', 'auteur'];

        foreach ($requiredFields as $field) {
            if (!isset($result[$field]) || empty($result[$field])) {
                throw new \Exception("Champ manquant ou vide : $field");
            }
        }

        // Validation du slug (format URL)
        if (!preg_match('/^[a-z0-9-]+$/', $result['slug'])) {
            throw new \Exception("Format de slug invalide");
        }

        // Validation du contenu HTML
        if (strlen($result['contenu']) < 500) {
            throw new \Exception("Contenu trop court");
        }

        // Validation stricte : aucune balise <div> ou <span> ne doit être présente
        if (preg_match('/<(div|span)[^>]*>/i', $result['contenu'])) {
            throw new \Exception('Le contenu généré contient des <div> ou <span>, prompt non respecté.');
        }
    }

    /**
     * Crée l'entité Blog à partir de la réponse OpenAI
     */
    private function createBlogEntity(array $result): Blog
    {
        $article = new Blog();

        $article->setTitre($result['titre']);
        $article->setSlug($this->ensureUniqueSlug($result['slug']));
        $article->setContenu($result['contenu']);
        $article->setAuteur($result['auteur']);
        $article->setPublished(true); // Publié par défaut
        $article->setCreatedAt(new \DateTimeImmutable('now'));
        $article->setUpdatedAt(null); // Null à la création, sera défini lors des modifications

        return $article;
    }

    /**
     * Assure l'unicité du slug
     */
    private function ensureUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Vérifie si un slug existe déjà
     */
    private function slugExists(string $slug): bool
    {
        return $this->entityManager->getRepository(Blog::class)
            ->findOneBy(['slug' => $slug]) !== null;
    }
}
