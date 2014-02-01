<?php namespace Timesheet\Repository\Period;

class ConfigPeriod implements PeriodInterface {

    public function getStartDate(){
        return date('d.m.Y', strtotime( "-1 day",
            strtotime(\Config::get('period.start_date'))));
    }

    public function getEndDate(){
        return date('d.m.Y', strtotime( "+1 month",
            strtotime(\Config::get('period.start_date'))));
    }

    public function getDates(){

        return array($this->getStartDate(),
            $this->getEndDate());
    }    

}

?>