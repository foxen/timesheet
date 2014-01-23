<?php namespace Timesheet\Repository\InputAdapter;

interface InputAdapterInterface{
    public function getStuff($datesArray);
    public function getEvents($datesArray,$controllersArray);
    public function getIntervals($stuffIdsArray);
}

?>