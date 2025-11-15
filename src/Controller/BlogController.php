<?php

namespace App\Controller;

use App\Repository\BlogRepository;
use App\Service\OpenAIService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;

final class BlogController extends AbstractController
{
    #[Route('/blog/generate/openai', name: 'app_blog_generate_openai')]
    public function generateWithOpenAI(
        OpenAIService $openAIService,
        BlogRepository $blogRepository,
        \Doctrine\ORM\EntityManagerInterface $em
    ): Response {
        // Exemple de prompt SEO pour un article de blog
        $prompt = "Rédige un article de blog SEO sur le thème : 'L'importance de l'estimation précise dans les projets web en 2025'. Structure l'article en JSON avec les clés : titre, slug (génère un slug SEO à partir du titre), contenu (corps principal, formaté en HTML), auteur (utilise 'Jérémie N.'), createdAt (date du jour, format YYYY-MM-DD). Le contenu doit être informatif, structuré (titres H2/H3), et optimisé pour le SEO. Réponds uniquement avec un JSON strictement valide.";

        $result = $openAIService->callOpenAI($prompt);

        // Validation et fallback minimal
        if (!isset($result['titre'], $result['slug'], $result['contenu'])) {
            throw new \Exception('Réponse OpenAI incomplète.');
        }

        $article = new \App\Entity\Blog();
        $article->setTitre($result['titre']);
        $article->setSlug($result['slug']);
        $article->setContenu($result['contenu']);
        $article->setAuteur($result['auteur'] ?? 'Jérémie N.');
        $article->setCreatedAt(new \DateTimeImmutable($result['createdAt'] ?? date('Y-m-d')));
        $article->setUpdatedAt(new \DateTimeImmutable());

        $em->persist($article);
        $em->flush();

        return $this->redirectToRoute('app_blog_post', ['slug' => $article->getSlug()]);
    }
    #[Route('/blog', name: 'app_blog')]
    public function index(Request $request, BlogRepository $blogRepository): Response
    {
        // Récupération du numéro de page depuis l'URL (par défaut 1)
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 8; // 8 articles par page

        // Création de la requête paginée
        $query = $blogRepository->createQueryBuilder('b')
            ->where('b.published = :published')
            ->setParameter('published', true)
            ->orderBy('b.id', 'ASC')
            ->getQuery();

        // Configuration de la pagination
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult($limit * ($page - 1))
            ->setMaxResults($limit);

        // Calcul des informations de pagination
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        // Ajout de l'intro pour chaque article
        foreach ($paginator as $article) {
            $article->intro = substr(strip_tags($article->getContenu()), 0, 150) . '...';
        }

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'page_title' => 'Le Blog - Sassify',
            'articles' => $paginator,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_items' => $totalItems,
                'limit' => $limit,
                'has_previous' => $page > 1,
                'has_next' => $page < $totalPages,
                'previous_page' => $page - 1,
                'next_page' => $page + 1,
            ]
        ]);
    }
    #[Route('/blog/{slug}', name: 'app_blog_post')]
    public function post(string $slug, BlogRepository $blogRepository): Response
    {
        $article = $blogRepository->findOneBySlug($slug);
        if (!$article) {
            throw $this->createNotFoundException('Article non trouvé');
        }
        return $this->render('blog/post.html.twig', [
            'article' => $article,
            'slug' => $slug,
            'controller_name' => 'BlogController',
            'page_title' => 'Article - ' . $article->getTitre(),
        ]);
    }
}
