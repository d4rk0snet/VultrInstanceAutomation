<?php

// application.php

require __DIR__.'/../vendor/autoload.php';

use D4rk0s\Vultr\Commands\CreateInstance;
use D4rk0s\Vultr\Commands\DestroyServer;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new DestroyServer());
$application->add(new CreateInstance());


$application->run();