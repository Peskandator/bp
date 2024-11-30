<?php

namespace App\Presenters;

use Nette\Application\Responses\TextResponse;
use Nette\Application\UI\Presenter;
use App\Metrics\HttpMetricsMiddleware;

class MetricsPresenter extends Presenter
{
    private HttpMetricsMiddleware $metrics;

    public function __construct(
        HttpMetricsMiddleware $metrics
    )
    {
        parent::__construct();
        $this->metrics = $metrics;
    }

    public function actionDefault(): void
    {
        $this->getHttpResponse()->setContentType('text/plain');
        $this->sendResponse(new TextResponse($this->metrics->renderMetrics()));
    }
}
