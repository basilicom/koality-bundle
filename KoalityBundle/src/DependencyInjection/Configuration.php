<?php

namespace Basilicom\KoalityBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{

    public const KOALITY = 'koality';
    public const ORDERS_CHECK = 'orders_check';
    public const HOURS = 'hours';
    public const SERVER_UPTIME_CHECK = 'server_uptime_check';
    public const TIME_INTERVAL = 'time_interval';
    public const SPACE_USED_CHECK = 'space_used_check';
    public const LIMIT_IN_PERCENT = 'limit_in_percent';
    public const CONTAINER_IS_RUNNING_CHECK = 'container_is_running_check';
    public const CONTAINER_NAME = 'container_name';


    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::KOALITY);
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode(self::ORDERS_CHECK)
                    ->children()
                        ->integerNode(self::HOURS)->end()
                    ->end()
                ->end() //ORDERS_CHECK
                ->arrayNode(self::SERVER_UPTIME_CHECK)
                    ->children()
                        ->scalarNode(self::TIME_INTERVAL)->end()
                    ->end()
                ->end() //SERVER_UPTIME_CHECK
                ->arrayNode(self::SPACE_USED_CHECK)
                    ->children()
                        ->integerNode(self::LIMIT_IN_PERCENT)->end()
                    ->end()
                ->end() //SPACE_USED_CHECK
                ->arrayNode(self::CONTAINER_IS_RUNNING_CHECK)
                    ->children()
                        ->scalarNode(self::CONTAINER_NAME)->end()
                    ->end()
                ->end() //CONTAINER_IS_RUNNING_CHECK
            ->end()
        ;

        return $treeBuilder;
    }
}
