<?php


namespace App\Service\MenuBuilder;


class MenuElement
{
    /** @var string|null */
    private $route;

    /** @var bool */
    private $active;

    /** @var MenuElement[] */
    private $nestedElements;

    /** @var string|null */
    private $label;

    public function __construct(bool $active, ?string $route = null, ?string $label = null)
    {
        $this->route = $route;
        $this->active = $active;
        $this->label = $label ?: $route;
    }

    public function addNestedElement(MenuElement $menuElement)
    {
        $this->nestedElements[] = $menuElement;
    }

    /**
     * @return string|null
     */
    public function getRoute(): ?string
    {
        return $this->route;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @return MenuElement[]
     */
    public function getNestedElements(): array
    {
        return $this->nestedElements;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @return string|null
     */
    public function getCssClass(): ?string
    {
        return $this->cssClass;
    }
}