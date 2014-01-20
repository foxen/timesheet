<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class Devices extends Facade {

    protected static function getFacadeAccessor() { 

        return 'DevicesInterface'; 

    }

}