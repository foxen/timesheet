<?php namespace Timesheet\Repository\Data;
interface DataInterface{
    public function putStaff($staffArray);
    public function putEvents($eventsArray);
    public function getLastEventDate();
    public function getFirstEventDate();
    public function putIntervals($intervalsArray);
    public function parse($datesArray, $readersArray);
    public function getTimesheet($datesArray);
    public function getGrafIds();
    public function setSyncState();
    public function unsetSyncState();
}
?>