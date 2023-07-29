<?php

namespace App\Components\AdminMenu;

use Nette\Application\UI\Link;
use Nette\Utils\Arrays;

class MenuItem
{
    private string $text;
    private ?Link $link;
    private array $children;
    private bool $active;

    public function __construct(
        string $text,
        ?Link $link,
        array $children,
        ?callable $isLinkCurrent,
    ) {
        $this->text = $text;
        $this->link = $link;
        $this->children = $children;
        $this->setActive($link, $isLinkCurrent, $children);
    }

    private function setActive(?Link $link, ?callable $isLinkCurrent, array $children): void
    {
        $this->active = ($link && $isLinkCurrent && $isLinkCurrent($link))
            || Arrays::some($children, static function (MenuItem $child) {return $child->isActive();})
        ;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    public function isActive(): bool
    {
        return $this->active;
    }
}
