<?php

namespace App\Controller\Admin;

use App\Entity\Blog;
use App\Service\BlogGeneratorService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class BlogCrudController extends AbstractCrudController
{
    public function __construct(
        private BlogGeneratorService $blogGeneratorService,
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Blog::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', 'Articles de blog')
            ->setPageTitle('new', 'Créer un article')
            ->setPageTitle('edit', 'Modifier l\'article')
            ->overrideTemplate('crud/index', 'admin/blog/index.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        // Création de l'action personnalisée "Générer un article"
        $generateArticle = Action::new('generateArticle', 'Générer un article', 'fas fa-magic')
            ->linkToUrl('#') // Lien factice requis par EasyAdmin
            ->addCssClass('btn btn-success')
            ->setHtmlAttributes([
                'onclick' => "openGenerateModal(); return false;"
            ])
            ->createAsGlobalAction(); // Affiche le bouton en haut à droite

        return $actions
            ->add(Crud::PAGE_INDEX, $generateArticle);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('titre', 'Titre'),
            TextField::new('slug', 'Slug')->hideOnIndex(),
            TextareaField::new('contenu', 'Contenu')
                ->setNumOfRows(20)
                ->setHelp('HTML brut - Ne pas utiliser l\'éditeur WYSIWYG pour préserver la structure'),
            TextField::new('auteur', 'Auteur'),
            BooleanField::new('published', 'Publié'),
            DateTimeField::new('createdAt', 'Créé le')->hideOnForm(),
            DateTimeField::new('updatedAt', 'Modifié le')->hideOnForm(),
        ];
    }



    /**
     * Génère un article avec le sujet fourni (route dédiée)
     */
    #[Route('/admin/blog/generate', name: 'admin_blog_generate', methods: ['POST'])]
    public function generateArticleApi(Request $request): Response
    {
        $topic = $request->request->get('topic');

        if (empty($topic)) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Veuillez saisir un sujet pour l\'article.'
            ], 400);
        }

        try {
            // Génération de l'article avec le sujet personnalisé
            $article = $this->blogGeneratorService->generateBlogArticle($topic);

            // URL d'édition pour redirection
            $editUrl = $this->adminUrlGenerator
                ->setController(self::class)
                ->setAction(Action::EDIT)
                ->setEntityId($article->getId())
                ->generateUrl();

            return new JsonResponse([
                'success' => true,
                'message' => sprintf('Article "%s" généré avec succès !', $article->getTitre()),
                'article' => [
                    'id' => $article->getId(),
                    'titre' => $article->getTitre(),
                    'slug' => $article->getSlug()
                ],
                'editUrl' => $editUrl
            ]);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la génération : ' . $e->getMessage()
            ], 500);
        }
    }
}
