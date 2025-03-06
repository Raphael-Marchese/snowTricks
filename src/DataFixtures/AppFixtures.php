<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Figure;
use App\Entity\Message;
use App\Entity\User;
use App\Service\CategoryNameEnum;
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

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('main');
        $user->setEmail('user@test.com');
        $password = $this->hasher->hashPassword($user, 'test');
        $user->setPassword($password);
        $manager->persist($user);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setEmail('user@example.com');
        $password = $this->hasher->hashPassword($user2, 'test');
        $user2->setPassword($password);
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('user');
        $user3->setEmail('user2@example.com');
        $password = $this->hasher->hashPassword($user3, 'test');
        $user3->setPassword($password);
        $manager->persist($user3);

        $user4 = new User();
        $user4->setUsername('user');
        $user4->setEmail('user3@example.com');
        $password = $this->hasher->hashPassword($user4, 'test');
        $user4->setPassword($password);
        $manager->persist($user4);

        $categories = [];
        $categoryNames = [
            CategoryNameEnum::GRABS->value => 'Grabs',
            CategoryNameEnum::ROTATIONS->value => 'Rotations',
            CategoryNameEnum::FLIPS->value => 'Flips',
            CategoryNameEnum::SLIDES->value => 'Slides',
            CategoryNameEnum::OFF_AXIS_ROTATIONS->value => 'Off Axis Rotation',
            CategoryNameEnum::ONE_FOOT_TRICKS->value => 'One foot tricks',
        ];

        foreach ($categoryNames as $key => $name) {
            $category = new Category();
            $category->name = CategoryNameEnum::from($key);
            $manager->persist($category);
            $categories[$key] = $category;
        }

        $manager->flush();

        $figure = new Figure();
        $figure->author = $user;
        $figure->name = 'indy';
        $figure->description =
            'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière';
        $figure->figureGroup = $categories['grabs'];
        $figure->createdAt = new \DateTimeImmutable();
        $manager->persist($figure);

        $users = [$user, $user2, $user3, $user4];

        $figuresData = [
            [
                'name' => 'Mute Grab',
                'description' => 'Saisie de la carre frontside de la planche entre les deux pieds, avec la main avant',
                'group' => CategoryNameEnum::GRABS->value,
                'messages' => [
                    'Super clean ce Mute Grab ! Bien exécuté !',
                    'Bien exécuté, continue !',
                    'Bravo pour ce Mute !'
                ]
            ],
            [
                'name' => 'Sad Grab',
                'description' => 'Saisie de la carre backside de la planche entre les deux pieds, avec la main avant',
                'group' => CategoryNameEnum::GRABS->value,
                'messages' => ['Un Sad Grab parfaitement stylé ! J’adore !', 'Génial !', 'Sad Grab impressionnant !']
            ],
            [
                'name' => 'Tail Grab',
                'description' => 'Saisie de la partie arrière de la planche avec la main arrière',
                'group' => CategoryNameEnum::GRABS->value,
                'messages' => [
                    'Bravo pour ce Tail Grab ! Impressionnant !',
                    'Super Tail Grab !',
                    'Un classique très bien exécuté !'
                ]
            ],
            [
                'name' => 'Nose Grab',
                'description' => 'Saisie de la partie avant de la planche avec la main avant',
                'group' => CategoryNameEnum::GRABS->value,
                'messages' => ['Ce Nose Grab est juste parfait !', 'Excellent Nose !', 'Bravo pour le contrôle !']
            ],
            [
                'name' => 'Japan Air',
                'description' => 'Saisie de l’avant de la planche, avec la main avant, du côté de la carre frontside',
                'group' => CategoryNameEnum::GRABS->value,
                'messages' => ['Quel Japan Air ! Tu as le style !', 'C’est magnifique !', 'Superbe Japan Air !']
            ],
            [
                'name' => '180 Frontside',
                'description' => 'Demi-tour orienté vers la carre backside',
                'group' => CategoryNameEnum::ROTATIONS->value,
                'messages' => ['Le 180 Frontside est super fluide ! Bravo !', 'Super propre !', 'Magnifique 180 !']
            ],
            [
                'name' => '360 Backside',
                'description' => 'Un tour complet orienté vers la carre frontside',
                'group' => CategoryNameEnum::ROTATIONS->value,
                'messages' => ['Wow, ce 360 Backside est vraiment propre !', 'Bien joué !', 'Un 360 parfait !']
            ],
            [
                'name' => 'Switch 540',
                'description' => 'Tour et demi en partant du côté non naturel du rideur',
                'group' => CategoryNameEnum::ROTATIONS->value,
                'messages' => [
                    'Le Switch 540 est tellement impressionnant !',
                    'Magnifique Switch 540 !',
                    'Très audacieux !'
                ]
            ],
            [
                'name' => 'Backflip',
                'description' => 'Rotation verticale en arrière',
                'group' => CategoryNameEnum::FLIPS->value,
                'messages' => ['Le Backflip, classique et toujours cool !', 'Très stylé !', 'Backflip réussi !']
            ],
            [
                'name' => 'Frontflip',
                'description' => 'Rotation verticale en avant',
                'group' => CategoryNameEnum::FLIPS->value,
                'messages' => [
                    'Superbe Frontflip, très fluide et bien contrôlé !',
                    'Beau travail !',
                    'Bravo pour ce Frontflip !'
                ]
            ],
            [
                'name' => 'Cork 720',
                'description' => 'Deux tours avec rotation désaxée en initiant un mouvement des épaules',
                'group' => CategoryNameEnum::OFF_AXIS_ROTATIONS->value,
                'messages' => [
                    'Le Cork 720 demande de l’audace, bien joué !',
                    'Parfaitement maîtrisé !',
                    'Cork 720 impressionnant !'
                ]
            ],
            [
                'name' => 'Rodeo Flip',
                'description' => 'Rotation désaxée avec un flip et un désaxage du buste',
                'group' => CategoryNameEnum::OFF_AXIS_ROTATIONS->value,
                'messages' => [
                    'Ce Rodeo Flip est tout simplement incroyable !',
                    'Un Rodeo fantastique !',
                    'Rodeo Flip bien contrôlé !'
                ]
            ],
            [
                'name' => 'Nose Slide',
                'description' => 'Glissade sur une barre avec l’avant de la planche',
                'group' => CategoryNameEnum::SLIDES->value,
                'messages' => ['Nose Slide maîtrisé ! Belle glissade !', 'Super Nose Slide !', 'Exécution parfaite !']
            ],
            [
                'name' => 'Tail Slide',
                'description' => 'Glissade sur une barre avec l’arrière de la planche',
                'group' => CategoryNameEnum::SLIDES->value,
                'messages' => ['Ton Tail Slide est bien enchaîné, stylé !', 'Très bon slide !', 'Génial Tail Slide !']
            ],
            [
                'name' => 'One Foot Tail Grab',
                'description' => 'Saisie de la partie arrière de la planche avec un pied décroché de la fixation',
                'group' => CategoryNameEnum::ONE_FOOT_TRICKS->value,
                'messages' => [
                    'Le One Foot Tail Grab, pas pour les amateurs ! Bravo !',
                    'Très audacieux !',
                    'Bien exécuté, bravo !'
                ]
            ],
        ];

        foreach ($figuresData as $data) {
            $figure = new Figure();

            $figure->author = $users[array_rand($users)];

            $figure->name = $data['name'];
            $figure->description = $data['description'];
            $figure->figureGroup = $categories[$data['group']];
            $figure->createdAt = new \DateTimeImmutable();
            $manager->persist($figure);

            foreach ($data['messages'] as $msgContent) {
                $message = new Message();

                $message->author = $users[array_rand($users)];

                $message->figure = $figure;
                $message->content = $msgContent;
                $message->createdAt = (new \DateTimeImmutable())->modify('+1 hour');
                $manager->persist($message);
            }
        }

        $manager->flush();
    }
}
