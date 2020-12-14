Parallel Bridge
==============

[![Version][version-badge]][changelog]
[![MIT License][license-badge]][license]
[![Symfony][symfony-badge]][symfony]
[![Pipeline][pipeline-badge]][pipeline]

Provides utilities from AMPHP Parallel, especially parallel functions.

### SETUP

1. Use composer to install parallel Bridge.

```bash
composer require publicplan/parallel-bridge
```

2. Create a _worker-bootstrap.php_ in your application config folder.

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
3. Create a _parallel_bridge.yaml_ in your config/packages dir
```yaml
#config/packages/parallel_bridge.yaml
publicplan_parallel_bridge:
  amphp_max_worker: '%env(int:AMPHP_MAX_WORKERS)%'
  project_dir: '%kernel.project_dir%'
```


4. Setup your max worker amount in your _.env.local_.

```yaml
AMPHP_MAX_WORKERS=3
```

5. Use PromiseWait in your class to remap async! 
   You can use any callable u like but you should consider that closures must be serializable. 
   When you need your projects context we recommend to use the following way:
   (also see https://amphp.org/parallel-functions/ for deeper informations) 

```PHP
//src/Service/YourClass.php
<?php

declare(strict_types=1);

namespace App\Service;

use Amp\MultiReasonException;
use Publicplan\ParallelBridge\PromiseWait;

class YourClass
{
    /** @var PromiseWait */
    private $promiseWait;

    public function __construct(PromiseWait $promiseWait)
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

6. Make your service public!
When using a service, like we do in the example above, you need to make your service public. 

```yaml
# config/services.yaml
services:
    [...]
    App\Service\YourClass:
        public: true
```

[version-badge]: https://img.shields.io/badge/version-1.0.0-blue.svg
[changelog]: ./CHANGELOG.md
[license-badge]: https://img.shields.io/badge/license-MIT-blue.svg
[license]: ./LICENSE
[symfony-badge]: https://img.shields.io/badge/Symfony-5.2-blue.svg
[symfony]: https://symfony.com/releases/5.2
[pipeline-badge]: https://github.com/thled/symfony-parallel-bridge/workflows/ci-pipeline/badge.svg?branch=master
[pipeline]: https://github.com/thled/symfony-parallel-bridge/actions?query=workflow%3A%22ci-pipeline%22+branch%3Amaster
