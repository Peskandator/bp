<?php

declare(strict_types=1);

namespace App\Components\Breadcrumb;

use Nette\Application\UI\Link;

class BreadcrumbItem
{
    public string $text;
    public ?Link $link;

    public function __construct(
        string $text,
        ?Link $link
    ) {
        $this->text = $text;
        $this->link = $link;
    }
}
