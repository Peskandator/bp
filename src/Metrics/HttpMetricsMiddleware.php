<?php

declare(strict_types=1);

namespace App\Metrics;

use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;
use Prometheus\RenderTextFormat;
use Nette\Http\IResponse;
use Nette\Http\IRequest;

class HttpMetricsMiddleware
{
    private CollectorRegistry $registry;

    public function __construct()
    {
        $adapter = new Redis(['host' => 'redis', 'port' => 6379]);
        $this->registry = new CollectorRegistry($adapter);
    }

    public function __invoke(IRequest $request, IResponse $response): void
    {
        $histogram = $this->registry->getOrRegisterHistogram(
            'http',
            'status_codes',
            'Histogram of HTTP response status codes',
            ['code']
        );

        $histogram->observe(1, [strval($response->getCode())]);

//        return $response;
    }

    public function renderMetrics(): string
    {
        $renderer = new RenderTextFormat();
        return $renderer->render($this->registry->getMetricFamilySamples());
    }
}
