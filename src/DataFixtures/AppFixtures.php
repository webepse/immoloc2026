<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use App\Entity\User;
//use Cocur\Slugify\Slugify;
use App\Entity\Image;
use App\Entity\Booking;
use App\Entity\Comment;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{

    // private $passwordHasher;

    // public function __construct(UserPasswordHasherInterface $passwordHasher)
    // {
    //     $this->passwordHasher = $passwordHasher;
    // }

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        //$slugify = new Slugify();

        $admin = new User();
        $admin->setFirstName('admin')
            ->setLastName('admin')
            ->setPicture("")
            ->setEmail("admin@myepse.be")
            ->setIntroduction($faker->sentence())
            ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
            ->setPassword($this->passwordHasher->hashPassword($admin,'password'))
            ->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($admin);

        // gestion des users
        $users = []; // init d'un tableau pour récup les user pour les associer avec les annonces
        $genres = ['male','femelle'];

        for($u = 1; $u <= 10; $u++)
        {
            $user = new User();
            $genre = $faker->randomElement($genres);

            // $picture = "https://randomuser.me/api/portraits/";
            // $pictureId = $faker->numberBetween(1,99).'.jpg';
            // $picture .= ($genre == 'male' ? 'men/' : 'women/').$pictureId;
            // https://randomuser.me/api/portraits/women/23.jpg

            // pour le mot de passe, j'ai besoin d'un système pour crypter (hash) -> construct pour UserPasswordHasherInterface
            $hash = $this->passwordHasher->hashPassword($user,'password');

            $user->setFirstName($faker->firstName($genre))
                ->setLastName($faker->lastName())
                ->setEmail($faker->email())
                ->setIntroduction($faker->sentence())
                ->setDescription('<p>'.join('</p><p>',$faker->paragraphs(3)).'</p>')
                ->setPassword($hash)
                ->setPicture("");

            $manager->persist($user);
            $users[] = $user; // ajouter un user dans le tableau récup des users (pour les annonces)
        }

        for($i = 1; $i <= 30; $i++)
        {
            $ad = new Ad();
            $title = $faker->sentence();
            //$slug = $slugify->slugify($title);
            $coverImage = "https://picsum.photos/id/".$i."/1000/350";
            $introduction = $faker->paragraph(2);
            $content = '<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            // ["element 1","element 2","element 3"]
            //<p> element 1 </p><p> element 2 </p><p> element 3 </p>

            // liaison avec user
            $user = $users[rand(0, count($users)-1)];

            // retirer ->setSlug($slug)
            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(rand(40,200))
                ->setRooms(rand(1,5))
                ->setAuthor($user)
            ;

           

            // gestion des images de l'annonce (galerie)
            for($j = 1; $j <= rand(2,5); $j++)
            {
                $image = new Image();
                $image->setAd($ad)
                    ->setUrl("https://picsum.photos/id/".$j."/900")
                    ->setCaption($faker->sentence());
                $manager->persist($image);
            }

            // gestion des réservations
            for($b = 1; $b <= rand(0,10); $b++)
            {
                $booking = new Booking();
                $createdAt = $faker->dateTimeBetween('-6 months','-4 months');
                $startDate = $faker->dateTimeBetween('-3 months');
                $duration = rand(3,10);

                // comme c'est un dateTime
                $endDate = (clone $startDate)->modify("+".$duration." days");
                $amount = $ad->getPrice() * $duration;
                $comment = $faker->paragraph();
                $booker = $users[rand(0,count($users)-1)];

                $booking->setBooker($booker)
                    ->setAd($ad)
                    ->setStartDate($startDate)
                    ->setEndDate($endDate)
                    ->setCreatedAt($createdAt)
                    ->setAmount($amount)
                    ->setComment($comment)
                   ;
                 $manager->persist($booking);

                // gestion des commentaires
                $comment = new Comment();
                $comment->setContent($faker->paragraph())
                    ->setRating(rand(1,5))
                    ->setAuthor($booker)
                    ->setAd($ad);

                $manager->persist($comment);
            }
             $manager->persist($ad);
        }


        // $product = new Product();
        // $manager->persist($product);


        $manager->flush();
    }
}
