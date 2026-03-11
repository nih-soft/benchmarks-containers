<?php

declare(strict_types=1);

namespace Bench;

use Bench\Builder\LaravelBuilder;
use Bench\Builder\NihBuilder;
use Bench\Builder\PhpDiBuilder;
use Bench\Builder\SpiralBuilder;
use Bench\Builder\SymfonyCompiledBuilder;
use Bench\Builder\SymfonyRuntimeBuilder;
use Bench\Builder\YiiBuilder;
use Bench\Stub\ServiceWithDependency;
use Bench\Stub\SimpleService;
use DI\Container as PhpDiContainer;
use Illuminate\Container\Container as LaravelContainer;
use NIH\Container\Arg as NihArg;
use NIH\Container\Container as NihContainer;
use NIH\Container\ContainerConfig;
use PhpBench\Attributes\BeforeMethods;
use PhpBench\Attributes\Iterations;
use PhpBench\Attributes\Revs;
use PhpBench\Attributes\Warmup;
use Psr\Container\ContainerInterface;
use Spiral\Core\Container as SpiralContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

use Yiisoft\Di\Container as YiiContainer;

use function DI\autowire as php_di_autowire;
use function DI\create as php_di_create;

#[Revs(5000), Warmup(5), Iterations(30)]
#[BeforeMethods('prepare')]
final class ServiceWithAutowireBench implements BenchInterface
{
    private SymfonyContainerBuilder $symfonyRuntime;
    private ContainerInterface $symfonyCompiled;
    private YiiContainer $yii;
    private LaravelContainer $laravel;
    private SpiralContainer $spiral;
    private PhpDiContainer $phpdi;
    private NihContainer $nihAuto;
    private NihContainer $nihManual;

    public function prepare(): void
    {
        $this->symfonyRuntime = SymfonyRuntimeBuilder::build(
            context: __METHOD__,
            build: function (SymfonyContainerBuilder $app) {
                $app->addDefinitions([
                    SimpleService::class => new Definition(SimpleService::class),
                    ServiceWithDependency::class => (new Definition(ServiceWithDependency::class))
                        ->setAutowired(true)
                        ->setPublic(true)
                ]);
            },
        );

        $this->symfonyCompiled = SymfonyCompiledBuilder::build(
            context: __METHOD__,
            build: function (SymfonyContainerBuilder $app) {
                $app->addDefinitions([
                    SimpleService::class => new Definition(SimpleService::class),
                    ServiceWithDependency::class => (new Definition(ServiceWithDependency::class))
                        ->setAutowired(true)
                        ->setPublic(true)
                ]);
            },
        );

        $this->yii = YiiBuilder::build(
            context: __METHOD__,
            build: function (): array {
                return [
                    SimpleService::class => SimpleService::class,
                    ServiceWithDependency::class => ServiceWithDependency::class,
                ];
            }
        );

        $this->laravel = LaravelBuilder::build(
            context: __METHOD__,
            build: function (LaravelContainer $app): void {
                $app->singleton(SimpleService::class);
                $app->singleton(ServiceWithDependency::class);
            }
        );

        $this->spiral = SpiralBuilder::build(
            context: __METHOD__,
            build: function (SpiralContainer $app): void {
                $app->bindSingleton(SimpleService::class, SimpleService::class);
                $app->bindSingleton(ServiceWithDependency::class, ServiceWithDependency::class);
            }
        );

        $this->phpdi = PhpDiBuilder::build(
            context: __METHOD__,
            build: function (): array {
                return [
                    SimpleService::class => php_di_create(SimpleService::class),
                    ServiceWithDependency::class => php_di_autowire(ServiceWithDependency::class),
                ];
            }
        );

        $this->nihAuto = NihBuilder::build(
            context: __METHOD__,
            shared: true,
            build: static function (ContainerConfig $config): void {
            }
        );

        $this->nihManual = NihBuilder::build(
            context: __METHOD__,
            shared: true,
            build: static function (ContainerConfig $config): void {
                $config->manual(SimpleService::class)->to(SimpleService::class);
                $config->manual(ServiceWithDependency::class)
                    ->to(ServiceWithDependency::class)
                    ->argument('dependency', NihArg::get(SimpleService::class));
            }
        );
    }

    public function benchSymfonyRuntime(): void
    {
        $instance = $this->symfonyRuntime->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }

    public function benchSymfonyCompiled(): void
    {
        $instance = $this->symfonyCompiled->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }

    public function benchYii(): void
    {
        $instance = $this->yii->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }

    public function benchLaravel(): void
    {
        $instance = $this->laravel->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }

    public function benchSpiral(): void
    {
        $instance = $this->spiral->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }

    public function benchPhpDi(): void
    {
        $instance = $this->phpdi->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }

    public function benchNihAuto(): void
    {
        $instance = $this->nihAuto->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }

    public function benchNihManual(): void
    {
        $instance = $this->nihManual->get(ServiceWithDependency::class);

        assert($instance instanceof ServiceWithDependency);
        assert($instance->dependency instanceof SimpleService);
    }
}
