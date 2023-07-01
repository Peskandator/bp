<?php

namespace App\DI;

use Nette\DI\CompilerExtension;

class AppExtension extends CompilerExtension
{
    public function beforeCompile()
    {
        $builder = $this->getContainerBuilder();

        if ($builder->hasDefinition('latte.templateFactory')) {
            $builder
                ->getDefinition('latte.templateFactory')
                ->addSetup('$service->onCreate[] = ?', [['@templateDecorator', 'onCreate']])
            ;
        }
    }
}
