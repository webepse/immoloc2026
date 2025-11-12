<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfig('Prénom', "Votre prénom..."))
            ->add('lastName', TextType::class, $this->getConfig('Nom', "Votre nom de famille..."))
            ->add('email', EmailType::class, $this->getConfig('Email', "Votre adresse e-mail..."))
            ->add('introduction', TextType::class, $this->getConfig('Introduction', "Une présentation rapide"))
            ->add('description', TextareaType::class, $this->getConfig('Description détaillée',"Présentez vous avec un peu plus de détails"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
