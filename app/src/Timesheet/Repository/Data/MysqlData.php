<?php namespace Timesheet\Repository\Data;

class MysqlData implements DataInterface{
    
    public function putStaff($staffArray){

        return $this->insertAssoc("staff", $staffArray, true, false);
    }
    
    public function putEvents($eventsArray){
        $resInsert = $this->insertAssoc("events", $eventsArray, true, false);
        $query = "update events set dt_tm = addtime(dt,tm)";
        $resUpdate = \DB::statement($query);
        return $resInsert && $resUpdate;
    }
    
    public function getLastEventDate(){
        $query = "select date_format(dt,'%d.%m.%Y') 
                    from events order by dt_tm desc limit 1";

        $ret = $this->objectsToFlatArray(\DB::select($query));
        if(empty($ret)){
            return "31.08.13";
        }
        return $ret[0];
    }

    public function getFirstEventDate(){
        $query = "select date_format(dt,'%d.%m.%Y') 
                    from events order by dt_tm limit 1";
        $ret = $this->objectsToFlatArray(\DB::select($query));
        if(empty($ret)){
            return "01.08.13";
        }
        return $ret[0];
    }
    
    public function parse($datesArray, $readersArray){
        
        $readersOutArray = array();

        foreach ($readersArray as $key=>$value){
            if ($value['direction']=='out'){

                $fromDt = $value['fromDt'] != '' ? date('Y-m-d',strtotime($value['fromDt'])) : "2013-01-01";
                $toDt   = $value['toDt']   != '' ? date('Y-m-d',strtotime($value['toDt']))   : "2023-01-01";
                $fromTm = $value['fromTm'] != '' ? $value['fromTm'] : "00:00:00";
                $toTm   = $value['toTm']   != '' ? $value['toTm']   : "00:00:00";

                $readersOutArray[] = "(ev_src = ".$key." and ".
                                     "dt_tm > '". $fromDt." ". $fromTm."' and ".
                                     "dt_tm < '". $toDt  ." ". $toTm."') ";
            }
        }

        $where = "where ".implode("or ", $readersOutArray);
        print_r($query = "update events set direction = 'out' ".$where);
        return true;
        //return $resUpdate = \DB::statement($query);
    }
    
    public function getTimesheet($datesArray){}
    
    public function getGrafIds(){
        $query = "select distinct graf_id from staff where graf_id > 0";
        return $this->objectsToFlatArray(\DB::select($query));
    }

    public function putIntervals($intervalsArray){
        return $this->insertAssoc("intervals", $intervalsArray, true, false);
    }

//============================================================================== 

    private function insertAssoc($table, $assocArray, $isUpsert = false, $isIgnore = false){
        
        $ignore    = $isIgnore  ? " ignore" : "";
        
        $fieldsArray =array_map(function($row){
                                    return strtolower($row);
                                },array_keys($assocArray[0]));

        $fields = implode(",",$fieldsArray).", created_at, updated_at";
        
        $values = implode(",", 
                             array_map(function($row){
                                        return "('".implode("','",$row).
                                            "', now(), now())";
                                       },
                                       $assocArray));
        
        $onDuplicate = '';

        if ($isUpsert && !$isIgnore){

            $tableKeysArray = $this->getTableKeys($table);
            
            $onDuplicateArray = array();

            foreach($fieldsArray as $key){
                $onDuplicateArray[] = $key."=values(".$key.")";
           }

            $duplicateValues = implode(",",$onDuplicateArray).",created_at=created_at,updated_at=now()";

            $onDuplicate = " on duplicate key update ".$duplicateValues;
        }

        $query = "insert ".$ignore." into ".$table." (".$fields.") values ".$values.$onDuplicate;

        return \DB::statement($query);
    }

    private function objectsToArray($objectsArray){

        return array_map(function($row){
            return (array) $row;
        }, $objectsArray);
    }

    private function objectsToFlatArray($objectsArray){
        return array_map(function($row){
                            foreach($row as $key=>$value){}
                            return $value;
                        },$this->objectsToArray($objectsArray));
    }

//==============================================================================

    private function getTableKeys($table){
        // not used, replace in product!
        $dbNamesArray=\DB::select( "SELECT DATABASE() as db");
        
        $dbName = $dbNamesArray[0]->db;
        
        $query = "SELECT distinct column_name FROM information_schema.KEY_COLUMN_USAGE where table_name = '".$table."' and constraint_schema = '".$dbName."'";
        
        return 
        array_map( 
            function($row){
                return strtoupper($row['column_name']);
            },
            $this->objectsToArray(\DB::select($query))
        );    
    }

    private function removeKeys($assocArray,$tableKeysArray){
        
        // not used, replace in product!

        $resArray = array();

        foreach ($assocArray as $row){
            foreach ($tableKeysArray as $v) {
                unset($row[(string)$v]);
            }

        }
        
        return $assocArray;
    }

    


}


?>