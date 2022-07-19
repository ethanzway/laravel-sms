<?php

namespace Ethanzway\Sms;

use Ethanzway\Sms\Support\Log;
use Monolog\Handler\HandlerInterface;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SmsManager
{
    protected $app;

    protected $factory;

    protected $driver;

    protected $drivers = [];

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

        if (!$this->app['config']['sms.debug'] || defined('PHPUNIT_RUNNING')) {
            $logger->pushHandler(new NullHandler());
        } elseif ($logFile = $this->app['config']['sms.log.file']) {
            $logger->pushHandler(
                new StreamHandler(
                $logFile,
                $this->app['config']['sms.log.level'],
                true,
                null
            )
            );
        }

        Log::setLogger($logger);
    }

    public function driver($name = null)
    {
        $name = $name ?: $this->getDefault();

        if (! isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->resolve($name);
        }

        return $this->drivers[$name];
    }

    protected function resolve($name)
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new \UnexpectedValueException('Driver [$name] is not defined.');
        }

        $driver = $this->factory->create($config['clazz']);

        $class = trim('\\Ethanzway\\Sms\\Drivers\\' . $config['clazz'], '\\');

        $reflection = new \ReflectionClass($class);
        
        foreach ($config['options'] as $optionName => $value) {
            $method = 'set' . ucfirst($optionName);

            if ($reflection->hasMethod($method)) {
                $driver->{$method}($value);
            }
        }

        return $driver;
    }

    protected function getDefault()
    {
        return $this->app['config']['sms.default'];
    }

    protected function getConfig($name)
    {
        return $this->app['config']['sms.drivers'][$name];
    }

    public function __call($method, $parameters)
    {
        return $this->driver()->{$method}(...$parameters);
    }
}
