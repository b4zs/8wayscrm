<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\Debug\Debug;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;


$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
require_once __DIR__.'/../app/AppCache.php';


$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();


$application = new Application($kernel);
$application->setAutoExit(false);

$input = new ArrayInput(array(
  'command' => 'octet-ticketing:send-reminders',
  '--env' => 'dev',
  '-vvv' => '',
// '--help' => '',
));
        // You can use NullOutput() if you don't need the output
$output = new BufferedOutput();
$application->run($input, $output);

 // return the output, don't use if you used NullOutput()
$content = $output->fetch();
echo $content;


