<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Metrics\HttpMetricsMiddleware;
use Nette;
use Nette\Application\Responses;
use Nette\Http;
use Tracy\ILogger;


final class ErrorPresenter implements Nette\Application\IPresenter
{
	use Nette\SmartObject;

	public function __construct(
		private ILogger $logger,
        private HttpMetricsMiddleware $httpMetricsMiddleware
	) {
	}


	public function run(Nette\Application\Request $request): Nette\Application\Response
	{
		$exception = $request->getParameter('exception');


		if ($exception instanceof Nette\Application\BadRequestException) {

            $this->httpMetricsMiddleware->invokeFromBadRequestException($exception->getCode());

			[$module, , $sep] = Nette\Application\Helpers::splitName($request->getPresenterName());
			return new Responses\ForwardResponse($request->setPresenterName($module . $sep . 'Error4xx'));
		}

		$this->logger->log($exception, ILogger::EXCEPTION);
		return new Responses\CallbackResponse(function (Http\IRequest $httpRequest, Http\IResponse $httpResponse): void {
            $this->httpMetricsMiddleware->__invoke($httpResponse);

			if (preg_match('#^text/html(?:;|$)#', (string) $httpResponse->getHeader('Content-Type'))) {
				require __DIR__ . '/templates/Error/500.phtml';
			}
		});
	}
}
