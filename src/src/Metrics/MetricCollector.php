<?php

declare(strict_types=1);

namespace App\Metrics;

use Artprima\PrometheusMetricsBundle\Metrics\RequestMetricsCollectorInterface;
use Artprima\PrometheusMetricsBundle\Metrics\TerminateMetricsCollectorInterface;
use Prometheus\CollectorRegistry;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class MetricCollector implements RequestMetricsCollectorInterface, TerminateMetricsCollectorInterface
{
    /**
     * @var string
     */
    private $namespace;

    /**
     * @var CollectorRegistry
     */
    private $collectionRegistry;

    public function init(string $namespace, CollectorRegistry $collectionRegistry): void
    {
        $this->namespace = $namespace;
        $this->collectionRegistry = $collectionRegistry;
    }

    private function incRequestsTotal(?string $method = null, ?string $route = null): void
    {
        $counter = $this->collectionRegistry->getOrRegisterCounter(
            $this->namespace,
            'http_requests_total',
            'Total request count',
            ['method', 'uri'],
        );

        if (null !== $method && null !== $route) {
            $counter->inc([$method, $route]);
        }
    }

    public function collectRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $requestMethod = $request->getMethod();
        $requestUrl = $request->getPathInfo();

        if ('OPTIONS' === $requestMethod) {
            return;
        }

        $this->incRequestsTotal($requestMethod, $requestUrl);

        //todo: инкапсулировать на ивенты
        if ($requestMethod === 'POST' && $requestUrl === '/expense/') {
            $this->incExpenseAdditionTotal($request->get('currency') ?? 'RUR');
        }
    }

    private function incExpenseAdditionTotal(?string $currency): void
    {
        $this->collectionRegistry->getOrRegisterCounter(
            $this->namespace,
            'expense_created_total',
            'Total expenses created',
            ['currency'],
        )->inc([$currency]);
    }

    public function collectResponse(TerminateEvent $event): void
    {
        return;
    }
}