<?php namespace Timesheet\Repository\Devices;

interface DevicesInterface{

    public function addController($controllerId, 
                                  $fromDt = '',
                                  $fromTm = '',
                                  $toDtTm = '',
                                  $toTm = '', 
                                  $area = 'walk',
                                  $description = '');

    public function addReader($readerId, 
                              $fromDt = '',
                              $fromTm = '',
                              $toDtTm = '',
                              $toTm = '', 
                              $direction = 'in', 
                              $area = 'walk',
                              $description = '');

    public function getControllers();

    public function getReaders();

    public function removeController($controllerId);

    public function removeReader($readerId);

}

?>