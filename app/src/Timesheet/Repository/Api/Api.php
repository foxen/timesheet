<?php namespace Timesheet\Repository\Api;

use Timesheet\Repository\Data\DataInterface;
use Timesheet\Repository\Days\DaysInterface;

class Api implements ApiInterface{
    
    protected $Data;
    protected $Days;

    public function __construct(DataInterface $Data, DaysInterface $Days){
        $this->Data = $Data;
        $this->Days = $Days;
    }

    public function getTimesheet($datesArray){
        $timesheetArray = $this->Data->getTimesheet($datesArray);
        return \Response::json($this->getLockedArray($timesheetArray));
    }

    private function getLockedArray($dataArray){
        
        $sideHeadArray = array( 'name'    => 'Фамилия Имя Отчество',
                                'subdiv'  => 'Отдел',
                                'appoint' => 'Должность');
        
        $fixedArray    = array();
        $bodyArray  = array();

        $headRowArray  = array_slice(array_keys($dataArray[0]),4,-2);
        $headArray = array();
        $headRowArray =    array_map(function($row){
                            return substr($row,0,-2);
                        }, array_slice(array_keys($dataArray[0]),4,-2));
        
        foreach ($headRowArray as $value){
            $headArray[$value] = substr($value,-2,2).".".substr($value,5,2);    
        }

        $freeDaysArray = array_intersect($headRowArray,$this->Days->getFreeDays());
        $incorrectDaysArray = array_intersect($headRowArray,$this->Days->getIncorrectDays());

        foreach ($dataArray as $row) {
            $id = $row['staff_id'];
            $fixedArray[] = array('staff_id'=>$id,
                                   'name' => $row['name'],
                                   'subdiv' => $row['subdiv'],
                                   'appoint' => $row['appoint']);
            $newRowArray = array();
            foreach (array_slice($row,4,-2) as $key=>$value){
                $cell = array(  'staff_id'  => $id,
                                'value'     => $value,
                                'date'      => substr($key,0,-2),
                                'type'      => substr($key, -1) == 't' ? 'worktime' : 'delay',
                                'masked'    => $value == '00:00' ? 'masked' : 'normal',
                                'freeday'   => in_array(substr($key,0,-2), $freeDaysArray)? 
                                                    'freeday' : 'workday',
                                'incorrect' => in_array(substr($key,0,-2), $incorrectDaysArray)? 
                                                    'incorrect' : 'correct');
                $newRowArray[] = $cell;
            }
            $bodyArray[] = $newRowArray;

        }
                                
        return array( 'sideHead' => $sideHeadArray,
                      'headArray'  => $headArray,
                      'fixedArray'  => $fixedArray,
                      'bodyArray'  => $bodyArray);
    }

}

