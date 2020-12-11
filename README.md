Parallel Bridge
==============

Provides utilities from AMPHP Parallel, especially parallel functions.

### SETUP

1. use composer to install parallel Bridge

```bash
composer require publicplan/parallel-bridge
```

2. create a _worker-bootstrap.php_ in your application config folder.

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
pp_parallel_bridge:
  amphp_max_worker: '%env(int:AMPHP_MAX_WORKERS)%'
  project_dir: '%kernel.project_dir%'
```


4.setup your max worker amount in your .env.local 2

```yaml
AMPHP_MAX_WORKERS=3
```

5.use PromiseWait in your class to remap async!

```PHP
<?php

declare(strict_types=1);

namespace App\Service\YourClass;

use Publicplan\ParallelBridge\PromiseWait;

class YourClass {
    /** @var PromiseWait */
    private $promiseWait;

    public function __construct( PromiseWait $promiseWait ) {
        $this->promiseWait = $promiseWait;
    }
    
    public function yourFunction(): void
    {
        $unprocessDataArray = [1,2,3,4,5,6,7,8];
        
        //If we want to use a container class it must be public and in the following format:
        $finalDataArray = $this->promiseWait->parallelMap($unprocessDataArray, [$this,'processSingleElement']);
        print_r($finalDataArray);
    }
 
    //This Function will be called async from our processes after grabbing this service from service container 
    public function processSingleElement(int $number): int {
        return $number ** $number;
    }
}
```

