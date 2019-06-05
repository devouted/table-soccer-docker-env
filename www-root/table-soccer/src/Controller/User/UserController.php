<?php

namespace App\Controller\User;

use App\Entity\Tournament;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class UserController
 * @package App\Controller
 *
 * @Route("/user")
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{page}", name="user_index", methods={"GET"}, requirements={"page"="\d+"})
     *
     * @param UserRepository $userRepository
     * @param int $page
     *
     * @return Response
     */
    public function index(UserRepository $userRepository, int $page = 1): Response
    {
        return $this->render('user/index.html.twig', [
            'pagination' => $userRepository->findWithPagination($page),
        ]);
    }

    /**
     * @Route("/{id}/show", name="user_show", methods={"GET"})
     *
     * @param User $user
     *
     * @return Response
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param User $user
     *
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }
}
