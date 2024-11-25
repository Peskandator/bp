<?php

declare(strict_types=1);

namespace App;

use App\Metrics\HttpMetricsMiddleware;
use Exception;
use Nette\Bootstrap\Configurator;
use Nette\Http\Request;
use Nette\Http\Response;
use Redis;

class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator;
		$appDir = dirname(__DIR__);

        $root = dirname(__DIR__);
        $configurator->setDebugMode(true);
        $configurator->addParameters($parameters = [
            'rootDir' => $root,
            'publicDir' => $root . '/www',
            'srcDir' => $root . '/src',
            'varDir' => $root . '/var',
            'logDir' => $root . '/var/log',
            'tempDir' => $root . '/var/temp',
        ]);

		//$configurator->setDebugMode('secret@23.75.345.200'); // enable for your remote IP

        $configurator->enableTracy($parameters['logDir']);
        $configurator->setTimeZone('Europe/Prague');
        $configurator->setTempDirectory($parameters['tempDir']);

		$configurator->createRobotLoader()
            ->addDirectory($parameters['srcDir'])
            ->addDirectory($parameters['tempDir'] . '/proxies')
            ->register()
        ;

		$configurator->addConfig($appDir . '/config/common.neon');
		$configurator->addConfig($appDir . '/config/services.neon');
		$configurator->addConfig($appDir . '/config/local.neon');


//        $redis = new Redis();
//        try {
//            $redis->connect('redis', 6379); // 'redis' odpovídá názvu služby v docker-compose.yml
//            bdump("Připojení k Redis bylo úspěšné!");
//        } catch (Exception $e) {
//            bdump("Nelze se připojit k Redis: " . $e->getMessage());
//        }

        $container = $configurator->createContainer();

        /** @var Request $httpRequest */
        $httpRequest = $container->getService('http.request');
        /** @var Response $httpResponse */
        $httpResponse = $container->getService('http.response');

        $httpMetricsMiddleware = $container->getByType(HttpMetricsMiddleware::class);
        $httpMetricsMiddleware->__invoke($httpRequest, $httpResponse);

		return $configurator;
	}
}
