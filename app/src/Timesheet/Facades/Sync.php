<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class Sync extends Facade {

    protected static function getFacadeAccessor() { 

        return 'Timesheet\Repository\Sync\SyncInterface'; 

    }

}