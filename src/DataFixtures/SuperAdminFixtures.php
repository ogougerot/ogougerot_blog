<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SuperAdminFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    )
    {
        
    }
    
    public function load(ObjectManager $manager): void
    {
        $superAdmin = $this->createSuperAdmin();

        $manager->persist($superAdmin);
        $manager->flush();
    }
    /**  Création du superAdmin
    * @return User
    */
    private function createSuperAdmin(): User
    {
        $superAdmin = new User();

        $passwordHashed = $this->hasher->hashPassword($superAdmin, "azerty1234A*");

        $superAdmin->setUserName('ogougerot');
        $superAdmin->setEmail('olivier.gougerot@outlook.fr');
        $superAdmin->setRoles(['ROLE_SUPER_ADMIN', 'ROLE_ADMIN', 'ROLE_USER']);
        $superAdmin->setIsVerified(true);
        $superAdmin->setPassword($passwordHashed);
        $superAdmin->setCreatedAt(new DateTimeImmutable());
        $superAdmin->setVerifiedAt(new DateTimeImmutable());
        $superAdmin->setUdpatedAt(new DateTimeImmutable());

        return $superAdmin;
    }
}
