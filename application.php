#!/usr/bin/env php
<?php
// application.php

use Symfony\Component\Console\Application;

require __DIR__.'/vendor/autoload.php';

$application = new Application();
$application->add(new Compile());
$application->add(new Watch());
$application->add(new RunAny());
$application->run();