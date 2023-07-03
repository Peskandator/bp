<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
	use Nette\StaticClass;

	public static function createRouter(): RouteList
	{
		$router = new RouteList;

        $router->addRoute('admin','Admin:Dashboard:default');
        $router->addRoute('admin/<presenter>/<action>', [
            'module' => 'Admin',
            'presenter' => 'Admin:Dashboard:default'
        ]);
        $router->addRoute('<presenter>/<action>[/<id>]', 'Home:default');

        return $router;
	}
}
