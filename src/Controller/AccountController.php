<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Entity\User;
use App\Entity\UserImgModify;
use App\Form\AccountType;
use App\Form\ImgModifyType;
use App\Form\PasswordUpdateType;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class AccountController extends AbstractController
{
    /**
     * Permet de se connecter
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        $loginError = null;
        //dump($error);

        if($error instanceof TooManyLoginAttemptsAuthenticationException)
        {
            $loginError = "Trop de tentatives de connexion, réessayer plus tard.";
        }


        return $this->render('account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $username,
            'loginError' => $loginError
        ]);
    }

    /**
     * Permet de se déconnecter
     *
     * @return void
     */
    #[Route('/logout', name:'account_logout')]
    public function logout(): void
    {}


    /**
     * Permet d'inscrire un utilisateur
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hasher
     * @return Response|string
     */
    #[Route("/register", name:"account_register")]
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $hasher): Response|string
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // gestion de l'image de profil
            $file = $form['picture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate("Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()", $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }

            $hash = $hasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                "success",
                "votre compte a bien été créé"
            );
            return $this->redirectToRoute('account_login');
        }

        return $this->render("account/registration.html.twig",[
            'myForm' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier son profil
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/account/profile", name:"account_profile")]
    #[IsGranted("ROLE_USER")]
    public function profile(Request $request, EntityManagerInterface $manager): Response
    {
        /**
         * Récupération de l'utilisateur connecté
         * @var User $user
         */
        $user = $this->getUser();
        // gestion de l'image de profil
        // pour garder en mémoire le nom de l'image en vue de le remettre après validation du formulaire
        $fileName = $user->getPicture();
        // comme on peut laisser le choix à l'utilisateur de ne pas mettre d'image
        if(!empty($fileName))
        {
            // modification de l'objet user pour mettre une image au paramètre $picture
            $user->setPicture(new File($this->getParameter('uploads_directory')."/".$fileName));
        }
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // remise en place du nom de l'image
            $user->setPicture($fileName);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                "success",
                "Les données ont été enregistrées avec succès"
            );
        }

        return $this->render("account/profile.html.twig",[
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Permet de modifier son mot de passe
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/account/update-password', name: 'account_password')]
    #[IsGranted("ROLE_USER")]
    public function updatePassword(Request $request, UserPasswordHasherInterface $hasher, EntityManagerInterface $manager): Response
    {
        /**
         * Récupération de l'utilisateur connecté
         * @var User $user
         */
        $user = $this->getUser();
        $passwordUpdate = new PasswordUpdate();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            // vérification si le mot de passe actuel est correct
            if(!$hasher->isPasswordValid($user, $passwordUpdate->getOldPassword()))
            {
                // gestion du message d'erreur
                //$this->addFlash("error", "Le mot de passe actuel est incorrect");
                $form->get('oldPassword')->addError(new \Symfony\Component\Form\FormError("Le mot de passe actuel est incorrect"));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user, $newPassword);
                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre mot de passe à bien été modifié"
                );
                return $this->redirectToRoute('account_index');
            }
        }

        return $this->render("account/password.html.twig", [
            "myForm" => $form->createView(),
        ]);

    }

    /**
     * Permet de modifier l'image de profil
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Reponse|string
     */
    #[Route('/account/img-modify', name: 'account_modifimg')]
    #[IsGranted("ROLE_USER")]
    public function imgModify(Request $request, EntityManagerInterface $manager): Response|string
    {
        $imgModify = new UserImgModify();
        /**
         * @var User $user
         */
        $user = $this->getUser();
        $form = $this->createForm(ImgModifyType::class, $imgModify);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['newPicture']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate("Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()", $originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

                try{
                    $file->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                    // supprimer l'image dans le dossier
                    // gèrer en plus le fait que l'image est facultative
                    if(!empty($user->getPicture())) {
                        unlink($this->getParameter('uploads_directory') . "/" . $user->getPicture());
                    }
                }catch(FileException $e){
                    return $e->getMessage();
                }
                $user->setPicture($newFilename);
            }
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre image de profile a bien été modifiée"
            );
            return $this->redirectToRoute('account_index');
        }

        return $this->render("account/imgModify.html.twig", [
            "myForm" => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer l'image de profil'
     * @param EntityManagerInterface $manager
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    #[Route('/account/remove-img', name: 'account_delimg')]
    #[IsGranted("ROLE_USER")]
    public function removeImg(EntityManagerInterface $manager): Response
    {
        /**
         * @var User $user
         */
        $user = $this->getUser();
        if(!empty($user->getPicture()))
        {
            unlink($this->getParameter('uploads_directory') . "/" . $user->getPicture());
            $user->setPicture(null);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre image de profil a bien été supprimée"
            );
        }
        return $this->redirectToRoute('account_index');
    }

    /**
     * Permet d'afficher les réservation de l'utilisateur
     *
     * @return Response
     */
    #[Route('/account/booking', name:'account_booking')]
    #[IsGranted("ROLE_USER")]
    public function bookings(): Response
    {
        return $this->render("account/bookings.html.twig");
    }

}
