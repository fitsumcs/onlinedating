<?php

    require_once("../util/connection.php");

    /*function a2comments($array){                     array to comment is not totally done
        $likes = "";
        foreach($array as $element){
               $likes = $likes.",".$element;
        }
        return $likes;
    }*/

    //superclass for messages
    class Message
    {

        public $sender;
        public $textContent;
        public $photoContent;
        public $time;

        public function __construct($sender,$textContent,$photoContent,$time=1)
        {
            $this->sender = $sender;
            $this->textContent = $textContent;
            $this->photoContent = $photoContent;
            $this->time = $time;
        }
    }

    //class for normal messages
    class NormalMessage extends Message
    {
        public $reciever;
        public $isSeen;

        public function __construct($sender,$reciever,$textContent,$photoContent,$time=1,$isSeen='0')
        {
            parent::__construct($sender,$textContent,$photoContent,$time);
            $this->reciever = $reciever;
            $this->isSeen = $isSeen;
        }

        public function save(){
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in saving NormalMessage for sender ".$sender."</p>";
                 exit();
             }

             //create new message in the database and check if inserted
            $query = "insert into normalMessage values
                       ('".$this->sender."', '".time()."', '".$this->textContent."',
                        '".$this->photoContent."', '".$this->reciever."', '".$this->isSeen."')";

            $inserted = $db->query($query);

            if(!$inserted){
                 echo  "can't insert normalMessage to the database";
            }

            $db->close();
        }


        function markAsSeen(){
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in updating normalMessage of ".$this->sender."</p>";
             }

             //update normalMessage in database
             $query = "update normalMessage
                       set isSeen='1'
                       where time='".$this->time."' and sender = '".$this->sender."'";
             $updated = $db->query($query);
             if(!$updated){
                 echo "<p>can't update normalMessage to database 0n updating normalMessage of ".$this->sender."</p>";
                 exit;
             }

             $db->close();
        }

        function view($loader){

             $time = getdate($this->time);
             $month = $time['month'];
             $mday = $time['mday'];
             $profile = new Profile($this->sender);
             if($this->sender === $loader){
                 $style = "style='margin-left:8px;'";
                 echo "
             <div class='postMessage normalMessage'  style='overflow:auto'>
                <a href='browse.php?email=$profile->email' > 
                    $profile->screenName 
                </a> 
                <span class='time' > sent @ $month, $mday </span> <br/>
                
                  ";
                  
             }
             else if($this->reciever === $loader){
                 $style = "style='float:right;'";
                echo "
             <div class='postMessage normalMessage'  style='overflow:auto;margin-left:auto;'>
                <a href='browse.php?email=$profile->email'  >
                    $profile->screenName
                </a>
                <span class='time'> recieved @ $month, $mday </span> <br/>
                  ";
             }

             if(!empty($this->photoContent))
                echo "<img src='$this->photoContent' alt='posted photo' />";
             if(empty($this->textContent))
                echo '<br/>';
             else
                echo "<p class='postText' $style> $this->textContent </p>";
             if($this->sender === $loader){
                 $string = ($this->isSeen)? "Seen" : "Not seen";
                 echo "<span class='like' style='clear:both;'> $string </span>";
             } 
             
             echo " </div> ";
            
         }


    }

    //class for post messages
    class PostMessage extends Message
    {
        public $comments;
        public $likes;

        public function __construct($sender,$textContent,$photoContent,$time=1,$comments="",$likes="")
        {
            parent::__construct($sender,$textContent,$photoContent,$time);
            $this->comments = $comments;
            $this->likes = $likes;
        }


        public function save(){
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in saving PostMessage for sender ".$sender."</p>";
                 exit();
             }

             //create new message in the database and check if inserted
            $query = "insert into postMessage values
                       ('".$this->sender."', '".time()."', '".$this->textContent."',
                        '".$this->photoContent."', '".$this->comments."', '".$this->likes."')";

            $inserted = $db->query($query);

            if(!$inserted){
                 echo  "can't insert postMessage to the database";
            }

            $db->close();
        }

        function update(){
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in updating postMessage of ".$this->sender."</p>";
             }

             //update postMessage in database
             $query = "update postMessage
                       set likes='".$this->likes."', comments='".$this->comments."'
                       where time='".$this->time."' and sender ='".$this->sender."'";
             $updated = $db->query($query);
             if(!$updated){
                 echo "<p>can't update postMessage to database 0n updating postMessage of ".$this->sender."</p>";
                 exit;
             }

             $db->close();
        }

        public function getLikers(){
             $delimiter = ",";
             $likers = explode($delimiter,$this->likes);
             if(empty($likers[0]))
                return array();
             else
                return $likers;
         }

         public function setLikers($likers){
             $likes = "";
             $lCounter = 0;
             foreach($likers as $element){
                 if($lCounter == 0)
                    $likes = $likes.$element;
                 else
                    $likes = $likes.",".$element;
                 $lCounter++;
             }
             $this->likes = $likes;
         }


        //add email to likes if it is not there ,otherwise remove it
        function changeLikeState($email){
            $likers = $this->getLikers();

             if(in_array($email,$likers)){
                 $likers = array_diff($likers,[$email]);
                 $this->setLikers($likers);
                 $this->update();
                 return false;
             }
             else{
                 array_push($likers,$email);
                 $this->setLikers($likers);
                 $this->update();
                 return true;
             }
         }

         function comment($email,$text){
             //check if it is for the first time
             if(empty($this->comments)){
                 $this->comments = $email."\t".$text;
             }
             else{
                 $this->comments = $this->comments."\v".$email."\t".$text;
             }

             $this->update();

         }

         function view($email){
             $poster = new Profile($this->sender);
             $loader = new Profile($email);
             $time = getdate($this->time);
             $month = $time['month'];
             $mday = $time['mday'];
             $count = count($this->getLikers());
             $s = ($count > 1)? "s" : "";
             echo "
             <div class='postMessage'>
                <a href='browsePage.php?email=$poster->email'> $poster->screenName </a>
                <span class='time'> posted @ $month, $mday </span> <br/>
                  ";
             if(!empty($this->photoContent))
                echo "<img src='$this->photoContent' alt='posted photo' />";
             if(empty($this->textContent))
                echo '<br/>';
             else
                echo "<p class='postText'> $this->textContent </p>";
             $string = (in_array($email,$this->getLikers()))? "Unlike" : "Like";
             echo "
                <a class='like' href='profilePage.php?like=yes&time=$this->time&sender=$this->sender'> $string </a>
                <span class='like'> $count like$s </span>
            </div> ";
         }

    }

?>