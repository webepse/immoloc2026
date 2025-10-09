<?php

namespace App\DataFixtures;

use App\Entity\Ad;
use Faker\Factory;
use Cocur\Slugify\Slugify;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create("fr_FR");
        $slugify = new Slugify();

        for($i = 1; $i <= 30; $i++)
        {
            $ad = new Ad();
            $title = $faker->sentence();
            $slug = $slugify->slugify($title);
            $coverImage = "https://picsum.photos/id/".$i."/1000/350";
            $introduction = $faker->paragraph(2);
            $content = '<p>'.join('</p><p>',$faker->paragraphs(5)).'</p>';
            // ["element 1","element 2","element 3"]
            //<p> element 1 </p><p> element 2 </p><p> element 3 </p>

            $ad->setTitle($title)
                ->setCoverImage($coverImage)
                ->setIntroduction($introduction)
                ->setContent($content)
                ->setPrice(rand(40,200))
                ->setRooms(rand(1,5))
                ->setSlug($slug);

            $manager->persist($ad);

        }


        // $product = new Product();
        // $manager->persist($product);


        $manager->flush();
    }
}
