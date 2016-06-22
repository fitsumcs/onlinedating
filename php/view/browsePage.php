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
    
    if(empty($_GET['email'])){
        header("location:profilePage.php");
    }
    else{
        if(!empty($_GET['add']) && !empty($_GET['nemail'])){
            $self->changeNominee($_GET['nemail']);
        }
        
        $browsed = new Profile($_GET['email']);
        $ph->init($browsed);
    }
    
    $thirdPerson = ($browsed->gender == 'M')? "his" : "her";
?>

<?php
    $active = "browse";
    include("../includes/header.php");
    include("../includes/navigation.php");
    include("../includes/searchbar.php");
?>

<div id="section" style="width:36em;">
    <div class="postMessage" style="width:29.55em;">
        <span class="time" style="float:none;font-size:14px;">
             @<?php echo $browsed->screenName ?>'s profile
        </span>
    </div>
    <?php 
        $browsed->viewAsBrowse($browsed->email); 
        
        $moreClass = (empty($_GET['additional']))? "" : "class='active'";
        $postsClass = (empty($_GET['posts']))? "" : "class='active'";
        $basicClass = (empty($moreClass) && empty($postsClass))? "class='active'" : "";
    ?>
    
    <ul class="tabs">
        <li <?php echo $basicClass ?> title="see basic profile information">
            <a href="browsePage.php?email=<?php echo $browsed->email?>"> Basic  </a>
        </li>
        <li <?php echo $moreClass ?> title="see additional, more profile information">
            <a href="browsePage.php?email=<?php echo $browsed->email?>&additional=yes"> More </a>
        </li>
        <li <?php echo $postsClass; echo "title='see $thirdPerson posts'" ?>>
            <a href="browsePage.php?email=<?php echo $browsed->email?>&posts=yes"> Posts </a>
        </li>
    </ul>
<?php
    if(!empty($_GET['additional'])){
        $browsed->profileInfo->viewAsTable();
    }
    else if(!empty($_GET['posts'])){
        if(isset($_GET['more'])){
            $posts = $mh->getHerPosts($browsed->email,true,$_GET['index']);
        } 
        else{
            $posts = $mh->getHerPosts($browsed->email);
        }
        if(empty($posts)){
            echo'
                <div class="postMessage" style="text-align:center;color:#333;font-size:1.2em;">
                    <p class="postText" > 
                        You have seen all '.$thirdPerson.' posts!
                    </p>
                </div>  '; 
        }
        else{
            foreach($posts as $post){
                $post->view($self->email);
            }
            echo"
            <a class='more' href='browsePage.php?email=$browsed->email&posts=yes&more=yes&index=$mh->currentPIndex'>
                See More
            </a>
            ";  
        }
    }
    else{
        $browsed->viewAsTable();
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
       else if (!empty($_GET['posts'])){
            if(!empty($_GET['more']) && !empty($_GET['index'])){
                 $param .= "posts=yes&more=yes&index=$mh->currentPIndex";
            }
            else{
                $param .= "";
            }
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
               $profile->viewAsSuggestion("",$param,"","","","","");
        }
      }
    ?>
</div>

<!--<div id="footer">
Copyright  &copy; 2015 - <?php echo date("Y"); ?> mike cooperation
</div>-->

</body>

</html>
