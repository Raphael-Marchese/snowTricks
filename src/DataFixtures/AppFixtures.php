<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        // Création d'un utilisateur
        $user = new User();
        $user->setUsername('main');
        $user->setEmail('user@test.com');
        $password = $this->hasher->hashPassword($user, 'test');
        $user->setPassword($password);
        $manager->persist($user);

        $user2 = $user = new User();
        $user->setUsername('user');
        $user->setEmail('user@example.com');
        $password = $this->hasher->hashPassword($user, 'test');
        $user->setPassword($password);
        $manager->persist($user);

        // Création de figures
        $figure = new Figure();
        $figure->setAuthor($user); // Associer à l'utilisateur créé
        $figure->setName('indy');
        $figure->setDescription('Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière');
        $figure->setFigureGroup('grabs');
        $figure->setCreatedAt(new \DateTimeImmutable());
        $manager->persist($figure);

        // Création de messages pour chaque figure
        for ($j = 0; $j < 3; $j++) {
            $message = new Message();
            $message->setAuthor($user2); // Associer à l'utilisateur
            $message->setFigure($figure); // Associer à la figure
            $message->setContent('wouahou trop bien cette figure!');
            $message->setCreatedAt((new \DateTimeImmutable())->modify('+1 hour'));
            $manager->persist($message);
        }


        $manager->flush();
    }
}
