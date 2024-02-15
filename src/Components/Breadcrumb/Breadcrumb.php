<?php

declare(strict_types=1);

namespace App\Components\Breadcrumb;

use Nette\Application\UI\Control;

class Breadcrumb extends Control
{
    private array $items = [];

    public function addItem(BreadcrumbItem $item): void
    {
        $this->items[] = $item;
    }

    public function render(): void
    {
        $this->template->setFile(__DIR__ . '/templates/breadcrumb.latte');
        $this->template->items = $this->items;
        $this->template->render();
    }
}
