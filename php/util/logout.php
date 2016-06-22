<?php
    
    
    session_start();
    
    include_once("../model/Profile.php");

    //mark user offline for other users
    if(!empty($_SESSION['validUser'])){
        $self = new Profile($_SESSION['validUser']);
        $self->updateActivity(time()-50);
    }
    unset($_SESSION['validUser']);
    
    session_destroy();
    
    header("location:../../index.html");
?>