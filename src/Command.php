<?php
/**
 * Command to execute scheduled jobs either once or continuously as a daemon.
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;

use Shideon\Tasker;

/**
 * Command to execute scheduled jobs once or continuously as a daemon.
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class Command extends ConsoleCommand
{
    /**
     * @var array Passed args and options
     *
     * @access private
     */
    private $options = [];

    /**
     * @var array Processed configuration
     *
     * @access private
     */
    private $config = [];

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
            ->addOption(
                'daemon',
                'd',
               InputOption::VALUE_OPTIONAL,
                'Run continuously as a daemon.'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->options = [
            'config' => $input->getOption('config'),
            'daemon' => $input->getOption('daemon')
        ];

        if (!$this->options['config']) {
            throw new \Exception('Must set --config.');
        }

        $processor = new Processor();
        $configuration = new Configuration();
        $this->config = $processor->processConfiguration(
            $configuration,
            [Yaml::parse($this->options['config']]
        );

        $tasker = new Tasker\Tasker;
        $output->write($tasker->run($this->options['daemon']));
    }
}
