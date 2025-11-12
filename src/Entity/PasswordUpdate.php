<?php

namespace App\Entity;

use App\Repository\PasswordUpdateRepository;
use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate
{

    #[Assert\NotBlank(message: "Veuillez renseigner votre ancien mot de passe")]
    private ?string $oldPassword = null;

    #[Assert\NotBlank(message: "Le mot de passe ne peut pas être vide")]
    #[Assert\Length(min: 8, minMessage: "Le mot de passe doit contenir au moins {{ limit }} caractères")]
    #[Assert\Regex(pattern: '/[A-Z]/', message: "Le mot de passe doit contenir au moins une lettre majuscule")]
    #[Assert\Regex(pattern: '/[a-z]/', message: "Le mot de passe doit contenir au moins une lettre minuscule")]
    #[Assert\Regex(pattern: '/\d/', message: "Le mot de passe doit contenir au moins un chiffre")]
    #[Assert\Regex(pattern: '/[^A-Za-z0-9]/', message: "Le mot de passe doit contenir au moins un caractère spécial")]
    private ?string $newPassword = null;

    #[Assert\EqualTo(propertyPath:"newPassword", message: "Les mots de passe ne correspondent pas")]
    private ?string $confirmPassword = null;


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): static
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): static
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): static
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
