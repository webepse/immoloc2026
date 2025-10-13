<?php

namespace App\Controller;

use App\Repository\AdRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdController extends AbstractController
{
    #[Route('/ads', name: 'ads_index')]
    public function index(AdRepository $repo): Response
    {
        // appel au model
        $ads = $repo->findAll();

        // vue
        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }
}
