<?php
use Timesheet\Repository\InputAdapter\PercoInputAdapter;
class TestController extends  BaseController {
    
    public function doit(){
        //$a = InputAdapter::getEvents(Period::getDates(), Devices::getControllers());
        //$a = Sync::parcelSync(\Period::getDates());
        $a = Data::getTimesheet(\Period::getDates());
        print_r($a);
    }
}

?>