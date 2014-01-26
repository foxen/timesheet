<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class Data extends Facade {

    protected static function getFacadeAccessor() { 

        return 'Timesheet\Repository\Data\DataInterface'; 

    }

}