<?php
require_once('../model/Message.php');

class MessageHandler{
    
    public $currentNIndex = 0;
    public $currentPIndex = 0;
    
    public function getNormalMessages($loaderEmail,$preferredEmail,$more=false,$index=0,$number=4)
        {
            if(!$more){
                $this->currentNIndex = 0;
            }
            else{
                $this->currentNIndex = $index;
            }
            
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in constructing NormalMessage for ".$loaderEmail."
                          interaction with ".$preferredEmail."</p>";
                 exit();
             }
             
             //check if a NormalMessage exists with given email
             $query = "select * from normalMessage
                       where ((sender='".$loaderEmail."' and reciever='".$preferredEmail."')
                          or (sender='".$preferredEmail."' and reciever='".$loaderEmail."')) 
                       order by time desc limit ".$this->currentNIndex.",".$number;
             $result = $db->query($query);
             if(!$result->num_rows){
                 return null;       //note that this can also run because of database query is not executed successfully 
             }
             
             //create message objects from database if validated and push in an array, then retrieve the array
             $messages = array();
             while($row = $result->fetch_assoc())
             {
                 $message = new NormalMessage(
                     $row['sender'],$row['reciever'],$row['textContent'],$row['photoContent'],$row['time'],$row['isSeen']);
                 array_push($messages,$message);
             }
             
             $result->free();
             $db->close();
             
             $this->currentNIndex += $number;
             
             return $messages;
        } 
    
    
        public function getPostMessages($loaderEmail,$more=false,$index=0,$number=6)
        {
            if(!$more){
                $this->currentPIndex = 0;
            }
            else{
                $this->currentPIndex = $index;
            }
            
             
             $loaderProfile = new Profile($loaderEmail);
             $nominees = $loaderProfile->profileInfo->getNominees();
             
             //create nomineesSet string using the nominees array for query 
             $nomineeSet = "(";
             $counter = 0;
             foreach($nominees as $nominee){            
                 if($counter == 0)              //enter comma if it is not first
                    $nomineeSet = $nomineeSet."'".$nominee."'";
                 else
                    $nomineeSet = $nomineeSet.",'".$nominee."'";
                 
                 ++$counter;
             }
             $nomineeSet = $nomineeSet.")";
             if(empty($nominees)){
                 $nomineeSet = "( 'empty' )";
             }
             //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in constructing PostMessage for ".$loaderEmail."</p>";
                 exit();
             }
             
             //check if a PostMessage exists with given nomineesSet and retrieve it
             $query = "select * from postMessage
                       where sender in ".$nomineeSet."   
                       order by time desc limit ".$this->currentPIndex.",".$number;
             $result = $db->query($query);
             if(!$result->num_rows){
                 return null;       //note that this can also run because of database query is not executed successfully 
             }
             
             //create message objects from database if validated and push in an array, then retrieve the array
             $messages = array();
             while($row = $result->fetch_assoc())
             {
                 $message = new PostMessage(
                     $row['sender'],$row['textContent'],$row['photoContent'],$row['time'],$row['comments'],$row['likes']);
                 array_push($messages,$message);
             }
             
             $result->free();
             $db->close();
             
             $this->currentPIndex = $this->currentPIndex + $number;
             return $messages;
        }
        
        public function getHerPosts($herEmail,$more=false,$index=0,$number=6)
        {
            if(!$more){
                $this->currentPIndex = 0;
            }
            else{
                $this->currentPIndex = $index;
            }
             
             //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in constructing PostMessage for ".$loaderEmail."</p>";
                 exit();
             }
             
             //check if a PostMessage exists with given nomineesSet and retrieve it
             $query = "select * from postMessage
                       where sender = '".$herEmail."'   
                       order by time desc limit ".$this->currentPIndex.",".$number;
             $result = $db->query($query);
             if(!$result->num_rows){
                 return null;       //note that this can also run because of database query is not executed successfully 
             }
             
             //create message objects from database if validated and push in an array, then retrieve the array
             $messages = array();
             while($row = $result->fetch_assoc())
             {
                 $message = new PostMessage(
                     $row['sender'],$row['textContent'],$row['photoContent'],$row['time'],$row['comments'],$row['likes']);
                 array_push($messages,$message);
             }
             
             $result->free();
             $db->close();
             
             $this->currentPIndex = $this->currentPIndex + $number;
             return $messages;
        }
        
        public function send($message){
            $message->save();
        }
        
        public function post($message){
            $message->save();
        }
        
        public function getUnseenMessages($email){
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in constructing PostMessage for ".$loaderEmail."</p>";
                 exit();
             }
             
             
             //check if there are  unseen messages exists with given email and retrieve it
             $query = "select * from normalMessage
                       where reciever = '".$email."' and isSeen = '0'   
                       order by time desc";
             $result = $db->query($query);
             if(!$result->num_rows){
                 return null;       //note that this can also run because of database query is not executed successfully 
             }
             
             //create message objects from database if validated and push in an array, then retrieve the array
             $messages = array();
             while($row = $result->fetch_assoc())
             {
                 $message = new NormalMessage(
                     $row['sender'],$row['reciever'],$row['textContent'],$row['photoContent'],$row['time'],$row['isSeen']);
                 array_push($messages,$message);
             }
             
             $result->free();
             $db->close();
             
             return $messages;
             
        }
        
        function loadPostMessage($time,$sender){
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in constructing PostMessage for ".$loaderEmail."</p>";
                 exit();
             }
             
             
             //check if there are  unseen messages exists with given email and retrieve it
             $query = "select * from postMessage
                       where time = '$time' and sender = '$sender'";
             $result = $db->query($query);
             $row = $result->fetch_assoc();
             $message = new PostMessage(
                     $row['sender'],$row['textContent'],$row['photoContent'],$row['time'],$row['comments'],$row['likes']);
                     
             return $message;
        }
        
             

}
?>