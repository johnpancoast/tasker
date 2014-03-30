<?php

namespace Shideon\Tasker\Configuration

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('shideon_tasker');

        // ... add node definitions to the root of the tree

        return $treeBuilder;
    }
}