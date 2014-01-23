<?php namespace Timesheet\Repository\Devices;

class ConfigDevices implements DevicesInterface {

    public function addController($controllerId, 
                                  $fromDt = '',
                                  $fromTm = '',
                                  $toDtTm = '',
                                  $toTm = '', 
                                  $area = 'walk',
                                  $description = ''){

    }

    public function addReader($readerId, 
                              $fromDt = '',
                              $fromTm = '',
                              $toDtTm = '',
                              $toTm = '', 
                              $direction = 'in', 
                              $area = 'walk',
                              $description = ''){

    }

    public function getControllers(){

        return \Config::get('devices.devices.controllers');

    }

    public function getReaders(){

        return \Config::get('devices.devices.readers');

    }

    public function removeController($controllerId){

    }

    public function removeReader($readerId){


    }



}

?>