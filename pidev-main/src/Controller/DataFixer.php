<?php

namespace App\Controller;

use App\Entity\Users;
use App\Entity\Utilisateur;
use App\Enum\UsersRoles;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class DataFixer extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new Utilisateur();
        $user1->setNom("admin");
        $user1->setPrenom("admin");
        $user1->setAdresse("aa");
        $user1->setTelephone("20104060");
        $user1->setEmail("admin@admin.com");
        $user1->setImageName("téléchargement.png");

        $hashedPassword = $this->passwordHasher->hashPassword($user1, "admin");
        $user1->setMotDePasse($hashedPassword);

        $user1->setRole(UsersRoles::ADMIN);
        $user1->setGender("anonyme");
        $manager->persist($user1);
        $manager->flush();
    }
}
