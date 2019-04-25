<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    public const USER_COUNT = 10;

    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::USER_COUNT; $i++) {
            $user = new User();
            $user->setEmail(sprintf('test%d@test-domain.com', $i));
            $user->setConfirmed(true);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                sprintf('test%d', $i)
            ));
            $manager->persist($user);
        }

        // Let's create one user with admin privileges
        $manager->persist($this->loadAdmin());

        $manager->flush();
    }

    private function loadAdmin(): User
    {
        $admin = new User();
        $admin->setEmail('admin@test-domain.com');
        $admin->setConfirmed(true);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'admin'
        ));
        $admin->addRole(User::ROLE_ADMIN);

        return $admin;
    }
}
