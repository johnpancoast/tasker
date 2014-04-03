<?php

require 'vendor/autoload.php';

use Shideon\Tasker\Command as Command;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Command\TaskerCommand);
$application->add(new Command\RunTaskCommand);
$application->run();
