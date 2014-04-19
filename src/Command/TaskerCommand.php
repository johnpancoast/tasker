<?php
/**
 * Command to execute scheduled jobs either once or continuously as a daemon.
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Shideon\Tasker;

/**
 * Command to execute scheduled jobs once or continuously as a daemon.
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class TaskerCommand extends Tasker\Command
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('shideon:tasker')
            ->setDescription('Run scheduled tasks.')
            ->addOption(
                'config',
                'c',
               InputOption::VALUE_REQUIRED,
                'Config file.'
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = [
            'config' => $input->getOption('config'),
        ];

        if (!$this->options['config']) {
            throw new \Exception('Must set --config.');
        }

        $tasker = new Tasker\Tasker($this->options['config']);
        $output->write($tasker->run());
    }
}
