<?php namespace Timesheet\Repository\Sync;
interface SyncInterface{
    public function parcelSync($datesArray);
    public function fullSync($datesArray);
}
?>