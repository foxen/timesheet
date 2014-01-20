<?php namespace Timesheet\Repository\SimpleDb;

class InterbaseSimpleDb implements SimpleDbInterface {

    private $db;
    
    private $username;

    private $password;

    public $connection;

    public function __construct($configArray){

        $this->db         = $configArray['db'];
        $this->username   = $configArray['username'];
        $this->password   = $configArray['password'];
        $this->connection = $this->connectDb();

    }

    public function connectDb(){
        
        $connection = ibase_connect(
            $this->db,
            $this->username,
            $this->password
        );
        
        return $connection;
    
    }

    public function rawSelect($queryTxt){
        
        $response = array();

        $query = ibase_query($this->connection, $queryTxt);

        while ($row = ibase_fetch_row($query)) {
            $response[] = $row;
        }

        ibase_free_result($query);
        
        return $response;
    
    }

    public function closeConnection(){
        
        ibase_close($connect_fdb);
    
    }

    public function __destruct(){
        
        try {
            $this->closeConnection();
        } catch (Exeption $e) {}

    }

}

?>