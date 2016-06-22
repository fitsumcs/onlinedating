<?php

    session_start();
    //$currentUser = $_SESSION['validUser']; 
    if(!isset($_SESSION['validUser'])){
        header("location:../../index_2.html");       //redirect to home page if user loads the page with out any session
    }
    
    require_once('../controller/ProfileHandler.php');
    require_once('../controller/MessageHandler.php');

    
    $ph = new ProfileHandler($_SESSION['validUser']);
    $self = $ph->self;
    $self->updateActivity(time());
    $mh = new MessageHandler();  
    
    if(empty($_REQUEST['email'])){
        $unseenMessages = $mh->getUnseenMessages($self->email);
        
    }
    else{
        if(!empty($_GET['add']) && !empty($_GET['nemail'])){
            $self->changeNominee($_GET['nemail']);
        }
        
        $interactor = new Profile($_REQUEST['email']);
        $ph->init($interactor);
    }
    
    if(!empty($_POST['postText']) || !empty($_POST['postPhoto'])){
        $postPhoto = "" ;
        $postText = addslashes(trim($_POST['postText']));
        //photo upload mechanism with all necessary checks
        $photoName = $_FILES['postPhoto']['name'];
        if(!empty($photoName)){
            $uploadOk = 1;
            $imageType = pathinfo($photoName,PATHINFO_EXTENSION);
            $photoName = md5($photoName.time().rand(1,1000));
            $destination = "../../images/".$photoName.".".$imageType;
            //check if image is actual image or fake
            $check = getimagesize($_FILES['postPhoto']['tmp_name']);
            if($check === false){
                $fakeImage = 1;$uploadOk = 0;
                $perror = "The file is not supported";
            }
            //check file size 
            if($_FILES['postPhoto']['size'] > 10000000){
                $hugeImage = 1;$uploadOk = 0;
                $perror = "The file size must be at most 10 MB";
            }
            //allow certain formats
            if($imageType != "jpg" && $imageType != "png" && 
               $imageType != "jpeg" && $imageType != "gif"){
                   $invalidFormat = 1;$uploadOk = 0;
                   $perror = "The valid file extension are jpg,jpeg,png and gif";
            }
            //upload if image is validated
            if($uploadOk == 1){
                if(!move_uploaded_file($_FILES['postPhoto']['tmp_name'],$destination)){
                    $notUploaded = 1;
                }
                if(!isset($notUploaded))
                    $postPhoto = $destination;
            }
            else{
                $notUploaded = 1;
            }
        }
        if(!empty($postText) || !empty($photoName)){
            if(empty($photoName)){
                $message = new NormalMessage($self->email,$interactor->email,$postText,$postPhoto);
                $mh->send($message);  
            }
            else if(!empty($postPhoto)){
                $message = new NormalMessage($self->email,$interactor->email,$postText,$postPhoto);
                $mh->send($message);  
            }
        }
              
            
    }
    
    
?>

<?php
    $active = "message";
    include("../includes/header.php");
    include("../includes/navigation.php");
    include("../includes/searchbar.php");
?>
    

<div id="section" style="width:36em;">
    <?php
        if(empty($_REQUEST['email'])){
            if(empty($unseenMessages)){
                echo'
                <div class="postMessage" style="text-align:center;color:#333;font-size:1.2em;width:32em;">
                    <p class="postText" > 
                        No new Messages
                    </p>
                </div>  ';
            }
            else{
                foreach($unseenMessages as $message){
                    $profile = new Profile($message->sender);
                    $ph->init($profile);
                    $profile->viewAsUnseen();
                    $message->view($self->email);
                    $message->markAsSeen();
                }
            }
        }
        else{
            echo "<div class='postMessage' style='width:29.55em;'>
                <span class='time' style='float:none;font-size:14px;'>
                    message @ $interactor->screenName  
                </span>
            </div>
            <div class='postForm'> 
            <form action='messagePage.php' method='POST' enctype='multipart/form-data'>
                <textarea name='postText'  rows='2'>
                </textarea><br/>
                <input type='file' name='postPhoto'/>
                <input type='submit' value='Send' style='margin-left:2.6em;'/>
                <input type='hidden' value='$interactor->email' name='email'/>
            </form>
            </div>";
                if(isset($notUploaded)){
                echo'
                    <div class="postMessage" 
                    style="text-align:center;color:#333;line-height:1.7em;background-color:#fdd;color:#f00;width:97%;">
                        <p class="postText" > 
                            File uploading failed!<br/>'.$perror.'
                        </p>
                    </div>  '; 
                }
        }
            ?>
        
    
<?php
if(!empty($_REQUEST['email'])){
    if(isset($_GET['more'])){
        $messages = $mh->getNormalMessages($self->email,$interactor->email,true,$_GET['index']);
    } 
    else{
        $messages = $mh->getNormalMessages($self->email,$interactor->email);
    }
    if(empty($messages)){
        if(empty($_GET['more'])){
            $notification = "No messages have been sent or recieved.";
        }
        else{
            $notification = "You have seen all messages.";
        }
        echo'
            <div class="postMessage" style="text-align:center;color:#333;font-size:1.2em;">
                <p class="postText" > 
                    '.$notification.'
                </p>
            </div>  '; 
    }
    else{
        foreach($messages as $message){
            $message->view($self->email);
            if(!$message->isSeen && ($self->email===$message->reciever)){
                $message->markAsSeen();
            }
        }
        echo"
        <a class='more' href='messagePage.php?email=$interactor->email&more=yes&index=$mh->currentNIndex'>
            See Previous Messages
        </a>
        ";  
    }
}   
?>    
             
 </div>

<?php
     include ("../includes/aside.php"); 
?>

<div style=
    "width:20em;background-color:#f0f0f0;float:right;border-radius:3px;
     position:relative;top:1.8em;right:6em;border:1px #cdcdcd solid;">
    <p class="little" style="text-align:center;color:#333;font-size:1em;height:auto;">
        chat list
    </p>
    
    <?php
    if(!empty($interactor))
       $interactor->viewAsChat($interactor->email);
    

    if(!empty($_GET['more']) && !empty($_GET['index'])){
            $param = "more=yes&index=$mh->currentPIndex";
       }
       else{
           $param = "";
       }
       $profiles = $ph->getSuggestedProfiles();
       if(empty($profiles)){
            $nemails = $self->profileInfo->getNominees();
            $profiles = array();
            foreach($nemails as $nemail){
                $nprofile = new Profile($nemail);
                array_push($profiles,$nprofile);
            }
            if(empty($profiles)){
                ;
            }
            else{
                foreach($profiles as $profile){
                    $ph->init($profile);
                    $profile->viewAsSuggestion("","","",$param,"","","");
                }
            }
       }
       else{
        foreach($profiles as $profile){
               $ph->init($profile);
               if(!empty($interactor))
                    $profile->viewAsChat($interactor->email);
               else
                    $profile->viewAsChat("");
        }
      }
      ?>
</div>

<!--<div id="footer">
Copyright  &copy; 2015 - <?php echo date("Y"); ?> mike cooperation
</div>-->

</body>

</html>
