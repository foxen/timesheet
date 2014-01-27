<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class Api extends Facade {

    protected static function getFacadeAccessor() { 

        return 'Timesheet\Repository\Api\ApiInterface'; 

    }

}