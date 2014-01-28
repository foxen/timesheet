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
        
        $fixedHeadArray  = array(   array( 'row_id'     => '0',
                                           'value'      => 'Фамилия Имя Отчество',
                                           'column'     => 'name',
                                           'attributes' => array(
                                                'fixed_head'   => 'fixed_head',
                                                'fixed_column' => 'name',),),
                                    array( 'row_id'     => '0',
                                           'value'      => 'Отдел',
                                           'column'     => 'subdiv',
                                           'attributes' => array(
                                                'fixed_head'   => 'fixed_head',
                                                'fixed_column' => 'subdiv',),),

                                    array( 'row_id'     => '0',
                                           'value'      => 'Должность',
                                           'column'     => 'appoint',
                                           'attributes' => array(
                                                'fixed_head'   => 'fixed_head',
                                                'fixed_column' => 'appoint',),),);
        
        $fixedArray = array();
        $bodyArray  = array();

        $headArray = array();
        $headRawArray =    array_map(function($row){
                            return substr($row,0,-2);
                        }, array_slice(array_keys($dataArray[0]),4,-2));
        
        $freeDaysArray = array_intersect($headRawArray,$this->Days->getFreeDays());
        $incorrectDaysArray = array_intersect($headRawArray,$this->Days->getIncorrectDays());


        $o = 1;
        foreach ($headRawArray as $value){
            if ($o%2 != 0){
                $headArray[] =  array(  'row_id'     => '0',
                                        'value'      => substr($value,-2,2).".".substr($value,5,2),
                                        'column'     => $value,
                                        'attributes' => array(
                                            'head'     => 'head',
                                            'head_date' => 'head_date',
                                            'freeday'   => in_array($value, $freeDaysArray)? 
                                                            'freeday' : 'workday',),);
            }
            $o = $o + 1;
        }

        $headArray[] =  array(  'row_id'     => '0',
                                'value'      => 'Отработано',
                                'column'     => 'ttl-t',
                                'attributes' => array(
                                    'head'     => 'head',
                                    'head_ttl-t' => 'head_ttl-t',),);

        $headArray[] =  array(  'row_id'     => '0',
                                'value'      => 'Опозданя',
                                'column'     => 'ttl-d',
                                'attributes' => array(
                                    'head'     => 'head',
                                    'head_ttl-d' => 'head_ttl-d',),);

        

        foreach ($dataArray as $row) {
            $id = $row['staff_id'];
            $fixedArray[] = array(  array( 
                                        'row_id'     => $id,
                                        'value'      => $row['name'],
                                        'column'     => 'name',
                                        'attributes' => array(
                                            'fixed'        => 'fixed',
                                            'fixed_column' => 'name',),),
                                    array(
                                        'row_id'     => $id,
                                        'value'      => $row['subdiv'],
                                        'column'     => 'subdiv',
                                        'attributes' => array(
                                            'fixed'        => 'fixed',
                                            'fixed_column' => 'subdiv',),),
                                    array(
                                        'row_id'     => $id,
                                        'value'      => $row['appoint'],
                                        'column'     => 'appoint',
                                        'attributes' => array(
                                            'fixed'        => 'fixed',
                                            'fixed_column' => 'appoint'),),);
            
            $newRowArray = array();
            foreach (array_slice($row,4,-2) as $key=>$value){
                $cell = array(  'row_id'     => $id,
                                'value'      => $value,
                                'column'     => substr($key,0,-2),
                                'attributes' => array(
                                    'type'      => substr($key, -1) == 't' ? 'worktime' : 'delay',
                                    'masked'    => $value == '00:00' ? 'masked' : 'normal',
                                    'freeday'   => in_array(substr($key,0,-2), $freeDaysArray)? 
                                                    'freeday' : 'workday',
                                    'incorrect' => in_array(substr($key,0,-2), $incorrectDaysArray)? 
                                                    'incorrect' : 'correct',),);
                $newRowArray[] = $cell;
            }
            $newRowArray[] = array( 'row_id'     => $id,
                                    'value'      => $row['ttl-t'],
                                    'column'     => 'ttl-t',
                                    'attributes' => array(
                                            'type'   => 'ttl-t',
                                            'masked' => $row['ttl-t'] == '00:00' ? 'masked' : 'normal',),);
            $newRowArray[] = array( 'row_id'     => $id,
                                    'value'      => $row['ttl-d'],
                                    'column'     => 'ttl-d',
                                    'attributes' => array(
                                            'type'   => 'ttl-d',
                                            'masked' => $row['ttl-d'] == '00:00' ? 'masked' : 'normal',),);

            $bodyArray[] = $newRowArray;

        }
                                
        return array( 'fixedHead' => $fixedHeadArray,
                      'head'      => $headArray,
                      'fixed'     =>$fixedArray,
                      'body'      => $bodyArray);
    }

}

