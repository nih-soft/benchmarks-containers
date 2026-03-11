# Containers benchmarks

This project compares the performance of several PHP dependency injection containers on PHP 8.5.

```bash
composer bench
```

Benchmarks currently include Symfony (runtime and compiled), Yii, Laravel, Spiral, PHP-DI, and NIH Container in two modes:

- `benchNihAuto`: automatic dependency resolution
- `benchNihManual`: manual service definitions

Current benchmark settings: `revs=5000`, `warmup=5`, `iterations=30`.

## Results


### NonSharedServiceCreationByNameBench

Create service on every call.

```php
$app->get(Service::class); // Service#1
$app->get(Service::class); // Service#2
```

```bash
NonSharedServiceCreationByNameBench
+----------------------+------+-----+----------+---------+---------+
| subject              | revs | its | mem_peak | mode    | rstdev  |
+----------------------+------+-----+----------+---------+---------+
| benchSymfonyRuntime  | 5000 | 30  | 2.558mb  | 1.132μs | ±4.90%  |
| benchSymfonyCompiled | 5000 | 30  | 2.558mb  | 0.112μs | ±38.21% |
| benchYii             | 5000 | 30  | 2.558mb  | 0.636μs | ±5.68%  |
| benchLaravel         | 5000 | 30  | 2.558mb  | 1.234μs | ±7.73%  |
| benchSpiral          | 5000 | 30  | 2.558mb  | 2.496μs | ±10.23% |
| benchPhpDi           | 5000 | 30  | 2.558mb  | 0.769μs | ±20.11% |
| benchNihAuto         | 5000 | 30  | 2.558mb  | 0.430μs | ±8.29%  |
| benchNihManual       | 5000 | 30  | 2.558mb  | 0.423μs | ±6.73%  |
+----------------------+------+-----+----------+---------+---------+
```

- Best: **Symfony Compiled** (0.112μs)
- Worst: **Spiral** (2.496μs)

### SharedServiceCreationByNameBench

Simple singleton service creation.

```php
$app->get(Service::class); // Service#1
$app->get(Service::class); // Service#1
```

```bash
SharedServiceCreationByNameBench
+----------------------+------+-----+----------+---------+---------+
| subject              | revs | its | mem_peak | mode    | rstdev  |
+----------------------+------+-----+----------+---------+---------+
| benchSymfonyRuntime  | 5000 | 30  | 2.558mb  | 0.162μs | ±26.75% |
| benchSymfonyCompiled | 5000 | 30  | 2.558mb  | 0.047μs | ±18.34% |
| benchYii             | 5000 | 30  | 2.558mb  | 0.055μs | ±35.13% |
| benchLaravel         | 5000 | 30  | 2.558mb  | 0.223μs | ±32.82% |
| benchSpiral          | 5000 | 30  | 2.558mb  | 0.542μs | ±15.59% |
| benchPhpDi           | 5000 | 30  | 2.558mb  | 0.049μs | ±13.87% |
| benchNihAuto         | 5000 | 30  | 2.558mb  | 0.045μs | ±19.10% |
| benchNihManual       | 5000 | 30  | 2.558mb  | 0.044μs | ±27.43% |
+----------------------+------+-----+----------+---------+---------+
```

- Best: **NIH Manual** (0.044μs)
- Worst: **Spiral** (0.542μs)

### ServiceWithAutowireBench

Service singleton creation with autowiring.

```php
$app->get(Service::class); // Service#1 { dependency: InnerService }
$app->get(Service::class); // Service#1 { dependency: InnerService }
```

```bash
ServiceWithAutowireBench
+----------------------+------+-----+----------+---------+---------+
| subject              | revs | its | mem_peak | mode    | rstdev  |
+----------------------+------+-----+----------+---------+---------+
| benchSymfonyRuntime  | 5000 | 30  | 2.627mb  | 0.151μs | ±12.03% |
| benchSymfonyCompiled | 5000 | 30  | 2.627mb  | 0.042μs | ±7.73%  |
| benchYii             | 5000 | 30  | 2.627mb  | 0.052μs | ±15.32% |
| benchLaravel         | 5000 | 30  | 2.627mb  | 0.246μs | ±5.69%  |
| benchSpiral          | 5000 | 30  | 2.627mb  | 0.545μs | ±5.09%  |
| benchPhpDi           | 5000 | 30  | 2.627mb  | 0.050μs | ±19.19% |
| benchNihAuto         | 5000 | 30  | 2.627mb  | 0.044μs | ±10.24% |
| benchNihManual       | 5000 | 30  | 2.627mb  | 0.044μs | ±6.69%  |
+----------------------+------+-----+----------+---------+---------+
```

- Best: **Symfony Compiled / NIH Auto / NIH Manual** (0.042-0.044μs range)
- Worst: **Spiral** (0.545μs)

### ServiceCreationFromFactoryBench

Create service on every call from singleton factory service.

```php
$app->get(Service::class); // Service#1 (from Factory#1->create())
$app->get(Service::class); // Service#2 (from Factory#1->create())
```

```bash
ServiceCreationFromFactoryBench
+----------------------+------+-----+----------+---------+---------+
| subject              | revs | its | mem_peak | mode    | rstdev  |
+----------------------+------+-----+----------+---------+---------+
| benchSymfonyRuntime  | 5000 | 30  | 2.631mb  | 1.733μs | ±8.07%  |
| benchSymfonyCompiled | 5000 | 30  | 2.631mb  | 0.168μs | ±14.57% |
| benchYii             | 5000 | 30  | 2.630mb  | 0.055μs | ±10.50% |
| benchLaravel         | 5000 | 30  | 2.630mb  | 1.356μs | ±12.10% |
| benchSpiral          | 5000 | 30  | 2.630mb  | 4.633μs | ±5.03%  |
| benchPhpDi           | 5000 | 30  | 2.630mb  | 0.049μs | ±13.23% |
| benchNihAuto         | 5000 | 30  | 2.630mb  | 2.417μs | ±5.91%  |
| benchNihManual       | 5000 | 30  | 2.630mb  | 1.224μs | ±7.62%  |
+----------------------+------+-----+----------+---------+---------+
```

- Best: **PHP-DI** (0.049μs)
- Worst: **Spiral** (4.633μs)
