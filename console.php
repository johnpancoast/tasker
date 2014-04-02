<?php

require 'vendor/autoload.php';

use Shideon\Tasker as Tasker;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Tasker\Command\TaskerCommand);
$application->add(new Tasker\Command\RunTaskCommand);
$application->run();
