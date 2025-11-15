<?php

namespace App\Controller;

use App\Form\UserProfileFormType;
use App\Repository\PlanRepository;
use App\Repository\QuoteRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PlanRepository $planRepository): Response
    {
        $plans = $planRepository->findAll();
        
        // Charger les données du portfolio
        $jsonPath = $this->getParameter('kernel.project_dir') . '/public/assets/data/saas.json';
        $projectsData = [];
        
        if (file_exists($jsonPath)) {
            $jsonContent = file_get_contents($jsonPath);
            if (str_starts_with($jsonContent, "\xEF\xBB\xBF")) {
                // Strip UTF-8 BOM if present to avoid json_decode issues
                $jsonContent = substr($jsonContent, 3);
            }
            try {
                $projectsData = json_decode($jsonContent, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $exception) {
                error_log('JSON decode error: ' . $exception->getMessage());
                $projectsData = [];
            }
        } else {
            error_log('JSON file NOT FOUND at: ' . $jsonPath);
        }
        
        return $this->render('home/index.html.twig', [
            'page_title' => 'Sassify - Incubateur de projets SaaS',
            'meta_description' => 'Votre incubateur de projets SaaS. Explorez nos offres, technologies et exemples de réalisations pour lancer votre application SaaS avec succès.',
            'plans' => $plans,
            'projects_data' => $projectsData,
        ]);
    }
}
