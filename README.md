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
use Symfony\Component\Debug\Debug;

set_time_limit(0);

require dirname(__DIR__).'/config/bootstrap.php';

if ($_SERVER['APP_DEBUG']) {
umask(0000);

    if (class_exists(Debug::class)) {
        Debug::enable();
    }
}

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();
$GLOBALS['kernel'] = $kernel;
```

3.setup your max worker amount in your .env.local 2
```yaml
AMPHP_MAX_WORKERS=3
```

4.use PromiseWait in your class to remap async!

```PHP
<?php

declare(strict_types=1);

namespace App\Service\YourClass;

use PP\ParallelBridge\PromiseWait;

class YourClass {
    /** @var PromiseWait */
    private $promiseWait;

    public function __construct( PromiseWait $promiseWait ) {
        $this->promiseWait = $promiseWait;
    }
    
    public function yourFunction(): void
    {
        $unprocessDataArray = [1,2,3,4,5,6,7,8];
        $finalDataArray = $this->promiseWait->parallelMap($unprocessDataArray, YourClass::class, 'processSingleElement');
        print_r($finalDataArray);
    }
 
    /** 
     * This Function will be called async from our processes 
     */   
    public function processSingleElement(int $number): int {
        return $number ** $number;
    }
}
```

