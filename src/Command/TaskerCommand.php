<?php

namespace Shideon\Tasker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class TaskerCommand extends Command
{
    /**
     * Passed args and options
     */
    private $options = [];

    protected function configure()
    {
        $this
            ->setName('shideon:tasker')
            ->setDescription('Run scheduled tasks.')
            ->addOption(
                'config',
                'c',
               InputOption::VALUE_REQUIRED,
                'Config file'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = [
            'config' => $input->getOption('config')
        ];

        print_r($this->options);

        $output->writeln($text);
    }
}