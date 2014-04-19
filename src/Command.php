<?php
/**
 * Parent command code.
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

/**
 * Parent command code.
 *
 * @author John Pancoast <shideon@gmail.com>
 * @abstract
 * @todo add monolog
 * @todo ditch this class if turns out it's not necessary.
 */
abstract class Command extends ConsoleCommand
{
    /**
     * @var array Passed args and options
     *
     * @access protected
     */
    protected $options = [];

    /**
     * Get command name
     *
     * @abstract
     * @access protected
     * @return string
     */
    abstract protected function getConfigName();

    /**
     * Get command description
     *
     * @abstract
     * @access protected
     * @return string
     */
    abstract protected function getConfigDescription();

    /**
     * Get command arguments
     *
     * @abstract
     * @access protected
     * @return array of arguments suitable for {@link Symfony\Component\Console\Command\Command::addArgument()}
     */
    abstract protected function getConfigArguments();

    /**
     * Get command options
     *
     * @abstract
     * @access protected
     * @return array of arguments suitable for {@link Symfony\Component\Console\Command\Command::addOption()}
     */
    abstract protected function getConfigOptions();

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName($this->getConfigName())
            ->setDescription($this->getConfigDescription());

        foreach ($this->getConfigOptions() as $option) {
           $this->addOption($option[0], $option[1], $option[2], $option[3], $option[4]); 
        }

        foreach ($this->getConfigArguments() as $argument) {
           $this->addArgument($argument[0], $argument[1], $argument[2], $argument[3]); 
        }
    }
}
