<?php

    session_start();
    //$currentUser = $_SESSION['validUser']; 
    if(!isset($_SESSION['validUser'])){
        header("location:../../index.html");       //redirect to home page if user loads the page with out any session
    }
    
    require_once('../controller/ProfileHandler.php');
    require_once('../controller/MessageHandler.php');

    
    $ph = new ProfileHandler($_SESSION['validUser']);
    $self = $ph->self;
    $self->updateActivity(time());
    $mh = new MessageHandler();  
    
    if(!empty($_GET['add']) && !empty($_GET['nemail'])){
            $self->changeNominee($_GET['nemail']);
        }

    $screenNameError='';$nameError='';$profilePhotoError='';
    $isUpdated = "no";
    if(isset($_POST['basicUpdate'])){
        
        if(empty($_POST['screenName'])){
            $screenNameError = "Screen name can not be empty";
        }
        else if(strlen($_POST['screenName']) < 4){
            $screenNameError = "Scree name must be at least 4 characters";
        }
        if(empty($_POST['name'])){
            $nameError = "Name can not be empty";
        }
        else if(mb_ereg(".*[[:digit:]]+.*",$_POST['name'])){
            $nameError = "Name shouldn't contain numbers";
        }
        else if(mb_ereg(".*[[:punct:]]+.*",$_POST['name'])){
            $nameError = "Name shouldn't contain punctuation characters";
        }
        else if(strlen($_POST['name']) < 3){
            $nameError = "Name must be at least 3 characters";
        }
        
        //about profile photo
        if(!empty($_POST['postPhoto'])){
        $postPhoto = "" ;
        $notUploaded = 0;
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
                $perror = "The file size can be at most 10 MB";
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
        if(!empty($photoName)){
            if($notUploaded === 1){
                $profilePhotoError = (empty($perror))? "Profile picture upload failed" : $perror ;
            }
        }   
        }
        
        if(empty($screenNameError) && empty($nameError)){
            if(empty($photoName) || empty($profilePhotoError)){
            $self->screenName = addslashes(trim($_POST['screenName']));
            $name = trim($_POST['name']);
            $arr = explode(" ",$name);
            if(count($arr) == 2){
                $firstName = $arr[0];
                $lastName = $arr[1];
            }
            else{
                $firstName = $name;
                $lastName = "";
            }
            $self->firstName = $firstName;
            $self->lastName = $lastName;
            if(!empty(trim($_POST['profileHeadline'])))
                $self->profileHeadline = addslashes(trim($_POST['profileHeadline']));
            $self->location = addslashes(trim($_POST['location']));
            $self->password = addslashes(trim($_POST['password']));
            
            $isUpdated = $self->update();
            }
        }
        else{
            $isUpdated = false;
        }
    }
?>

<?php
    $active = "edit";
    include("../includes/header.php");
    include("../includes/navigation.php");
    include("../includes/searchbar.php");
?>

<div id="section" style="width:36em;">
<?php 
    if( $isUpdated !== "no" ){
        if($isUpdated === true)
          echo '<div class="postMessage" style="width:29.55em;">
        <span class="time" style="float:none;font-size:14px;">
             your profile is successfully updated
        </span>
                </div>';
        else 
            echo '<div class="postMessage" style="width:29.55em;">
        <span class="time" style="float:none;font-size:14px;">
             your profile was not updated.Please, try again
        </span>
                </div>';
        
    }  
  else {
    echo'
    <div class="postMessage" style="width:29.55em;">
        <span class="time" style="float:none;font-size:14px;">
             your profile status
        </span>
    </div>';
    }
    ?>
    
    <?php 
         
        $moreClass = (empty($_GET['additional']))? "" : "class='active'";
        $criteriaClass = (empty($_GET['criteria']))? "" : "class='active'";
        $basicClass = (empty($moreClass) && empty($criteriaClass))? "class='active'" : "";
    ?>
    
    <ul class="tabs">
        <li <?php echo $basicClass ?> title="Edit basic profile information">
            <a href="editPage.php"> Basic  </a>
        </li>
        <li <?php echo $moreClass ?> title="Edit additional, more profile information">
            <a href="editPage.php?additional=yes"> More </a>
        </li>
        <li <?php echo $criteriaClass ?> title="Edit your criteria about which kind of peoples you are interested in">
            <a href="editPage.php?criteria=yes"> Criteria </a>
        </li>
    </ul>
    
<?php   
       
    if(!empty($_GET['additional'])){
         $self->profileInfo->viewAsEditable();
    }
    else if(!empty($_GET['criteria'])){
        $self->criteria->viewAsEditable();
    }
    else{
        $self->viewAsEditable($screenNameError,$nameError,$profilePhotoError);
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
        suggestion list
    </p>
    
    <?php
       $param = "";
       if(!empty($_GET['additional'])){
           $param .= "additional=yes";
       }
       else if(!empty($_GET['criteria'])){
           $param .= "criteria=yes";
       }
       
       
       
       $profiles = $ph->getSuggestedProfiles();
       if(empty($profiles)){
        echo " <p class='little' style='text-align:center;color:#333;font-size:1em;height:auto;'>
            no suggestions for now
         </p> ";
       }
       else{
        foreach($profiles as $profile){
               $ph->init($profile);
               $profile->viewAsSuggestion("","",$param,"","","","");
        }
      }
    ?>

</div>

<!--<div id="footer">
Copyright  &copy; 2015 - <?php echo date("Y"); ?> mike cooperation
</div>-->

</body>

</html>
