<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Category;
use App\Entity\Food;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    // Tableau de domaines mails
    private $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com'];
    // Tableau de prénoms inspirés de personnages de films
    private $firstNames = ['harry', 'hermione', 'ron', 'gandalf', 'frodo', 'sam', 'legolas', 'aragorn', 'boromir', 'gimli'];
    // Tableau de noms inspirés de personnages de films
    private $lastNames = ['potter', 'granger', 'weasley', 'grey', 'gandalf', 'gamgee', 'greenleaf', 'elessar', 'parker', 'johnson'];

    public function load(ObjectManager $manager): void
    {
        // Creation de 5 utilisateurs normaux
        for ($i = 0; $i < 5; $i++) {
            $user = new User();
            $user->setEmail($this->firstNames[array_rand($this->firstNames)] . "$i" . '.' . $this->lastNames[array_rand($this->lastNames)] . '@' . $this->domains[array_rand($this->domains)]);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $user->setRoles(["ROLE_USER"]);
            $manager->persist($user);
        }

        // Creation de 5 utilisateurs admin
        for ($i = 0; $i < 5; $i++) {
            $userAdmin = new User();
            $userAdmin->setEmail("$i" . '.' . $this->lastNames[array_rand($this->lastNames)] . '@' . $this->domains[array_rand($this->domains)]);
            $userAdmin->setPassword($this->userPasswordHasher->hashPassword($userAdmin, "password"));
            $userAdmin->setRoles(["ROLE_ADMIN"]);
            $manager->persist($userAdmin);
        }

        // Creation de 5 catégories
        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setName('Category ' . $i);
            $category->setFeatured(true);
            $category->setActive(true);
            $manager->persist($category);
            // On sauvegarde la catégorie créé dans un tableau.
            $listCategory[] = $category;
        }

        // Creation de 10 plats
        for ($i = 0; $i < 10; $i++) {
            $food = new Food();
            $food->setName('Food ' . $i);
            $food->setDescription('Description ' . $i);
            $food->setPrice(mt_rand(10, 100));
            $food->setImage('food' . $i . '.jpg');
            $food->setFeatured(true);
            $food->setActive(true);
            // set random category
            $food->setCategory($listCategory[array_rand($listCategory)]);
            $manager->persist($food);
        }

        $manager->flush();
    }
}
