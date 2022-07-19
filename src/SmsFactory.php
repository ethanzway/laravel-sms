<?php

namespace Ethanzway\Sms;

class SmsFactory
{
    public function create($class)
    {
        $class = trim('\\Ethanzway\\Sms\\Drivers\\'.$class, '\\');
        if (!class_exists($class)) {
            throw new \UnexpectedValueException('Driver [$class] is not defined.');
        }

        return new $class;
    }
}