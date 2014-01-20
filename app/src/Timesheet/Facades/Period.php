<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class Period extends Facade {

    protected static function getFacadeAccessor() { 

        return 'PeriodInterface'; 

    }

}