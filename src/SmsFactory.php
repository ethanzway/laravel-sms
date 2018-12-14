<?php

namespace Ethanzway\Sms;

class SmsFactory
{
    /**
     * Create a new driver instance
     *
     * @param string               $class       Driver name
     * @throws RuntimeException                 If no such driver is found
     */
    public function create($class)
    {
        $class = trim('\\Ethanzway\\Sms\\Drivers\\'.$class, "\\");
        if (!class_exists($class)) {
            throw new \UnexpectedValueException("Driver [$class] is not defined.");
        }

        return new $class;
    }
}