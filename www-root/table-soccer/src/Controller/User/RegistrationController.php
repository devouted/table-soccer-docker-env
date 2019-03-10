<?php

namespace App\Controller\User;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class RegistrationController
 * @package App\Controller
 *
 * @Route("/register")
 */
class RegistrationController extends AbstractController
{
    /**
     * @Route("", name="registration_register")
     *
     * @param Request $request
     * @param UserManager $userManager
     *
     * @return Response
     *
     */
    public function register(Request $request, UserManager $userManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userManager->save($user);

            // TODO: Send email to user with link to registration confirmation action

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{confirmationToken}", name="registration_confirm")
     *
     * @param UserManager $userManager
     * @param User $user
     *
     * @return Response
     */
    public function confirm(UserManager $userManager, ?User $user): Response
    {
        if(null !== $user) {
            $userManager->confirm($user);
        }

        return $this->redirectToRoute('app_login');
    }
}
