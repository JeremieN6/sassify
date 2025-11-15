<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ProjectDataExtension extends AbstractExtension
{
    private string $projectRoot;

    public function __construct(string $projectRoot)
    {
        $this->projectRoot = $projectRoot;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_projects_data', [$this, 'getProjectsData']),
        ];
    }

    public function getProjectsData(): array
    {
        $jsonPath = $this->projectRoot . '/public/assets/data/saas.json';
        
        if (!file_exists($jsonPath)) {
            error_log("JSON file NOT found at: " . $jsonPath);
            return ['saas' => [], 'technologies' => []];
        }

        $jsonContent = file_get_contents($jsonPath);
        $data = json_decode($jsonContent, true);
        
        error_log("JSON loaded successfully. Projects count: " . count($data['saas'] ?? []));
        
        return $data ?? ['saas' => [], 'technologies' => []];
    }
}
