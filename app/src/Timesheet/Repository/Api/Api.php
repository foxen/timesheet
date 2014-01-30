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
        
        $fixedHeadArray  = array(   array( 'id'         => '0_name',
                                           'value'      => 'Фамилия Имя Отчество',
                                           'col_type'   => 'col_name',
                                           'attributes' => array(
                                                'fixed_head' => true,
                                                'name'       => true,
                                                'row_id_0'   => true),),
                                    array( 'id'         => '0_subdiv',
                                           'value'      => 'Отдел',
                                           'col_type'   => 'col_subdiv',
                                           'attributes' => array(
                                                'fixed_head' => true,
                                                'subdiv'     => true,
                                                'row_id_0'   => true,),),

                                    array( 'id'         => '0_appount',
                                           'value'      => 'Должность',
                                           'col_type'   => 'col_appoint',
                                           'attributes' => array(
                                                'fixed_head' => true,
                                                'appoint'    => true,
                                                'row_id_0'   => true,),),);
        
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
                
                $freeDay = in_array($value, $freeDaysArray) ? 'freeday' : 'workday';
                
                $headArray[] =  array(  'id'         => '0_'. $value,
                                        'value'      => substr($value,-2,2).".".substr($value,5,2),
                                        'col_type'   => 'col_day',
                                        'attributes' => array(
                                            'head'     => true,
                                            $value     => true,
                                            'row_id_0' => true,
                                            $freeDay   => true,),);
            }
            $o = $o + 1;
        }

        $headArray[] =  array(  'id'         => '0_ttl-t',
                                'value'      => 'Отработано',
                                'col_type'   => 'col_ttl-t',
                                'attributes' => array(
                                    'head'     => true,
                                    'ttl-t'    => true,
                                    'row_id_0' => true,),);

        $headArray[] =  array(  'id'         => '0_ttl-d',
                                'value'      => 'Опоздания',
                                'col_type'   => 'col_ttl-d',
                                'attributes' => array(
                                    'head'     => true,
                                    'ttl-d'    => true,
                                    'row_id_0' => true,),);

        

        foreach ($dataArray as $row) {
            $id = $row['staff_id'];
            $fixedArray[] = array(  array( 
                                        'id'     => $id.'_name',
                                        'value'      => $row['name'],
                                        'col_type'   => 'col_name',
                                        'attributes' => array(
                                            'fixed'       => true,
                                            'name'        => true,
                                            'row_id_'.$id => true,),),
                                    array(
                                        'id'     => $id.'_subdiv',
                                        'value'      => $row['subdiv'],
                                        'col_type'   => 'col_subdiv',
                                        'attributes' => array(
                                            'fixed'       => true,
                                            'subdiv'      => true,
                                            'row_id_'.$id => true,),),
                                    array(
                                        'id'     => $id.'_appoint',
                                        'value'      => $row['appoint'],
                                        'col_type'   => 'col_appoint',
                                        'attributes' => array(
                                            'fixed'       => true,
                                            'appoint'      => true,
                                            'row_id_'.$id => true,),),);
            
            $newRowArray = array();
            
            foreach (array_slice($row,4,-2) as $key=>$value){
                $column = substr($key,0,-2);
                $freeDay = in_array(substr($key,0,-2), $freeDaysArray)? 
                                                    'freeday' : 'workday';
                $workTime = substr($key, -1) == 't' ? 'worktime' : 'delay';
                $masked = $value == '00:00' ? 'masked' : 'normal';
                $incorrect = in_array(substr($key,0,-2), $incorrectDaysArray)? 
                                                    'incorrect' : 'correct';
                                                    
                $cell = array(  'id'         => $id.'_'.$column,
                                'value'      => $value,
                                'col_type'   => 'col_'.$workTime,
                                'attributes' => array(
                                    'row_id_'.$id => true,
                                    $column       => true,
                                    $workTime     => true,
                                    $masked       => true,
                                    $freeDay      => true,
                                    $incorrect    => true,),);
                $newRowArray[] = $cell;
            }
            
            $masked = $row['ttl-t'] == '00:00' ? 'masked' : 'normal';
            
            $newRowArray[] = array( 'id'         => $id.'_ttl-t',
                                    'value'      => $row['ttl-t'],
                                    'col_type'   => 'col_ttl-t',
                                    'attributes' => array(
                                        'row_id_'.$id     => true,
                                        'ttl-t'           => true,
                                        $masked           => true,),);
            
            $masked = $row['ttl-d'] == '00:00' ? 'masked' : 'normal';
            
            $newRowArray[] = array( 'id'     => $id.'_ttl-d',
                                    'value'      => $row['ttl-d'],
                                    'col_type'   => 'col_ttl-d',
                                    'attributes' => array(
                                        'row_id_'.$id    => true,
                                        'ttl-d'          => true,
                                        $masked          => true,),);

            $bodyArray[] = $newRowArray;

        }
                                
        return array( 'fixed_head' => array($fixedHeadArray),
                      'head'       => array($headArray),
                      'fixed'      => $fixedArray,
                      'body'       => $bodyArray);
    }

}

