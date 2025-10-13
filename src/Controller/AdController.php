<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
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

    /*
        #[Route('/ads/{id}', name: 'ads_show')]
        public function show(int $id, ManagerRegistry $doctrine): Response
        {
            $repo = $doctrine->getRepository(Ad::class);
            $ad = $repo->find($id);

            return $this->render("ad/show.html.twig",[
                "ad" => $ad
            ]);
        }
    */

    /*
        #[Route('/ads/{id}', name: 'ads_show')]
        public function show(int $id, AdRepository $repo): Response
        {
            $ad = $repo->find($id);

            return $this->render("ad/show.html.twig",[
                "ad" => $ad
            ]);
        }
    */

   /*
        // symfony Flex
        #[Route('/ads/{id}', name: 'ads_show')]
        public function show(Ad $ad): Response
        {
            return $this->render("ad/show.html.twig",[
                "ad" => $ad
            ]);
        }
   */

    /**
     * permet d'afficher la page de l'annonce choisie par l'utilisateur avec son slug
     * pour faire fonctionner ceci, il faut dans le fichier config/packages/doctrine.yaml (ligne 28) passer controller_resolver:
     * auto_mapping: true (false par défaut)

            #[Route('/ads/{slug}', name: 'ads_show')]
            public function show(Ad $ad): Response
            {
                return $this->render("ad/show.html.twig",[
                    "ad" => $ad
                ]);
            }
    */


    /**
     * permet d'afficher la page de l'annonce choisie par l'utilisateur avec son slug
     * @param Ad $ad
     * @return Response
     */
    #[Route('/ads/{slug}', name: 'ads_show')]
    public function show(
        #[MapEntity(mapping: ['slug' => 'slug'])]
        Ad $ad
    ): Response
    {
        // dump($ad);
        return $this->render("ad/show.html.twig",[
            "ad" => $ad
        ]);
    }
}
