<?php

declare(strict_types=1);

namespace App\Components\Breadcrumb;

interface BreadcrumbFactoryInterface
{
    public function create(): Breadcrumb;
}
