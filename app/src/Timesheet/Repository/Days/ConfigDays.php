<?php namespace Timesheet\Repository\Days;

class ConfigDays implements DaysInterface {

    public function getFreeDays(){
        return \Config::get('days.freedays');
    }

    public function getIncorrectDays(){
        return \Config::get('days.incorrectdays');
    }
}