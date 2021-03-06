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
    public const ENABLE = 'enable';
    public const TOKEN = 'token';
    public const SECRET = 'secret';


    public const ORDERS_PER_TIME_INTERVAL_CHECK = 'orders_per_time_interval_check';
    public const HOURS = 'hours';
    public const THRESHOLD = 'threshold';
    public const NEW_CARTS_PER_TIME_INTERVAL_CHECK = 'new_carts_per_time_interval_check';
    public const SERVER_UPTIME_CHECK = 'server_uptime_check';
    public const TIME_INTERVAL = 'time_interval';
    public const SPACE_USED_CHECK = 'space_used_check';
    public const LIMIT_IN_PERCENT = 'limit_in_percent';
    public const PATH_TO_CONTAINER = 'path_to_container';
    public const CONTAINER_IS_RUNNING_CHECK = 'container_is_running_check';
    public const CONTAINER_NAME = 'container_name';
    public const DEBUG_MODE_ENABLED_CHECK = 'debug_mode_enabled_check';
    public const MAINTENANCE_WORKER_RUNNING_CHECK = 'maintenance_worker_running_check';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::KOALITY);
        $treeBuilder->getRootNode()
                ->children()
                    ->arrayNode(self::TOKEN)
                        ->children()
                            ->scalarNode(self::SECRET)->end()
                        ->end()
                    ->end() //TOKEN
                    ->arrayNode(self::ORDERS_PER_TIME_INTERVAL_CHECK)
                        ->children()
                            ->booleanNode(self::ENABLE)->end()
                            ->integerNode(self::HOURS)->end()
                            ->integerNode(self::THRESHOLD)->end()
                        ->end()
                    ->end() //ORDERS_PER_TIME_INTERVAL_CHECK
                    ->arrayNode(self::NEW_CARTS_PER_TIME_INTERVAL_CHECK)
                        ->children()
                            ->booleanNode(self::ENABLE)->end()
                            ->integerNode(self::HOURS)->end()
                        ->end()
                    ->end() //NEW_CARTS_PER_TIME_INTERVAL_CHECK
                    ->arrayNode(self::DEBUG_MODE_ENABLED_CHECK)
                        ->children()
                            ->booleanNode(self::ENABLE)->end()
                        ->end()
                    ->end() //DEBUG_MODE_ENABLED_CHECK
                    ->arrayNode(self::MAINTENANCE_WORKER_RUNNING_CHECK)
                        ->children()
                            ->booleanNode(self::ENABLE)->end()
                        ->end()
                    ->end() //MAINTENANCE_WORKER_RUNNING_CHECK
                    ->arrayNode(self::SERVER_UPTIME_CHECK)
                        ->children()
                            ->booleanNode(self::ENABLE)->end()
                            ->scalarNode(self::TIME_INTERVAL)->end()
                        ->end()
                    ->end() //SERVER_UPTIME_CHECK
                    ->arrayNode(self::SPACE_USED_CHECK)
                        ->children()
                            ->booleanNode(self::ENABLE)->end()
                            ->integerNode(self::LIMIT_IN_PERCENT)->end()
                            ->scalarNode(self::PATH_TO_CONTAINER)->end()
                        ->end()
                    ->end() //SPACE_USED_CHECK
                    ->arrayNode(self::CONTAINER_IS_RUNNING_CHECK)
                        ->children()
                            ->booleanNode(self::ENABLE)->end()
                            ->scalarNode(self::CONTAINER_NAME)->end()
                        ->end()
                    ->end() //CONTAINER_IS_RUNNING_CHECK
            ->end()
        ;

        return $treeBuilder;
    }
}
