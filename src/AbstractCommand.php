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

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Parent command code.
 *
 * @author John Pancoast <shideon@gmail.com>
 * @abstract
 */
abstract class AbstractCommand extends ConsoleCommand
{
    /**
     * @var InputInterface Command input
     *
     * @access protected
     */
    protected $input;

    /**
     * @var OutputInterface Command output
     *
     * @access protected
     */
    protected $output;

    /**
     * @var array Passed args and arguments
     *
     * @access protected
     */
    protected $arguments = [];

    /**
     * @var array Passed args and options
     *
     * @access protected
     */
    protected $options = [];

    /**
     * @var Logger The monolog logger
     *
     * @access private
     */
    private $logger;

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
     * @access protected
     * @return array of arguments suitable for {@link Symfony\Component\Console\Command\Command::addOption()}
     */
    protected function getConfigOptions()
    {
        // note that we (as a lib) require these fields. This is for 2 reasons:
        // config = To work standalone we need to allow configs to be
        // passed to read the tasks that are to be run. A framework's
        // implementation (extension) may define it's tasks differently
        // and thus, this method is here to be overridden.
        // log_file = As a standalone lib, we instantiate our own
        // monolog logger but allow the command client/caller to define
        // where the log file lies.
        return [
            [
                'config_file',
                'c',
               InputOption::VALUE_REQUIRED,
                'Config file.',
                null
            ],
            [
                'log_file',
                'f',
               InputOption::VALUE_REQUIRED,
               'Log file',
                null
            ],
            [
                'log_level',
                'e',
               InputOption::VALUE_REQUIRED,
               'Log level',
                null
            ]
        ];
    } 

    /**
     * Required options.
     *
     * Defines the options that are required. We do this because symfony
     * follows some standard that somehow dictates that their options must
     * be "optional". The functionality we need is different in
     * that we need named args that are required. This provides that.
     *
     * @access protected
     * @return array Return empty array to imply that nothing is required.
     */
    protected function getRequiredOptions()
    {
        // see comments inside {@link self::getConfigOptions()}
        return [
            'config_file',
            'log_file'
        ];
    }

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

    /**
     * Initialize
     *
     * Set internal vars:
     * {@link self::input}, {@link self::output},
     * {@link self::arguments}, {@link self::options}
     *
     * @access protected
     * @param InputInterface $input An InputInterface instance
     * @param OutputInterface $output An OutputInterface instance
     * @return void
     */
    protected function init(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        // pull args and options from input, allow methods to be
        // overridden so children can define functionality.
        $this->arguments = $this->getInputArguments($input);
        $this->options = $this->getInputOptions($input);

        $this->validateOptions($this->getRequiredOptions());
    }

    /**
     * Build our logger
     *
     * @access protected
     * @return Monolog\Logger
     */
    protected function buildLogger()
    {
        if (!$this->logger) {
            if (!isset($this->options['log_file'])) {
                throw new \Exception('To build the lib\'s logger, we need a log file to log to.');
            }

            $this->logger = new Logger('shideon_tasker_main');
            $this->logger->pushHandler(new StreamHandler($this->options['log_file'], $this->options['log_level'] ?: Logger::INFO));;
        }

        return $this->logger;
    }

    /**
     * Get arguments out of input
     *
     * Default functionality just pulls args from input but
     * having this method here allows children to override...
     * perhaps they need to initialize these values somehow.
     *
     * @access protected
     * @param InputInterface $input An InputInterface instance
     * @return array
     */
    protected function getInputArguments(InputInterface $input)
    {
        return $input->getArguments();
    }

    /**
     * Get arguments out of input
     *
     * Default functionality just pulls args from input but
     * having this method here allows children to override...
     * perhaps they need to initialize these values somehow.
     *
     * @access protected
     * @param InputInterface $input An InputInterface instance
     * @return array
     */
    protected function getInputOptions(InputInterface $input)
    {
        return $input->getOptions();
    }


    /**
     * Validate options
     *
     * @access protected
     * @throws Exception\RequiredCommandOptionException if requiredOption doesn't exist in {@link self::options}
     */
    protected function validateOptions(array $requiredOptions = [])
    {
        foreach ($requiredOptions as $key) {
            if (!array_key_exists($key, $this->options) || empty($this->options[$key])) {
                throw new Exception\RequiredCommandOptionException("Must set the --$key option.", $key);
            }
        }
    }
}
