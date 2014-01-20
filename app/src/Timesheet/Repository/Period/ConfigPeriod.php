<?php namespace Timesheet\Repository\Period;

class ConfigPeriod implements PeriodInterface {

    public function getStartDate(){
        return \Config::get('period.start_date');
    }

    public function getEndDate(){
        
        return date('d.m.Y', strtotime( "+1 month",
            strtotime($this->getStartDate())));
    }

    public function getDates(){
        return array($this->getStartDate(),
            $this->getEndDate());
    }    

}

?>