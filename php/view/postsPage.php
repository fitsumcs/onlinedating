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
                $post = new PostMessage($self->email,$postText,$postPhoto);
                $mh->post($post);
            }
            else if(!empty($postPhoto)){
                $post = new PostMessage($self->email,$postText,$postPhoto);
                $mh->post($post);
            }
        }
                    
    }
    
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

    <div class="postForm"> 
      
      <form action="postsPage.php" method="POST" enctype="multipart/form-data">
        <textarea name="postText"  rows="2">
        </textarea><br/>
        <input type="file" name="postPhoto"/>
        <input type="submit" value="Post" style="margin-left:2.6em;"/>
      </form>
      <?php
        if(isset($notUploaded)){
           echo'
            <div class="postMessage" 
            style="text-align:center;color:#333;line-height:1.7em;background-color:#fdd;color:#f00;width:97%;">
                <p class="postText" > 
                    File uploading failed!<br/>'.$perror.'
                </p>
            </div>  '; 
        }
      ?>
    </div>
<?php
    if(isset($_GET['more'])){
        $posts = $mh->getHerPosts($self->email,true,$_GET['index']);
        $notification = "You have seen all your posts.";
    } 
    else{
        $posts = $mh->getHerPosts($self->email);
        $notification = "You haven't posted anything yet";
    }
    if(empty($posts) && !empty($notification)){
        echo'
            <div class="postMessage" style="text-align:center;color:#333;font-size:1.2em;">
                <p class="postText" > 
                    '.$notification.'
                </p>
            </div>  '; 
    }
    else{
        foreach($posts as $post){
            $post->view($self->email);
        }
        echo"
        <a class='more' href='postsPage.php?more=yes&index=$mh->currentPIndex'>
            See More
        </a>
        ";  
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
