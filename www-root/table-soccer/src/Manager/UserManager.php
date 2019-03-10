<?php

namespace App\Manager;

use App\Entity\User;
use App\Exception\ConfirmationTokenExpiredException;
use App\Service\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserManager
{
    public const REGISTRATION_TOKEN_EXPIRATION_TIME = 86400;
    public const RESET_PASSWORD_TOKEN_EXPIRATION_TIME = 3600;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, TokenGenerator $tokenGenerator, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->tokenGenerator = $tokenGenerator;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function save(User $user): void
    {
        if (null === $user->getId()) {
            $user->setConfirmationToken($this->tokenGenerator->generateToken());
            $user->setConfirmationTokenGeneratedAt(new \DateTime());
            $this->entityManager->persist($user);
        }

        if($user->getPlainPassword()) {
            $user->setPassword(
                $this->passwordEncoder->encodePassword(
                    $user,
                    $user->getPlainPassword()
                )
            );
            // Let's clear plain password field of user entity
            $user->eraseCredentials();
        }

        $this->entityManager->flush();
    }

    public function confirm(User $user): void
    {
        if ((new \DateTime())->getTimestamp() - $user->getConfirmationTokenGeneratedAt()->getTimestamp() > self::REGISTRATION_TOKEN_EXPIRATION_TIME) {
            throw new ConfirmationTokenExpiredException();
        }
        $user->setConfirmed(true);
        $user->eraseConfirmationData();

        $this->entityManager->flush();
    }

    public function sendResetPasswordEmail(User $user): void
    {
        $user->setConfirmationToken($this->tokenGenerator->generateToken());
        $user->setConfirmationTokenGeneratedAt(new \DateTime());

        // TODO: Send email to user with link to reset password action
        $this->entityManager->flush();
    }

    public function changePassword(User $user, string $password)
    {
        if ((new \DateTime())->getTimestamp() - $user->getConfirmationTokenGeneratedAt()->getTimestamp() > self::RESET_PASSWORD_TOKEN_EXPIRATION_TIME) {
            throw new ConfirmationTokenExpiredException();
        }

        $user->eraseConfirmationData();
        $user->setPassword($this->passwordEncoder->encodePassword(
            $user,
            $password
        ));

        $this->entityManager->flush();
    }
}
