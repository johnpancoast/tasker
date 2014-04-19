<?php
/**
 * Common tasker functionality
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Symfony\Component\Console\Output\OutputInterface;
use Shideon\Tasker as TaskerBase;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Config\Definition\Processor;


/**
 * Common tasker functionality
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class Common
{
    /**
     * Get a config array given a file.
     *
     * This also doubles as config validation.
     *
     * @access public
     * @static
     * @param string $configFile The config file.
     * @return array
     */
    public static function getConfigArray($configFile)
    {
        $configFile = self::getAbsolutePath($configFile);

        $processor = new Processor();
        $configuration = new Configuration(); // Shideon\Tasker\Configuration
        return $processor->processConfiguration(
            $configuration,
            [Yaml::parse($configFile)]
        );
    }

    /**
     * Get an absolute path of a config file
     *
     * @access public
     * @static
     * @param string $file A config file path
     */
    public static function getAbsolutePath($path)
    {
       return (substr($path, 0, 1) == '/') ? $path : getcwd().'/'.$path;
    }
}
