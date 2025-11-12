<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;


class UserImgModify
{
    #[Assert\NotBlank(message: "Veuillez ajouter une image")]
    #[Assert\File(maxSize: '2M', mimeTypes: ['image/jpeg', 'image/png', 'image/gif','image/jpg'], mimeTypesMessage: "Vous devez upload un fichier jpg, jpeg, png ou gif", maxSizeMessage: "La taille du fichier est trop grande")]
    private ?string $newPicture = null;

    public function getNewPicture(): ?string
    {
        return $this->newPicture;
    }

    public function setNewPicture(?string $newPicture): self
    {
        $this->newPicture = $newPicture;
        return $this;
    }
}
