<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class TournamentController
 * @package App\Controller
 *
 * @IsGranted("ROLE_USER")
 */
class TournamentController extends AbstractController
{
    /**
     * @Route("/tournament", name="tournament")
     */
    public function index()
    {
        return $this->render('tournament/index.html.twig', [
            'controller_name' => 'TournamentController',
        ]);
    }
}
