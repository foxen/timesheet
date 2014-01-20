<?php namespace Timesheet\Repository\SimpleDb;
    /**
     * Интерфейс простого адаптера чтения базы данных
     *
    */
    interface SimpleDbInterface {

        public function rawSelect($queryTxt);
        
    }

?>