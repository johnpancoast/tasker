<?php
/**
 * Tasker configuration
 *
 * @copyright (c) 2014 John Pancoast
 * @author John Pancoast <shideon@gmail.com>
 * @license MIT
 */

namespace Shideon\Tasker;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Tasker configuration
 *
 * For details on architecture, see docs for symfony's console component
 * at http://symfony.com/doc/current/components/console/introduction.html.
 *
 * @author John Pancoast <shideon@gmail.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shideon');

        $rootNode
            ->children()
            ->arrayNode('tasker')
                ->children()
                    ->append($this->appendTasks())
                ->end()
            ->end();

        return $treeBuilder;
    }

    public function appendTasks()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('tasks');

        $node
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->prototype('array')
                ->children()
                    ->scalarNode('name')->cannotBeEmpty()->end()
                    ->scalarNode('time')->cannotBeEmpty()->end()
                    ->scalarNode('class')->cannotBeEmpty()->end()
                    ->arrayNode('command')
                        ->cannotBeEmpty()
                        ->children()
                            ->scalarNode('name')->isRequired()->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
