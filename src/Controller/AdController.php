<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AnnonceType;
use App\Repository\AdRepository;
//use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/ads/new', name: 'ads_create')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $ad = new Ad();

        // instanciation de 2 objets Image
        $image1 = new Image();
        $image2 = new Image();

        // set les objets Image avec les infos Url et Caption
        $image1->setUrl("https://picsum.photos/400/200")
            ->setCaption('Titre 1');

        $image2->setUrl("https://picsum.photos/400/200")
            ->setCaption('Titre 2');

        // ajout des 2 objets Image à mon objet Ad
        $ad->addImage($image1);
        $ad->addImage($image2);

        $form = $this->createForm(AnnonceType::class,$ad);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash(
                'success',
                "L'annonce <strong>".$ad->getTitle()."</strong> a bien été enregistrée"
            );
            return $this->redirectToRoute('ads_show',['slug'=>$ad->getSlug()]);
        }

        return $this->render('ad/new.html.twig',[
            'myForm' => $form->createView()
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
