# Parallel Bridge

[![Version][version-badge]][changelog]
[![MIT License][license-badge]][license]
[![Symfony][symfony-badge]][symfony]
[![Pipeline][pipeline-badge]][pipeline]

Provides utilities from AMPHP Parallel, especially parallel functions.
This Bundle is developed and tested with php 7.3, 7.4 and 8.0 and Symfony 4.4 and 5.2. 
Other Versions might work but are not supported or tested.

## SETUP

1. Use composer to install Parallel-Bridge.

```bash
composer require publicplan/parallel-bridge
```

1. Create `worker-bootstrap.php` in your application config folder.

```PHP
// app/config/worker-bootstrap.php
<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

set_time_limit(0);

if (class_exists(Dotenv::class)) {
    if (method_exists(Dotenv::class, 'bootenv')) {
        (new Dotenv())->bootEnv(dirname(__DIR__) . '/.env');
    } else {
        require dirname(__DIR__) . '/config/bootstrap.php';
    }
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool)$_SERVER['APP_DEBUG']);
$kernel->boot();
$GLOBALS['kernel'] = $kernel;

```

1. Create `parallel_bridge.yaml` in your `config/packages` dir.

```yaml
# config/packages/parallel_bridge.yaml
publicplan_parallel_bridge:
  amphp_max_worker: '%env(int:AMPHP_MAX_WORKERS)%'
  project_dir: '%kernel.project_dir%'
```

1. Setup your maximal worker amount in your `.env.local`.

```bash
AMPHP_MAX_WORKERS=3
```

## Usage

1. Use PromiseWait in your class to remap async!
   You can use any callable you like but you should consider that closures must be serializable.
   When you need your projects context we recommend to use the following way:
   (also see <https://amphp.org/parallel-functions/> for deeper information)

```PHP
// src/Service/YourClass.php
<?php

declare(strict_types=1);

namespace App\Service;

use Amp\MultiReasonException;
use Publicplan\ParallelBridge\PromiseWaitInterface;

class YourClass
{
    /** @var PromiseWaitInterface */
    private $promiseWait;

    public function __construct(PromiseWaitInterface $promiseWait)
    {
        $this->promiseWait = $promiseWait;
    }

    public function yourFunction(): void
    {
        $unprocessDataArray = range(1, 100);

        try{
            //If we want to use a container class it must be public and in the following format:
            $finalDataArray = $this->promiseWait->parallelMap($unprocessDataArray, [$this,'processSingleElement']);
        } 
        //to get possible errors you have to catch MultiReasonException 
        catch (MultiReasonException $exception ){
            dd($exception);
        }
        print_r($finalDataArray);
    }

    //This Function will be called async from our processes after grabbing this service from service container
    public function processSingleElement(int $number): string
    {
        for ($i = 0; $i < 200000; $i++) {
         $hash =  hash('SHA512', (string)$number);
        }
        return $hash;
    }
}
```

1. Make your service public!
When using a service, like we do in the example above, you need to make your service public.

```yaml
# config/services.yaml
services:
    [...]
    App\Service\YourClass:
        public: true
```

### Optional: Additional Arguments

Add additional arguments to the `PromiseWait::parallelMap()` function as you like.
All arguments get passed through to your called function.

```php
<?php

declare(strict_types=1);

namespace App\Service;

use Publicplan\ParallelBridge\PromiseWaitInterface;

class YourClass
{
    /** @var PromiseWaitInterface */
    private $promiseWait;

    public function __construct(PromiseWaitInterface $promiseWait)
    {
        $this->promiseWait = $promiseWait;
    }
    
    public function yourFunction(): void
    {
        $unprocessDataArray = range(1, 100);
        $additionalArg = 42;

        $result = $this->promiseWait->parallelMap(
           $unprocessDataArray, 
           [$this,'processSingleElement'],
           $additionalArg,
        );
        
        //Returns numbers from 43 to 143
        print_r($result);
    }

    public function processSingleElement(int $number, int $additionalArg): int
    {
        return $number + $additionalArg;
    }
}
```

[version-badge]: https://img.shields.io/badge/version-1.0.2-blue.svg
[changelog]: ./CHANGELOG.md
[license-badge]: https://img.shields.io/badge/license-MIT-blue.svg
[license]: ./LICENSE
[symfony-badge]: https://img.shields.io/badge/Symfony-5.2-blue.svg
[symfony]: https://symfony.com/releases/5.2
[pipeline-badge]: https://github.com/thled/symfony-parallel-bridge/workflows/ci-pipeline/badge.svg?branch=master
[pipeline]: https://github.com/thled/symfony-parallel-bridge/actions?query=workflow%3A%22ci-pipeline%22+branch%3Amaster
