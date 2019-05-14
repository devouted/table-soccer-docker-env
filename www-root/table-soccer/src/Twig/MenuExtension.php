<?php


namespace App\Twig;


use App\Service\MenuBuilder\MenuBuilder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    /** @var MenuBuilder */
    private $menuBuilder;

    public function __construct(MenuBuilder $menuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('renderMenu', [$this, 'renderMenu'], ['is_safe' => ['html']])
        ];
    }

    public function renderMenu() {
        return $this->menuBuilder->renderMenu();
    }


}