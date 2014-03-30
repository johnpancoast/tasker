<?php

require 'vendor/autoload.php';

use Shideon\Tasker\Command\TaskerCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new TaskerCommand);
$application->run();