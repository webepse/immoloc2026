<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
//use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdController extends AbstractController
{
    /**
     * Permet d'afficher les annonces
     * @param AdRepository $repo
     * @return Response
     */
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

    /**
     * Permet d'afficher la page de l'annonce choisie par l'utilisateur avec son slug
     * Attention {slug} c'est paramConverter pas lié à Symfony Flex
     * @param Ad $ad
     * @return Response
     */
    #[Route('/ads/{slug}', name: 'ads_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Ad $ad
    ): Response
    {
        //dump($ad);
        return $this->render("ad/show.html.twig",[
            "ad" => $ad
        ]);
    }
}
