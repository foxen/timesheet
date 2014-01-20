<?php namespace Timesheet\Repository\Period;

use Illuminate\Support\ServiceProvider;

class PeriodServiceProvider extends ServiceProvider {
    
    public function register(){
        $this->app->bind('PeriodInterface', function(){
            
            return new ConfigPeriod;
        });
    }

}

?>