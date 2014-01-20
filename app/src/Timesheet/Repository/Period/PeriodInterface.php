<?php namespace Timesheet\Repository\Period;

interface PeriodInterface{
    public function getStartDate();
    public function getEndDate();
    public function getDates();
}

?>