<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    /**
     * Permet d'afficher la fiche d'un utilisateur
     * @param User $user
     * @return Response
     */
    #[Route('/user/{slug}', name: 'user_show')]
    public function index(User $user): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $user,
        ]);
    }


    /**
     * Permet d'afficher son profil utilisateur
     * @return Response
     */
    #[Route('/account', name: 'account_index')]
    public function myAccount(): Response
    {
        return $this->render('user/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
