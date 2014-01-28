<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class Days extends Facade {

    protected static function getFacadeAccessor() { 

        return 'Timesheet\Repository\Days\DaysInterface'; 

    }

}