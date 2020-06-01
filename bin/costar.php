#!/usr/bin/env php
<?php declare(strict_types=1);

if ('cli' !== PHP_SAPI) {
    echo sprintf("Warning: 'costar' should be invoked via the CLI version of PHP, not the %s SAPI\n\n", PHP_SAPI);
}

if (file_exists(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
} elseif (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} elseif (file_exists(__DIR__ . '/../../../autoload.php')) {
    require_once __DIR__ . '/../../../autoload.php';
}

use Composer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

{
    $application = new Application();
    $application->setAutoExit(true);

    try {
        echo "\n";
        echo <<<FIGLET
     ______           __            
    / ____/___  _____/ /_____ ______
   / /   / __ \/ ___/ __/ __ `/ ___/
  / /___/ /_/ (__  ) /_/ /_/ / /    
  \____/\____/____/\__/\__,_/_/   
    
    Coding Standard Generator
   
      http://127.0.0.1:8080 

                
FIGLET;

        $application->run(new ArrayInput(['command' => 'serve']), new NullOutput());
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}