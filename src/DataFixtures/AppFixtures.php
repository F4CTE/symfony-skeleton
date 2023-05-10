<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public const NB_CATEGORIES = 15;
    public const NB_ARTICLES = 150;

    public function __construct(private UserPasswordHasherInterface $passwordHasherAwareInterface)
    {
    }
    

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $categories = [];

        $user = new User();
        $user->setEmail('bobby@boby.bob');  
        $user->setPassword($this->passwordHasherAwareInterface->hashPassword($user, 'bobby'))
            ->setRoles(['ROLE_ADMIN']);
        
        $manager->persist($user);

        for ($i = 0; $i < self::NB_CATEGORIES; $i++) {
            $category = new Category();
            $category->setName($faker->unique()->word())
                ->setDescription($faker->text(200));
            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i < self::NB_ARTICLES; $i++) {
            $article = new Article();
            $article->setTitle($faker->words(nb: 6, asText: true))
                ->setContent($faker->text(maxNbChars:200))
                ->setDateCreated($faker->dateTimeBetween(startDate:'-2 years'))
                ->setVisible($faker->boolean(chanceOfGettingTrue:90))
                ->setCategory($faker->randomElement(array: $categories))
                ->setAuthor($user);
            $manager->persist($article);
        }

        $manager->flush();
    }
}