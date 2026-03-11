<?php

declare(strict_types=1);

namespace Bench;

interface BenchInterface
{
    public function benchSymfonyRuntime(): void;

    public function benchSymfonyCompiled(): void;

    public function benchYii(): void;

    public function benchLaravel(): void;

    public function benchSpiral(): void;

    public function benchPhpDi(): void;

    public function benchNihAuto(): void;

    public function benchNihManual(): void;
}
