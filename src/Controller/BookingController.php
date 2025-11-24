<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Booking;
use App\Form\BookingType;
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

            $this->addFlash(
                'success',
                'Merci pour votre réservation'
            );
            $manager->persist($booking);
            $manager->flush();
        }




        return $this->render('booking/book.html.twig', [
            'myForm' => $form,
            'ad' => $ad
        ]);
    }
}
