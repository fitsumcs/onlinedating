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
    
    
    
    if(!empty($_GET['like'])){
        $post = $mh->loadPostMessage($_GET['time'],$_GET['sender']);
        $post->changeLikeState($self->email);
        
    }

    if(!empty($_GET['add']) && !empty($_GET['nemail'])){
            $self->changeNominee($_GET['nemail']);
        }
?>

<?php
    $active = "profile";
    include("../includes/header.php");
    include("../includes/navigation.php");
    include("../includes/searchbar.php");
?>

<div id="section" style="width:36em;">

   <p class="little" style="text-align:center;color:#333;font-size:1em;height:auto;">
        nominees list
    </p>
    
    <?php
       if(!empty($_GET['more']) && !empty($_GET['index'])){
            $param = "more=yes&index=$mh->currentPIndex";
       }
       else{
           $param = "";
       }
       $nemails = $self->profileInfo->getNominees();
       $profiles = array();
       foreach($nemails as $nemail){
           $nprofile = new Profile($nemail);
           array_push($profiles,$nprofile);
       }
       if(empty($profiles)){
        echo " <p class='little' style='text-align:center;color:#333;font-size:1em;height:auto;padding-top:3em;padding-bottom:3em;'>
            You have no nominees in your nominees list.</br>
            Please, add people you are interested in to your nominees lists.
         </p> ";
       }
       else{
        foreach($profiles as $profile){
               $ph->init($profile);
               $profile->viewAsSuggestion("","","","",$param,"","");
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
        suggestion list
    </p>
    
    <?php
       if(!empty($_GET['more']) && !empty($_GET['index'])){
            $param = "more=yes&index=$mh->currentPIndex";
       }
       else{
           $param = "";
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
               $profile->viewAsSuggestion("","","","",$param,"","");
        }
      }
    ?>
</div>

<!--<div id="footer">
Copyright  &copy; 2015 - <?php echo date("Y"); ?> mike cooperation
</div>-->

</body>

</html>
