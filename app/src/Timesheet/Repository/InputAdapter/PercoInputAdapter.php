<?php namespace Timesheet\Repository\InputAdapter;
use Timesheet\Repository\SimpleDb\SimpleDbInterface;

class PercoInputAdapter implements InputAdapterInterface{
    
    private $dbAdapter;


    public function __construct(SimpleDbInterface $dbAdapter){
        $this->dbAdapter = $dbAdapter;
    }

    public function getStuff($datesArray){
        $query =    "select 
                        st_rf.STAFF_ID as id,
                        st_rf.NAME as name, 
                        SUBDIV_REF.DISPLAY_NAME as subdiv,
                        APPOINT_REF.DISPLAY_NAME as appoint,
                        st_rf.GROUP_WT_ID as graf_id
                    from                       
                       (select 
                            ref.*, 
                           (upper(left(STAFF.LAST_NAME,1))   || lower(substring(STAFF.LAST_NAME from 2))  || ' ' || 
                            upper(left(STAFF.FIRST_NAME,1))  || lower(substring(STAFF.FIRST_NAME from 2)) || ' ' || 
                            upper(left(STAFF.MIDDLE_NAME,1)) || lower(substring(STAFF.MIDDLE_NAME from 2))) as name 
                        from 
                            STAFF 
                        
                        left join 
                            (select 
                                d.staff_id, 
                                b.* 
                            from 
                                (select 
                                    a.STAFF_ID, 
                                    max(a.ID_STAFF_REF) as ID_STAFF_REF 
                                from 
                                    STAFF_REF a  
                                group by 
                                    a.STAFF_ID) d

                            join 
                                (select 
                                    c.ID_STAFF_REF, 
                                    c.SUBDIV_ID, 
                                    c.APPOINT_ID, 
                                    c.GROUP_WT_ID 
                                from 
                                    STAFF_REF c) b 
                        
                            on 
                                d.ID_STAFF_REF = b.ID_STAFF_REF) ref
                  
                    on 
                        STAFF.ID_STAFF = ref.STAFF_ID
                        where (STAFF.DATE_DISMISS > '".$datesArray[0]."') or (STAFF.DATE_DISMISS is NULL)) st_rf 
                
                left join 
                    SUBDIV_REF 
                on 
                    SUBDIV_REF.ID_REF = st_rf.SUBDIV_ID
           
                left join 
                    APPOINT_REF 
                on 
                    st_rf.APPOINT_ID = APPOINT_REF.ID_REF";

        return $this->dbAdapter->rawSelect($query);

    }
    
    public function getEvents($datesArray, $controllersArray){

        $wh = '';

        foreach ($controllersArray as $key => $value){
            
            $fromDt = $value['fromDt'] != '' ? $value['fromDt'] : "01.01.2013";
            $toDt   = $value['toDt']   != '' ? $value['toDt']   : "01.01.2023";
            $fromTm = $value['fromTm'] != '' ? $value['fromTm'] : "00:00:00";
            $toTm   = $value['toTm']   != '' ? $value['toTm']   : "00:00:00";

            $whR = "(a.CONFIGS_TREE_ID_CONTROLLER='".$key.
                    "' and  a.DATE_EV + a.TIME_EV > '". $fromDt." ". $fromTm."' ". 
                    "and  a.DATE_EV + a.TIME_EV < '". $toDt." ". $toTm."') or ";
            
            $wh =  $wh. $whR;         
        }

        $wh = "(".substr($wh,0,-3).") and ";

        $query = "select a.ID_REG as id,
                     a.DATE_EV as dt,
                     a.TIME_EV as tm,
                     a.STAFF_ID as staff_id,
                     a.CONFIGS_TREE_ID_RESOURCE as ev_src
            from REG_EVENTS a
            where ". $wh .
            "DATE_EV>'" . $datesArray[0] .
             "' and a.DATE_EV<'" . $datesArray[1] .
             "' and a.STAFF_ID is not null" .
             "  and a.INNER_NUMBER_EV in (17,27,16)";

        return $this->dbAdapter->rawSelect($query);
    }

    public function getIntervals($dayIntervalIdsArray) {

        $where = "";

        if (!(empty($dayIntervalIdsArray))){
            
            $whereArray = array_map(function($row){
            return " a.ID_GROUP_WT_MAIN = ".$row;
            },$dayIntervalIdsArray);
        
            $where = "where ".implode("or ", $whereArray);

        }

        $query = "select 
                        d.id_group_wt_main as graf_id, 
                        d.day_number as day_number, 
                        e.ID_GR_WORK_INTERVALS as int_id, 
                        e.TIME_BEGIN as time_begin, 
                        e.TIME_END as time_end 
                    from GR_WORK_INTERVALS e 
                    left join
                    
                        (SELECT 
                            b.ID_GROUP_WT_MAIN, 
                            c.DAY_NUMBER, 
                            c.GR_WORK_REFERENCE_INT_ID 
                        from GR_WORK_DAYS c 
                        left join
                        
                            (SELECT 
                                a.ID_GROUP_WT_MAIN, 
                                a.GR_WORK_REFERENCE_ID 
                                FROM GR_WORK_MAIN a ".$where.") b
                        
                        on c.REFERENCE_GR_WORK_ID = b.GR_WORK_REFERENCE_ID
            
                        where b.ID_GROUP_WT_MAIN is not null 
                    
                        and c.GR_WORK_REFERENCE_INT_ID > 0) d
                    
                    on e.REFERENCE_GR_WORK_ID = d.GR_WORK_REFERENCE_INT_ID
                    
                    where d.GR_WORK_REFERENCE_INT_ID is not null
            
                    order by d.id_group_wt_main, d.day_number";
        
        return $this->dbAdapter->rawSelect($query);
    }

}