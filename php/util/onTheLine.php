<?php
    session_start();
    
    if(!isset($_SESSION['validUser'])){
        header("location:../../index_2.html");
    }

    require_once('../controller/ProfileHandler.php');

    $ph = new ProfileHandler($_SESSION['validUser']);
    $self = $ph->self;

    $self->updateActivity(time());

    echo time();
?>