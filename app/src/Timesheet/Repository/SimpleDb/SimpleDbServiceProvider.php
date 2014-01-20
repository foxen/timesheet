<?php namespace Timesheet\Repository\SimpleDb;

use Illuminate\Support\ServiceProvider;

class SimpleDbServiceProvider extends ServiceProvider {
    
    public function register(){
        $this->app->bind('SimpleDbInterface', function(){
            
            return new InterbaseSimpleDb(
                \Config::get('simple.connections.interbase'));
        });
    }

}
?>