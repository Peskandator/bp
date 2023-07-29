<?php

namespace App\Components\AdminMenu;

use Nette\Utils\Arrays;

class MenuSection
{
    private string $text;
    private array $items;
    private bool $active;

    public function __construct(string $text, array $items)
    {
        $this->text = $text;
        $this->items = $items;

        $this->setActive($items);
    }

    private function setActive(array $items): void
    {
        $this->active = Arrays::some($items, static function (MenuItem $item) {return $item->isActive();});
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function getTotranslate(): bool
    {
        return $this->toTranslate;
    }
}
