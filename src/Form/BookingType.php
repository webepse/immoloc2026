<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Booking;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('startDate', DateType::class, $this->getConfig("Date d'arrivée","La date à laquelle vous comptez arriver"),[
                "widget" => "single_text"
            ])
            ->add('endDate', DateType::class, $this->getConfig("Date de départ du bien","La date à laquelle vous comptez partir du bien réservé"),[
                "widget" => "single_text"
            ])
            ->add('comment', TextareaType::class, $this->getConfig(false, "Si vous avez un commentaire, n'hésitez pas à en faire part"),[
                "required" => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
