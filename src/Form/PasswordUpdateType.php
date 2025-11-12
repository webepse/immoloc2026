<?php

namespace App\Form;

use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfig('Ancien mot de passe', 'Votre ancien mot de passe'))
            ->add('newPassword', PasswordType::class, $this->getConfig('Nouveau mot de passe', 'Votre nouveau mot de passe'))
            ->add('confirmPassword', PasswordType::class, $this->getConfig('Confirmation du mot de passe', 'Confirmez votre nouveau mot de passe'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
