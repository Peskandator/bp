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
        $statusCode = $response->getCode();

        $counter = $this->registry->getOrRegisterCounter(
            'http',
            'status_codes_total',
            'Total number of HTTP responses by status code',
            ['code']
        );

        $counter->inc([$statusCode]);
    }

    public function renderMetrics(): string
    {
        $renderer = new RenderTextFormat();
        return $renderer->render($this->registry->getMetricFamilySamples());
    }
}
