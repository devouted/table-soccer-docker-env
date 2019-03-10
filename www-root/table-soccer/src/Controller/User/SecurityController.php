<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Exception\ConfirmationTokenExpiredException;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     *
     * @param AuthenticationUtils $authenticationUtils
     *
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
    }

    /**
     * @Route("/reset-password", name="app_reset_password")
     *
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return Response
     */
    public function resetPassword(Request $request, UserManager $userManager): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);
        $data = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository = $this->getDoctrine()->getRepository(User::class);
            /** @var User $user */
            $user = $userRepository->findOneBy(['email' => $data['email']]);
            if (null !== $user && $user->isConfirmed()) {
                $userManager->sendResetPasswordEmail($user);
            }
        }

        return $this->render('security/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView(),
            'mailSent' => $form->isSubmitted()
        ]);
    }

    /**
     * @Route("/change-password/{confirmationToken}", name="app_change_password")
     *
     * @param Request $request
     * @param UserManager $userManager
     * @param string $confirmationToken
     *
     * @return Response
     */
    public function changePassword(Request $request, UserManager $userManager, string $confirmationToken): Response
    {
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        /** @var User|null $user */
        $user = $userRepository->findOneBy(['confirmationToken' => $confirmationToken]);

        $form = $this->createForm(ChangePasswordFormType::class);
        if (null === $user) {
            return $this->render('security/change_password.html.twig', [
                'changePasswordForm' => $form->createView(),
                'tokenError' => 'security.error.token.notFound'
            ]);
        }
        $form->handleRequest($request);

        $data = $form->getData();
        if ($form->isSubmitted() && $form->isValid()) {
            if (null !== $user) {
                try {
                    $userManager->changePassword($user, $data['plainPassword']);
                    return $this->redirectToRoute('app_login');
                } catch (ConfirmationTokenExpiredException $e) {
                    return $this->render('security/change_password.html.twig', [
                        'changePasswordForm' => $form->createView(),
                        'tokenError' => 'security.error.token.expired'
                    ]);
                }
            }

        }

        return $this->render('security/change_password.html.twig', [
            'changePasswordForm' => $form->createView(),
        ]);
    }
}
