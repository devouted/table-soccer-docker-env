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


        $manager->flush();
    }
}
