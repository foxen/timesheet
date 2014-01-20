<?php namespace Timesheet\Repository\Devices;

use Illuminate\Support\ServiceProvider;

class DevicesServiceProvider extends ServiceProvider {
    
    public function register(){
        $this->app->bind('DevicesInterface', function(){
            
            return new ConfigDevices;
        });
    }

}

?>