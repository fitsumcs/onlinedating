<?php

include("../model/Message.php");
include("../model/Profile.php");
include("../controller/MessageHandler.php");

$p = new Profile("yad.tad.yt@gmail.com");

//$p->save();
//var_dump($p);echo "<br><br>";
//$p->isSeen = true;
/*
$p->save();)
var_dump($p);


$a = new MessageHandler();
$p = new Profile("melesewtemesgen@gmail.com");
//var_dump($p);
//var_dump($p->changeNominee("yad.tad.yt@gmail.com"));
//var_dump($p);
var_dump($x=$a->getUnseenMessages("melesewtemesgen@gmail.com"));
$x[2]->markAsSeen();
var_dump($x=$a->getUnseenMessages("melesewtemesgen@gmail.com"));
//var_dump($a->getNormalMessages("melesewtemesgen@gmail.com","yad.tad.yt@gmail.com",true));
*/

?>

<?php
    var_dump(intval(date('Y'))-intval(date('Y',strtotime($p->birthday))));
?>

