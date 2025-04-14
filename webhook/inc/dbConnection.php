<?php

 class database{


    private $host = "localhost";
    private $db_name = "nexustr2_bitrader";
    private $username = "nexustr2_bitrader123";
    private $password = "Bitrader#1";
    public $conn;
  
    // get the database connection
      public function getConnection(){
  
        $this->conn = null;
  
        try{
            $this->conn = new mysqli($this->host,$this->username, $this->password, $this->db_name);
    
		//echo 'Success';
        }catch(Exception $exception){
            echo "Connection error: " . $exception->getMessage();
        }
  
        return $this->conn;
    }
	
	
}


?>
