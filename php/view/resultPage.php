<?php

    session_start();
    //$currentUser = $_SESSION['validUser']; 
    if(!isset($_SESSION['validUser'])){
        header("location:../index.html");       //redirect to home page if user loads the page with out any session
    }
    
    require_once('../controller/ProfileHandler.php');
    require_once('../controller/MessageHandler.php');

    
    $ph = new ProfileHandler($_SESSION['validUser']);
    $self = $ph->self;
    $self->updateActivity(time());
    $mh = new MessageHandler();  
    
    if(!empty($_GET['add']) && !empty($_GET['email'])){
        $self->changeNominee($_GET['email']);
    }
    if(!empty($_GET['index'])){
        $ph->currentSIndex = $_GET['index'];
    }

    if(!empty($_GET['add']) && !empty($_GET['nemail'])){
            $self->changeNominee($_GET['nemail']);
        }
?>

<?php
    $active = "result";
    include("../includes/header.php");
    include("../includes/navigation.php");
    include("../includes/searchbar.php");
?>
    

<div id="section" style="width:36em;">
  
<?php
    $term = (empty($_GET['searchTerm']))? "" : addslashes(trim($_GET['searchTerm']));
    if(empty($term) && empty($_GET['advanced'])){
        header("location:profilePage.php");
    }
    else if (!empty($term)){
        $profiles = $ph->simpleSearch($term,$ph->currentSIndex);
        if(empty($profiles)){
           echo'
            <div class="postMessage" style="text-align:center;color:#333;font-size:1.2em;">
                <p class="postText" > 
                    No match found!
                </p>
            </div>  ';  
        }
        else{
            foreach($profiles as $profile){
                $ph->init($profile);
            }
            foreach($profiles as $profile){
                $profile->viewAsResult($_GET['searchTerm']);
            }
            $sterm = $_GET['searchTerm'];
            echo"
                <a class='more' 
                    href='resultPage.php?index=$ph->currentSIndex&searchTerm=$sterm'>
                        See More
                </a>
            "; 
        }
    }
    else if (!empty($_GET['advanced'])){
        $url = "advanced=yes";
        if(!empty($_GET['initialAge'])){
            $url .= "initialAge&".urlencode($_GET['initialAge']);
        }
        if(!empty($_GET['finalAge'])){
            $url .= "finalAge&".urlencode($_GET['finalAge']);
        }
        if(!empty($_GET['religion'])){
            $url .= "religion&".urlencode($_GET['religion']);
        }
        if(!empty($_GET['location'])){
            $url .= "location&".urlencode($_GET['location']);
        }
        if(!empty($_GET['mStatus'])){
            $url .= "mStatus&".urlencode($_GET['mStatus']);
        }
        if(!empty($_GET['initialHeight'])){
            $url .= "initialHeight&".urlencode($_GET['initialHeight']);
        }
        if(!empty($_GET['finalHeight'])){
            $url .= "finalHeight&".urlencode($_GET['finalHeight']);
        }
        if(!empty($_GET['build'])){
            $url .= "build&".urlencode($_GET['build']);
        }
        if(!empty($_GET['education'])){
            $url .= "education&".urlencode($_GET['education']);
        }
        if(!empty($_GET['occupation'])){
            $url .= "occupation&".urlencode($_GET['occupation']);
        }
        if(!empty($_GET['drinking'])){
            $url .= "drinking&".urlencode($_GET['drinking']);
        }
        if(!empty($_GET['smoking'])){
            $url .= "smoking&".urlencode($_GET['smoking']);
        }
        if(!empty($_GET['haveChildren'])){
            $url .= "haveChildren&".urlencode($_GET['haveChildren']);
        }
        $profiles = $ph->advancedSearch($ph->currentSIndex);
        if(empty($profiles)){
           echo'
            <div class="postMessage" style="text-align:center;color:#333;font-size:1.2em;">
                <p class="postText" > 
                    No match found!
                </p>
            </div>  ';  
        }
        else{
            foreach($profiles as $profile){
                $ph->init($profile);
            }
            foreach($profiles as $profile){
                $profile->viewAsResult("",$url);
            }
            
            echo"
                <a class='more' 
                    href='resultPage.php?index=$ph->currentSIndex&$url'>
                        See More
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
        suggestion list
    </p>
    
    <?php
       $param = "index=$ph->currentSIndex";
       if(!empty($_GET['advanced'])){
           $param .= "&".$url;
       }
       else if(!empty($term)){
           $param .= "&".$term;
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
               $profile->viewAsSuggestion("","","","","",$param,"");
        }
      }
    ?>

</div>

<!--<div id="footer">
Copyright  &copy; 2015 - <?php echo date("Y"); ?> mike cooperation
</div>-->

</body>

</html>
