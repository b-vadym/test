<?php

declare(strict_types=1);

namespace App\Sentry;

use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use Sentry\Tracing\GuzzleTracingMiddleware;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SentryPlugin extends Bundle implements PluginInterface
{
    public function getPluginName(): string
    {
        return 'sentry';
    }

    public function addConfiguration(ArrayNodeDefinition $pluginNode): void
    {
    }

    public function loadForClient(
        array $config,
        ContainerBuilder $container,
        string $clientName,
        Definition $handler
    ): void {
        $middleware = new Definition(GuzzleTracingMiddleware::class);
        $middleware->setFactory([GuzzleTracingMiddleware::class, 'trace']);
        $middleware->setPublic(true);

        // Register Middleware as a Service
        $middlewareServiceName = sprintf('guzzle_bundle_magic_header_plugin.middleware.sentry.%s', $clientName);
        $container->setDefinition($middlewareServiceName, $middleware);

        // Inject this service to given Handler Stack
        $middlewareExpression = new Expression(sprintf('service("%s")', $middlewareServiceName));
        $handler->addMethodCall('unshift', [$middlewareExpression]);
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
    }
}
