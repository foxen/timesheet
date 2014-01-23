<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class SimpleDb extends Facade {

    protected static function getFacadeAccessor() { 

        return 'Timesheet\Repository\SimpleDb\SimpleDbInterface'; 

    }

}

?>