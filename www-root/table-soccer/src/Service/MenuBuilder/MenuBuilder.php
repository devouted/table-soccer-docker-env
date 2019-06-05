<?php

namespace App\Service\MenuBuilder;


use App\Entity\User;
use App\Service\MenuBuilder\MenuElement;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Twig\Environment;

class MenuBuilder
{
    /** @var RouterInterface */
    private $router;

    /** @var RequestStack */
    private $requestStack;

    /** @var Environment $templating */
    private $templating;

    /** @var TokenStorageInterface */
    private $tokenStorage;

    public function __construct(RouterInterface $router, RequestStack $requestStack, Environment $templating, TokenStorageInterface $tokenStorage)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->templating = $templating;
        $this->tokenStorage = $tokenStorage;
    }

    public function renderMenu()
    {
        $currentRoute = $this->requestStack->getCurrentRequest()->get('_route');
        if($this->tokenStorage->getToken()->getUser() instanceof User) {
            /** @var MenuElement[] $mainMenu */
            $mainMenu = [
                new MenuElement(
                    $currentRoute === 'team_index',
                    $this->router->generate('team_index'),
                    'team.index.title'
                ),
                new MenuElement(
                    $currentRoute === 'tournament_index',
                    $this->router->generate('tournament_index'),
                    'tournament.index.title'
                ),
                new MenuElement(
                    $currentRoute === 'user_index',
                    $this->router->generate('user_index'),
                    'user.index.title'
                ),
                new MenuElement(
                    $currentRoute === 'app_logout',
                    $this->router->generate('app_logout'),
                    'app.logout'
                ),
            ];
        } else {
            /** @var MenuElement[] $mainMenu */
            $mainMenu = [
                new MenuElement(
                    $currentRoute === 'app_login',
                    $this->router->generate('app_login'),
                    'app.login'
                ),
                new MenuElement(
                    $currentRoute === 'app_register',
                    $this->router->generate('app_register'),
                    'app.register'
                ),
            ];
        }

        return $this->templating->render(
            'menuBuilder/main_menu.html.twig', [
                'menuElements' => $mainMenu
            ]
        );
    }
}