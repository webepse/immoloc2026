<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    /**
     * Permet de faciliter la création de form (perso pas de symfony)
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    private function getConfig(string $label, string $placeholder, array $options = []): array
    {
        return array_merge_recursive(
            [
                'label' => $label,
                'attr' => [
                    'placeholder' => $placeholder
                ]
            ], $options
        );
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'attr' => [
                    'placeholder' => 'Ajoute un titre à ton annonce'
                ]
            ])
            ->add('slug', TextType::class, $this->getConfig('slug', 'Adresse Web (automatique)',[
                'required' => false
            ]))
            ->add('coverImage', UrlType::class, $this->getConfig('Image de couverture', 'Donnez l\'adresse URL de votre image'))
            ->add('introduction', TextType::class, $this->getConfig('Introduction', 'Donnez une description globale de votre annonce'))
            ->add('content', TextareaType::class, $this->getConfig('Description détaillée', 'Donnez une description détaillée de votre annonce'))
            ->add('rooms', IntegerType::class, $this->getConfig('Nombre de chambre', 'Donnez nombre de chambre disponible'))
            ->add('price', MoneyType::class, $this->getConfig('Prix par nuit', "Donnez le prix que vous voulez pour une nuit"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
