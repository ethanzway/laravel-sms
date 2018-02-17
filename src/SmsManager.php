<?php

namespace Gw19900524\Sms;

use Gw19900524\Sms\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SmsManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;

    /**
     * Omnipay Factory Instance
     * @var \Omnipay\Common\GatewayFactory
     */
    protected $factory;

    /**
     * The current driver to use
     * @var string
     */
    protected $driver;

    /**
     * The array of resolved queue connections.
     *
     * @var array
     */
    protected $drivers = [];
    
    /**
     * Create a new sms manager instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @param $factory
     */
    public function __construct($app, $factory)
    {
        $this->app = $app;
        $this->factory = $factory;
        $this->initializeLogger();
    }

    private function initializeLogger()
    {
        if (Log::hasLogger()) {
            return;
        }

        $logger = new Logger('laravelsms');

        if (!$this->app['config']["laravel-sms.debug"] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($logFile = $this->app['config']["laravel-sms.log.file"]) {
            $logger->pushHandler(new StreamHandler(
                $logFile,
                $this->app['config']['laravel-sms.log.level'],
                true,
                null)
            );
        }

        Log::setLogger($logger);
    }
    
    /** 
     * Get an instance of the specified driver
     * @param  index of config array to use
     * @return Gw19900524\Sms\Drivers\AbstractDriver
     */
    public function driver($name = null)
    {
        $name = $name ?: $this->getDefault();

        if ( ! isset($this->drivers[$name]))
        {
            $this->drivers[$name] = $this->resolve($name);
        }

        return $this->drivers[$name];
    }

    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if(is_null($config))
        {
            throw new \UnexpectedValueException("Driver [$name] is not defined.");
        }

        $driver = $this->factory->create($config['clazz']);

        $class = trim('\\Gw19900524\\Sms\\Drivers\\' . $config['clazz'], "\\");

        $reflection = new \ReflectionClass($class);
        
        foreach($config['options'] as $optionName => $value)
        {
            $method = 'set' . ucfirst($optionName);

            if ($reflection->hasMethod($method)) {
                $driver->{$method}($value);
            }
        }

        return $driver;
    }

    protected function getDefault()
    {
        return $this->app['config']['laravel-sms.default'];
    }

    protected function getConfig($name)
    {
        return $this->app['config']["laravel-sms.drivers.{$name}"];
    }

    public function __call($method, $parameters)
    {
        return $this->driver()->{$method}(...$parameters);
    }
}