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
     * Is a log file required
     *
     * @access protected
     * @return bool
     */
    abstract protected function isLogFileRequired();

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
     * Get command options
     *
     * @access protected
     * @return array of arguments suitable for {@link Symfony\Component\Console\Command\Command::addOption()}
     */
    protected function getConfigOptions()
    {
        // ATM, all that's expected is the below but the code assuming
        // future will expect more.
        $options = [];

        if ($this->isLogFileRequired()) {
            $options[] = [
                'log_file',
                'f',
               InputOption::VALUE_REQUIRED,
               'Log file',
                null
            ];
        }

        return $options;
    } 

    /**
     * Build our logger
     *
     * @access protected
     * @return Monolog\Logger
     */
    protected function buildLogger()
    {
        if (!isset($this->options['log_file'])) {
            throw new \Exception('To build the lib\'s logger, we need a log file to log to.');
        }

        $logger = new Logger('shideon_tasker_main');
        $logger->pushHandler(new StreamHandler($this->options['log_file'], Logger::INFO));;

        return $logger;
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
     * @param array $requiredOptions Required options (since we sometime need named args that are required and console doesn't allow this)
     * @return void
     */
    protected function init(InputInterface $input, OutputInterface $output, array $requiredOptions = [])
    {
        $this->input = $input;
        $this->output = $output;

        // pull args and options from input, allow methods to be
        // overridden so children can define functionality.
        $this->arguments = $this->getInputArguments($input);
        $this->options = $this->getInputOptions($input);

        if ($this->isLogFileRequired()) {
            $requiredOptions[] = 'log_file';
        }

        $this->validateOptions($requiredOptions);
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
                throw new Exception\RequiredCommandOptionException("Must set the --$key option.");
            }
        }
    }
}
