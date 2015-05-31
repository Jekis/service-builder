<?php

namespace Jekis;

class ServiceBuilder
{
    public static function registerServices(\Silex\Application $app, array $dependencies)
    {
        foreach ($dependencies as $name => $config) {
            static::registerService($app, $name, $config);
        }
    }

    private static function registerService(\Silex\Application $app, $name, array $customConfig = array())
    {
        $lib = static::libs($name);
        $serviceProvider = new $lib['provider'];
        $serviceConfig = isset($lib['config']) ? $lib['config'] : array();
        $serviceConfig = array_merge($serviceConfig, $customConfig);

        $app->register($serviceProvider, $serviceConfig);
    }

    public static function hasLib($lib)
    {
        return in_array($lib, array_keys(static::libs()), true);
    }

    private static function libs($name = null)
    {
        $libs = array(
            'dbal' => array(
                'provider' => 'Silex\Provider\DoctrineServiceProvider',
            ),
            'twig' => array(
                'provider' => 'Silex\Provider\TwigServiceProvider',
            ),
            'redis' => array(
                'provider' => 'Predis\Silex\ClientServiceProvider',
                'config' => array(
                    'predis.parameters' => 'tcp://127.0.0.1:6379',
                    'predis.options' => array(
                        'prefix'  => 'newsservice:',
                        'profile' => '3.0',
                    ),
                ),
            ),
        );

        if ($name && !isset($libs[$name])) {
            throw new \InvalidArgumentException(sprintf('Library "%s" is not supported.', $name));
        }

        return $name ? $libs[$name] : $libs;
    }
}
