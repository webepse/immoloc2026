<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Comment;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Types\Types;
use App\Repository\AdRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AdRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields:['title'], message:"Une autre annonce possède déjà ce titre, merci de le modifier")]
class Ad
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(min:10,max:255, minMessage:"Le titre doit faire plus de 10 caractères",maxMessage:"Le titre ne peut pas faire plus de 255 caractères")]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\Column(type: Types::TEXT)]
     #[Assert\Length(min:20,max:255, minMessage:"L'introduction doit faire plus de 20 caractères",maxMessage:"L'introduction ne peut pas faire plus de 255 caractères")]
    private ?string $introduction = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\Length(min:100, minMessage:"Votre description doit faire plus de 100 caractères")]
    private ?string $content = null;

    #[ORM\Column(length: 255)]
    #[Assert\Url(message: "L'adresse de l'image doit être valide")]
    private ?string $coverImage = null;

    #[ORM\Column]
    private ?int $rooms = null;

    /**
     * @var Collection<int, Image>
     */
    #[ORM\OneToMany(targetEntity: Image::class, mappedBy: 'ad', orphanRemoval: true)]
    #[Assert\Valid()]
    private Collection $images;

    #[ORM\ManyToOne(inversedBy: 'ads')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, Booking>
     */
    #[ORM\OneToMany(targetEntity: Booking::class, mappedBy: 'ad')]
    private Collection $bookings;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'ad', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function initializeSlug(): void
    {
        if(empty($this->slug))
            {
                $slugify = new Slugify();
                $this->slug = $slugify->slugify($this->title);
            }        
    }

    public function getNotAvailableDays(): ?array
    {
        $notAvailableDays = [];
        foreach($this->bookings as $booking)
        {
            // calculer les jours qui se trouvent entre la date d'arrivée et la date de départ
            // la fonction range() de php permet de créer un tableau qui contient chaque étpae existane entre deux nombre
            // $result = range(10,20,2)
            // reponse = [10,12,14,16,18,20]
            $resultat = range($booking->getStartDate()->getTimestamp(),$booking->getEndDate()->getTimestamp(), 24*60*60);
            $days = array_map(function($dayTimestamp){
                return new \DateTime(date('Y-m-d',$dayTimestamp));
            },$resultat);
            $notAvailableDays = array_merge($notAvailableDays,$days);
        }
        return $notAvailableDays;
    }

    /**
     * Permet de récup la moyenne des notes sur l'annonce
     *
     * @return integer
     */
    public function getAvgRatings(): int
    {
        // calculer la somme des notations
        /* array_reduce => permet de réduire le tableau à une seule valeur (attention il faut un tableau pas une array Collection donc on va utiliser toArray()) 1er param tab / 2ème param la fonction pour chaque valeur / 3ème paramètre la valeur par défaut */

        $sum = array_reduce($this->comments->toArray(), function($total, $comment){
            return $total + $comment->getRating();
        },0);

        // faire la division pour avoir la moyenne (ex ternaire)
        if(count($this->comments) > 0) return $moyenne = round($sum / count($this->comments));

        return 0;

    }

    /**
     * Permet de récupérer le commentaire d'un auteur par rapport à une annonce
     *
     * @param User $author
     * @return Comment|null
     */
    public function getCommentFromAuthor(User $author): ?Comment
    {
        foreach($this->comments as $comment)
        {
            if($comment->getAuthor() === $author)
            {
                return $comment;
            }
        }
        return null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(string $introduction): static
    {
        $this->introduction = $introduction;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }

    public function setCoverImage(string $coverImage): static
    {
        $this->coverImage = $coverImage;

        return $this;
    }

    public function getRooms(): ?int
    {
        return $this->rooms;
    }

    public function setRooms(int $rooms): static
    {
        $this->rooms = $rooms;

        return $this;
    }

    /**
     * @return Collection<int, Image>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setAd($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getAd() === $this) {
                $image->setAd(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setAd($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getAd() === $this) {
                $booking->setAd(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAd($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAd() === $this) {
                $comment->setAd(null);
            }
        }

        return $this;
    }
}
