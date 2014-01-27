<?php namespace Timesheet\Repository\Api;

use Timesheet\Repository\Data\DataInterface;

class Api implements ApiInterface{
    
    protected $Data;

    public function __construct(DataInterface $Data){
        $this->Data = $Data;
    }

    public function getTimesheet($datesArray){
        $timesheetArray = $this->Data->getTimesheet($datesArray);
        return \Response::json($this->getLockedArray($timesheetArray,3));
    }

    private function getLockedArray($dataArray, $left = 1){
        
        $sideArray     = array();
        $bodyArray     = array();
        $keysArray     = array();
        $i = 0;
        foreach(array_keys($dataArray[0]) as $value){
            $keysArray[$i] = $value;
            $i++;
        }
        $sideHeadArray = array_slice($keysArray, 0,$left);
        $headArray     = array_slice($keysArray,$left);

        foreach($dataArray as $row){
            $sideArray[] = array_slice($row, 0,$left);
            $bodyArray[] = array_slice($row, $left);
        }

        return array( 'sh' => $sideHeadArray,
                      'h'  => $headArray,
                      's'  => $sideArray,
                      'b'  => $bodyArray);
    }

}

