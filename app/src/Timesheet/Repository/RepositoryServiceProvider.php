<?php namespace Timesheet\Repository;

use Illuminate\Support\ServiceProvider;

use Timesheet\Repository\SimpleDb\InterbaseSimpleDb;
use Timesheet\Repository\InputAdapter\PercoInputAdapter;
use Timesheet\Repository\Period\ConfigPeriod;
use Timesheet\Repository\Devices\ConfigDevices;
use Timesheet\Repository\Data\MysqlData;
use Timesheet\Repository\Sync\Sync;

class RepositoryServiceProvider extends ServiceProvider {
    
    public function register(){
        $app = $this->app;

        $app->bind('Timesheet\Repository\SimpleDb\SimpleDbInterface', function(){   
                return new InterbaseSimpleDb(
                    \Config::get('simple.connections.interbase')
                );
        });

        $app->bind('Timesheet\Repository\InputAdapter\InputAdapterInterface', function($app){
            return new PercoInputAdapter(
                $app->make(
                    'Timesheet\Repository\SimpleDb\SimpleDbInterface'
                )
            );
        });

        $app->bind('Timesheet\Repository\Period\PeriodInterface', function(){        
            return new ConfigPeriod;
        });

        $app->bind('Timesheet\Repository\Devices\DevicesInterface', function(){ 
            return new ConfigDevices;
        });

        $app->bind('Timesheet\Repository\Data\DataInterface', function(){ 
            return new MysqlData;
        });

        $app->bind('Timesheet\Repository\Sync\SyncInterface', function($app){
            return new Sync(
                $app->make(
                    'Timesheet\Repository\Period\PeriodInterface'),
                $app->make(
                    'Timesheet\Repository\Devices\DevicesInterface'),
                $app->make(
                    'Timesheet\Repository\Data\DataInterface'),
                $app->make(
                    'Timesheet\Repository\InputAdapter\InputAdapterInterface')

            );
        });


    }

}