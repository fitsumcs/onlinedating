<?php
    
        //create short names for form variables
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $gender = $_POST['gender'];
        $screenName = trim($_POST['screenName']);
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
        $birthday = trim($_POST['birthday']);
        $location = $_POST['location'];
        $profileHeadline = trim($_POST['profileHeadline']);
        $profilePhoto = null;

        
        $birthyear = intval(date('Y',strtotime($birthday)));
         $age = intval(date('Y')) - $birthyear;

         if($age < 18){
             $ageError = 1;
         }
        //check all the necessary inputs are given
        if(!$email || !$password || !$screenName || !$firstName || !$gender ||
           !$birthday || !$location || !$profileHeadline)
           {
              $emptyInput = 1;
              //echo "You have not entered all the necessary details. Please go back and try again"; 
           }

         else if(!empty($ageError)){
             ;//empty statement
         }
         else{
         
         
         //add escape charcters if it is necessary using addslashes method
         if(!get_magic_quotes_gpc())
         {
             $email = addslashes($email);
             $password = addslashes($password);
             $gender = addslashes($gender);
             $screenName = addslashes($screenName);
             $firstName = addslashes($firstName);
             $lastName = addslashes($lastName);
             $location = addslashes($location);
             $profileHeadline = addslashes($profileHeadline);
             $profilePhoto = addslashes($profilePhoto);
         }
         
         //connect to database and validate
         $db = new mysqli('localhost','root','7993','datingSystem');
         
         if(mysqli_connect_errno())
         {
             $noConnection = 1;//echo "Couldn't connect to database. Please try again";
         }
         else{
         
         //check if given email already exists
         $query = "select * from profile
                  where email= '".$email."'";
         $result = $db->query($query);
         if($result->num_rows){
             $emailExists = 1;//echo "a user with the given email already exists";
         }
         else{
         
         //photo upload mechanism with all necessary checks
        $photoName = $_FILES['profilePhoto']['name'];
        if(!empty($photoName)){
            $uploadOk = 1;
            $imageType = pathinfo($photoName,PATHINFO_EXTENSION);
            $photoName = md5($photoName.time().rand(1,1000));
            $destination = "../images/".$photoName.".".$imageType;
            //check if image is actual image or fake
            $check = getimagesize($_FILES['profilePhoto']['tmp_name']);
            if($check === false){
                $fakeImage = 1;$uploadOk = 0;
            }
            //check file size 
            if($_FILES['profilePhoto']['size'] > 10000000){
                $hugeImage = 1;$uploadOk = 0;
            }
            //allow certain formats
            $imageType = strtolower($imageType);
            if($imageType != "jpg" && $imageType != "png" && 
               $imageType != "jpeg" && $imageType != "gif"){
                   $invalidFormat = 1;$uploadOk = 0;
            }
            //upload if image is validated
            if($uploadOk == 1){
                if(!move_uploaded_file($_FILES['profilePhoto']['tmp_name'],$destination)){
                    $notUploaded = 1;
                }
                if(!isset($notUploaded))
                    $profilePhoto = "../".$destination;
            }
            
            
        }
            
        if(empty($photoName) || $uploadOk == 0 || $notUploaded == 1){
            $photoGender = ($gender == "F") ? "female" : "male"; 
            $profilePhoto = "../../images/".$photoGender."Anonymous.PNG";
        }  
            
        if(empty($lastName)){
            $lastName = null;
        }
         $lastActive = 0;       //when was the user last active
         //create new profile in the database and check if inserted
         $query = "insert into profile values
                   ('".$email."', '".sha1($password)."', '".$gender."', '".$screenName."', '".$firstName."', 
                   '".$lastName."', '".$birthday."', '".$location."', '".$profileHeadline."', '".$profilePhoto."', '".$lastActive."')";
                    
         $inserted = $db->query($query);
         
         if(!$inserted){
             $notInserted = 1;//echo  "can't insert profile to the database";
         }
         else{
         //create new profileInfo for the new profile automatically
         $n = null;
         $query = "insert into profileInformation values
                   ('".$email."', '".$n."', '".$n."', '".$n."', '".$n."', '".$n."',
                    '".$n."', '".$n."', '".$n."', '".$n."', '".$n."', '".$n."')";
         
         $infoInserted = $db->query($query);
         if(!$infoInserted){
             echo "can't insert profileInfo to the database";
         }
         
         $n = null;
         $query = "insert into criteria values
                   ('".$email."', '".$n."', '".$n."', '".$n."', '".$n."', '".$n."',
                    '".$n."', '".$n."', '".$n."', '".$n."', '".$n."', '".$n."')";
         //create new criteria for the new profile automatically
         $criteriaInserted = $db->query($query);
         if(!$criteriaInserted){
             echo "can't insert criteria to the database";
         }
         
         $db->close();
         
         }
         }
         }
         }


if(isset($noConnection) || isset($notInserted)){
    ;// empty statement    
}
else if(isset($emptyInput) || isset($emailExists)){
    ;//empty statement
}
else if(isset($ageError)){
    ;
}    
else{
     if(isset($notUploaded)){
         ?>
         <script>
            alert("There was an error uploading your profile photo. \
                 \n Please, try to upload it again in your profile .");
         </script>
         <?php
     }
     echo '<form action="login.php" method="post" name="tempForm">';
     echo     '<input type="hidden" name="email" value="'.$email.'" />';
     echo     '<input type="hidden" name="password" value="'.$password.'" />';
     echo '</form>';
     ?>
     <script>
         document.tempForm.submit();
     </script>
     <?php  
}        
?>

<html>
    <head>
        <title> Online Dating System </title>
        <meta charset="utf-8" />
        <meta name="keywords" content="dating" /> <!-- keywords not finished -->
        <meta name="description" content="Online Dating System" /> <!-- description not finished -->
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        
        <link rel="stylesheet" href="../css/welcome.css" />
        
        
    <style>
        .error {
            border:1px #ddd solid;
            border-radius: 4px;
            box-shadow: 2px 2px 3px rgba(0,0,0,.5);
            background-color: #fdd;
            width: 44em;
            margin: 5em auto;
            padding: 3em 18px;
            text-align: center;
            color: #f00;
            line-height: 2em;   
        }
        @media (max-width:760px){
            .error{
                width: 18em;
            }
        }
    </style>
        <!-- head not finished -->
    
    </head>

    <div class="error">
    <?php
        if(!empty($noConnection) || !empty($notInserted)){
            echo "There was database connection error.</br> Please, click back and try again";
        } 
        else if(!empty($emailExists)){
            echo "The given email already exists in the system.</br> Please, click back and change to an appropriate email";
        }
        else if(!empty($emptyInput)){
            echo "You can't leave any field empty except for the profile picture field.</br> Please, click back and fill all the required fields";
        }
        else if(!empty($ageError)){
            echo "You must be at least 18 years old to sign up.</br> Sorry ";
        }
        else{
            echo "There was database connection error.</br> Please, click back and try again";
        }
    ?>
    </div>    
    
<body>

