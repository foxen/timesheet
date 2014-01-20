<?php

class TestController extends  BaseController {
    
    public function doit(){
        $a = Period::getDates();
        print_r($a);
    }
}

?>