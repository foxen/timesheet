<?php namespace Timesheet\Facades;

use Illuminate\Support\Facades\Facade;
    
class InputAdapter extends Facade {

    protected static function getFacadeAccessor() { 

        return 'Timesheet\Repository\InputAdapter\InputAdapterInterface'; 

    }

}