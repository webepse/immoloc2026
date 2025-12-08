<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Entity\Comment;
use App\Form\BookingType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class BookingController extends AbstractController
{
    #[Route('/ads/{slug}/book', name: 'booking_create')]
    #[IsGranted('ROLE_USER')]
    public function index(Ad $ad, Request $request, EntityManagerInterface $manager): Response
    {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user = $this->getUser();
            $booking->setBooker($user)
                ->setAd($ad);

            if(!$booking->isBookableDates())
            {
                $this->addFlash(
                    'warning',
                    "Les dates que vous avez choisie ne peuvent être réservées: elles sont déjà prises!"
                );
            }else{
                // $this->addFlash(
                //     'success',
                //     'Merci pour votre réservation'
                // );
                $manager->persist($booking);
                $manager->flush();

                return $this->redirectToRoute('booking_show',[
                    'id'=>$booking->getId(),
                    'withAlert' => true
                ]);
            }
        }




        return $this->render('booking/book.html.twig', [
            'myForm' => $form,
            'ad' => $ad
        ]);
    }

    /**
     * Permet d'afficher les informations de la réservation ainsi que la possibilité de commenter la réservation
     *
     * @param Booking $booking
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route("/bookings/{id}", name:"booking_show")]
    #[IsGranted("ROLE_USER")]
    public function show(Booking $booking, Request $request, EntityManagerInterface $manager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $comment->setAd($booking->getAd())
                ->setAuthor($this->getUser());
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre commentaire a bien été pris en compte"
            );
        }

        return $this->render("booking/show.html.twig",[
            'booking' => $booking,
            'myForm' => $form->createView()
        ]);
    }
}
