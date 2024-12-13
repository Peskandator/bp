<?php

namespace App\Presenters;

use App\Metrics\HttpMetricsMiddleware;
use Nette\Application\UI\Presenter;

abstract class BasePresenter extends Presenter
{
    private HttpMetricsMiddleware $httpMetricsMiddleware;

    public function injectBaseDeps(
        HttpMetricsMiddleware $httpMetricsMiddleware
    ): void
    {
        $this->httpMetricsMiddleware = $httpMetricsMiddleware;
    }

    protected function startup(): void
    {
        parent::startup();
        $this->httpMetricsMiddleware->__invoke();
    }
}
