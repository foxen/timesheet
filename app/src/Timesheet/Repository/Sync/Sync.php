<?php namespace Timesheet\Repository\Sync;

use Timesheet\Repository\Period\PeriodInterface;
use Timesheet\Repository\Devices\DevicesInterface;
use Timesheet\Repository\Data\DataInterface;
use Timesheet\Repository\InputAdapter\InputAdapterInterface;

class Sync implements SyncInterface{
    
    protected $Period;
    protected $Devices;
    protected $Data;
    protected $InputAdapter;

    public function __construct(PeriodInterface  $Period, 
                                DevicesInterface $Devices,
                                DataInterface $Data,
                                InputAdapterInterface $InputAdapter){
        $this->Period       = $Period;
        $this->Devices      = $Devices;
        $this->Data         = $Data;
        $this->InputAdapter = $InputAdapter;


    }

    public function parcelSync($datesArray = ''){

        $newDatesArray = array();

        if($datesArray == ''){
            $datesArray = $this->Period->getDates();
        }
        
        $lastDt = $this->Data->getLastEventDate();
        $firstDt = $this->Data->getFirstEventDate();
        
        $newDatesArray = $datesArray;

        if((strtotime($firstDt) <= strtotime($datesArray[0]))&&
            (strtotime($datesArray[1])<= strtotime($lastDt))){
        
            return true;
        
        }

        if((strtotime($datesArray[0]) < strtotime($firstDt)) &&
            (strtotime($datesArray[1]) >= strtotime($firstDt))) {
            
            $newDatesArray[1] = date('d.m.Y', strtotime("+1 day", strtotime($firstDt)) );            
        
        }
        
        if(strtotime($lastDt) < strtotime($datesArray[1])){
        
            $newDatesArray[0] = date('d.m.Y', strtotime("-1 day", strtotime($lastDt)) );
        
        }

        return $this->sync($newDatesArray) && $this->parse($newDatesArray);

    }
    
    public function fullSync($datesArray = ''){
        

        if($datesArray == ''){
            $datesArray = $this->Period->getDates();
        }
        
        $firstDt = $this->Data->getFirstEventDate();
        $lastDt = $this->Data->getLastEventDate();
        
        $newDatesArray = $datesArray;

        if (strtotime($datesArray[0]) < strtotime($firstDt)){
            $newDatesArray = array($datesArray[0], 
                                   date('d.m.Y', strtotime("+1 day", strtotime("now")) ) );
        }

        if (strtotime($datesArray[0]) > strtotime($lastDt)){
            $newDatesArray = array($lastDt,$datesArray[1]);
        }        

        
        return $this->sync($newDatesArray) && $this->parse($newDatesArray);
    }

    private function sync($datesArray){
        
        $controllersArray = $this->Devices->getControllers();
        
        $resStaff       = $this->Data->putStaff(
                            $this->InputAdapter->getStuff($datesArray));
        
        $resEvents      = $this->Data->putEvents(
                            $this->InputAdapter->getEvents($datesArray,$controllersArray));
        
        $grafIdsArray   = $this->Data->getGrafIds();
        $intervalsArray = $this->InputAdapter->getIntervals($grafIdsArray);

        $resIntervals   = $this->Data->putIntervals($intervalsArray);
        
        return $resStaff && $resEvents && $resIntervals;
    }

    private function parse($datesArray){
        $controllersArray = $this->Devices->getReaders();
        return $this->Data->parse($datesArray,$controllersArray);
    }
}



?>