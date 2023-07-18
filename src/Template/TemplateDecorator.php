<?php

namespace App\Template;

use App\Majetek\Latte\Filters\YearsTextFilter;
use App\Utils\SrcDir;
use Nette\Bridges\ApplicationLatte\Template;

class TemplateDecorator
{
    public function __construct(
        private bool $debugMode,
        private SrcDir $srcDir
    ) {
    }

    public function onCreate(Template $template)
    {
        $template->debugMode = $this->debugMode;
        $template->srcDir = $this->srcDir->getDir();
        $template->layout = $this->srcDir->getDir() . '/Presenters/templates/@layout.latte';
        $template->baselayoutPath = $this->srcDir->getDir() . '/Presenters/templates/layout-base.latte';
        $template->adminlayoutPath = $this->srcDir->getDir() . '/Presenters/templates/layout-admin.latte';
        $template->addFilter('yearsText', new YearsTextFilter());
    }
}
