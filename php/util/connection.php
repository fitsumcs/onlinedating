<?php
        
        function getDatabase()
        {
            $host = 'localhost';
            $user ='root';
            $password = '7993';
            $database = 'datingsystem';
            
            $db = new mysqli($host,$user,$password,$database);
            
            if(mysqli_connect_errno())
            {
                return false;
            }
            else 
            {
                return $db;    
            }
            
        }

?>