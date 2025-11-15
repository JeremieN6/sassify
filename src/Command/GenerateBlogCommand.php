<?php

namespace App\Command;

use App\Service\BlogGeneratorService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:generate-blog',
    description: 'Génère un article de blog avec OpenAI',
)]
class GenerateBlogCommand extends Command
{
    public function __construct(
        private BlogGeneratorService $blogGeneratorService
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $io->title('🚀 Génération d\'article de blog');
            
            $article = $this->blogGeneratorService->generateBlogArticle();
            
            $io->success('Article généré avec succès !');
            $io->table(
                ['Propriété', 'Valeur'],
                [
                    ['Titre', $article->getTitre()],
                    ['Slug', $article->getSlug()],
                    ['Auteur', $article->getAuteur()],
                    ['Contenu (aperçu)', substr(strip_tags($article->getContenu()), 0, 200) . '...']
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('Erreur lors de la génération : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
