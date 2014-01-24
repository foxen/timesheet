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
    
    public function getTimesheet($datesArray){}
    
    public function getGrafIds(){
        $query = "select distinct graf_id from staff where graf_id > 0";
        return $this->objectsToFlatArray(\DB::select($query));
    }

    public function putIntervals($intervalsArray){
        
        return $this->insertAssoc("intervals", $intervalsArray, true, false);
    }

    public function parse($datesArray, $readersArray){
        
        $datesArray[0] = date('Y-m-d',strtotime($datesArray[0]));
        $datesArray[1] = date('Y-m-d',strtotime($datesArray[1]));

        $res = $this->updateControllers($datesArray, $readersArray);
        $res = $res && $this->deleteAllTmp();
        $res = $res && $this->insertIns($datesArray);
        $res = $res && $this->insertOuts($datesArray);
        $res = $res && $this->deleteNullOuts();
        $res = $res && $this->updateHours();
        $res = $res && $this->fillStaffData();
        $res = $res && $this->fillDow();
        $res = $res && $this->fillPrev();
        $res = $res && $this->fillInt();
        $res = $res && $this->fillDelay();
        $res = $res && $this->insertParsed();

        return $res;
    
    }
//================================================================================
    private function updateControllers($datesArray, $readersArray){

        $readersOutArray = array();
        $readersDriveArray = array();

        foreach ($readersArray as $key=>$value){
            

            $fromDt = $value['fromDt'] != '' ? date('Y-m-d',strtotime($value['fromDt'])) : "2013-01-01";
            $toDt   = $value['toDt']   != '' ? date('Y-m-d',strtotime($value['toDt']))   : "2023-01-01";
            $fromTm = $value['fromTm'] != '' ? $value['fromTm'] : "00:00:00";
            $toTm   = $value['toTm']   != '' ? $value['toTm']   : "00:00:00";

            $whereRow = "(ev_src = ".$key." and ".
                                 "dt_tm > '". $fromDt." ". $fromTm."' and ".
                                 "dt_tm < '". $toDt  ." ". $toTm."') ";
            
            
            if ($value['direction']=='out'){
                $readersOutArray[] = $whereRow;
            }

            if ($value['area']=='drive'){
                $readersDriveArray[] = $whereRow;
            }            
        
        }

        if (!empty($readersOutArray)){
            $whereOut = "where ".implode("or ", $readersOutArray)." and dt > '".$datesArray[0]."'";
            $queryOut = "update events set direction = 'out' ".$whereOut." and dt < '".$datesArray[1]."'";
            $res = \DB::statement($queryOut);
        }

        
        if (!empty($readersDriveArray)){
            $whereDrive = "where ".implode("or ", $readersDriveArray)." and dt > '".$datesArray[0]."'";
            $queryDrive = "update events set area = 'drive' ".$whereDrive." and dt < '".$datesArray[1]."'";
            $res = $res && \DB::statement($queryDrive);
        }

        return  $res;
    }
    
    private function deleteAllTmp(){
        $query = "delete from tmp_parsed";
        return \DB::statement($query);
    }

    private function insertIns($datesArray){
        
        $query = "insert into tmp_parsed (in_id, staff_id, in_datetime, dt)
                    SELECT distinct id, staff_id, dt_tm, dt 
                    FROM events WHERE direction = 'in'"
                    ." and dt > '".$datesArray[0]."'"
                    ." and dt < '".$datesArray[1]."'";

        return \DB::statement($query);

    }

    private function insertOuts($datesArray){

        $query = "update tmp_parsed, events 
                    
                    SET tmp_parsed.out_datetime = events.dt_tm, 
                        tmp_parsed.out_id=events.id 
                    
                    WHERE tmp_parsed.staff_id = events.staff_id 
                      AND tmp_parsed.out_datetime is NULL 
                      AND events.dt_tm >= tmp_parsed.in_datetime"
                    ." and events.dt > '".$datesArray[0]."'"
                    ." and events.dt < '".$datesArray[1]."'"
                    ." and events.direction = 'out'";

        $queryD = "delete from tmp_parsed where out_datetime is NULL";

        return \DB::statement($query) && \DB::statement($queryD);
    
    }

    private function deleteNullOuts(){
        $query = "delete from tmp_parsed where out_datetime is NULL";

        $queryD = "delete from t1 using tmp_parsed as t1, tmp_parsed as t2 
                            WHERE t1.out_datetime=t2.out_datetime  
                                AND t1.staff_id=t2.staff_id  
                                AND t1.in_id < t2.in_id";
        
        return \DB::statement($query) && \DB::statement($queryD);
    }

    private function updateHours(){
        $query = "update tmp_parsed set hours=timediff(out_datetime,in_datetime)";
        //$queryU = "update tmp_parsed set hours='8:00:00' where hours > '25:00:00'";
        return \DB::statement($query);// && \DB::statement($queryU);
    }

    private function fillStaffData(){
        $query = "update tmp_parsed, staff set 
                    tmp_parsed.subdiv = staff.subdiv,
                    tmp_parsed.appoint = staff.appoint,
                    tmp_parsed.graf_id = staff.graf_id,
                    tmp_parsed.name = staff.name
                where tmp_parsed.staff_id = staff.id";
        return \DB::statement($query);
    }

    private function fillDow(){
        $query  = "update tmp_parsed set dow = (DAYOFWEEK(in_datetime)-1)";
        $queryU = "update tmp_parsed set dow = 7 where dow = 0";

        return \DB::statement($query) && \DB::statement($queryU);
    }

    private function fillPrev(){
        $query =   "update tmp_parsed t1, (select * from tmp_parsed order by in_id desc) t2 set 
                        t1.prev_out_datetime = t2.out_datetime
                    where 
                        t1.in_id > t2.in_id 
                    and 
                        t1.staff_id = t2.staff_id";
        return \DB::statement($query);
    }

    private function fillInt(){
        $query =   "update tmp_parsed, intervals set 
                        tmp_parsed.gr_in = intervals.time_begin,
                        tmp_parsed.int_id = intervals.int_id
                    where 
                        tmp_parsed.in_datetime >  concat( tmp_parsed.dt, ' ', intervals.time_begin)
                    and 
                        tmp_parsed.in_datetime < concat( tmp_parsed.dt, ' ', intervals.time_end)
                    and 
                        tmp_parsed.dow = intervals.day_number
                    and 
                        tmp_parsed.graf_id = intervals.graf_id
                    and 
                        (
                            tmp_parsed.prev_out_datetime < concat( tmp_parsed.dt, ' ', intervals.time_begin) 
                        or 
                            tmp_parsed.prev_out_datetime is null)";
        
        return \DB::statement($query);   
    }

    private function fillDelay(){
        $query = "  update tmp_parsed set 
                        delay = timediff(in_datetime, concat(dt, ' ', gr_in)) 
                    where 
                        gr_in is not null";

        $queryU =   "update tmp_parsed set 
                        delay = '00:00:00' 
                    where 
                        gr_in is null";

        return \DB::statement($query) && \DB::statement($queryU);
    }

    private function insertParsed(){
        $query = "insert into parsed (in_id, out_id, staff_id, subdiv, appoint, name, graf_id, in_datetime, out_datetime, 
                    prev_out_datetime, dt, hours, dow, gr_in, delay, int_id, created_at, updated_at) 
                select 
                    in_id, out_id, staff_id, subdiv, appoint, name, graf_id, in_datetime, out_datetime, prev_out_datetime,
                        dt, hours, dow, gr_in, delay, int_id, now()  as created_at, now() as updated_at
                from tmp_parsed 
                on duplicate key update
                    out_id = values(out_id), 
                    staff_id = values(staff_id), 
                    subdiv = values(subdiv), 
                    appoint = values(appoint), 
                    name = values(name), 
                    graf_id = values(graf_id), 
                    in_datetime = values(in_datetime), 
                    out_datetime = values(out_datetime), 
                    prev_out_datetime = values(prev_out_datetime), 
                    dt = values(dt), 
                    hours = values(hours), 
                    dow = values(dow), 
                    gr_in = values(gr_in), 
                    delay = values(delay), 
                    int_id = values(int_id), 
                    updated_at = values(updated_at)";
        return \DB::statement($query);
    }

//================================================================================
    

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