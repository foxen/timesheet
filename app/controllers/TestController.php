<?php
use Timesheet\Repository\InputAdapter\PercoInputAdapter;
class TestController extends  BaseController {
    
    public function gettimesheet(){
        //Sync::parcelSync(\Period::getDates());
        //$a = Data::getTimesheet(\Period::getDates());
        Sync::parcelSync(\Period::getDates());

        return Api::getTimesheet(\Period::getDates());
    }
}

?>