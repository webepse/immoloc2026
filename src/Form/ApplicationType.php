<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * Permet de faciliter la création de form (perso pas de symfony)
     * @param string $label
     * @param string $placeholder
     * @param array $options
     * @return array
     */
    protected function getConfig(string|bool $label, string $placeholder, array $options = []): array
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
}