<?php

namespace Gw19900524\Sms\Facades;

use Illuminate\Support\Facades\Facade;

class SmsFacade extends Facade {

    protected static function getFacadeAccessor() { 
        return 'sms'; 
    }

}