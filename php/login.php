<?php
    
    session_start();
    
    if(isset($_POST['email']) && isset($_POST['password']))
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $db = new mysqli('localhost','root','7993','datingsystem');
        
        if(mysqli_connect_errno())
        {
            //echo "Connection to database is failed.".mysqli_connect_error();
            $dbError = "Connection failed. Please, try again";
            //exit;
        }
        
        $query = "select * from profile
                  where email= '".$email."' and password = '".sha1($password)."'";
                 
        $result = $db->query($query);
        if($result->num_rows)
        {
            $_SESSION['validUser'] = $email;
            header("location:view/profilePage.php");
        }
        else 
        {
             $profileError = "The email or the password is incorrect.";
        }
        
        $db->close();
        
        
    }
    else{
        header("location:../index.html");
    }
    
    //feedback when error occurs
?>

<!doctype html>
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
            width: 24em;
            margin: 5px auto;
            padding: 8px 18px;
            text-align: center;
            color: #f00;   
        }
        @media (max-width:760px){
            .error{
                width: 18em;
            }
        }
    </style>
        <!-- head not finished -->
    
    </head>
    
<body>

<div id="login-form" style="display:block">    
    <form action="login.php" method="post" id="login">
        <h1>Log In</h1>
        <p class="error">
        <?php 
             if(isset($dbError)){
                 echo $dbError;
             }
             else if(isset($profileError)){
                 echo $profileError;
             }
        ?>
        </p>
        
        <ul>
            <li>
                <!--<label>Email</label>-->
                    <input type="email" name="email" id="email" placeholder="Your email address" required/><br/>
            </li>
            <li>    
                <!--<label>Password</label>-->
                    <input type="password" name="password" id="password" placeholder="Your password" required/><br/>
            </li>
            <li>
                <a href="php/forgot.php"> Forgot your Password? </a>
            </li>
            <li>
                <input type="submit" class="button" value="Log In" />
            </li>
        </ul>
        <!--<a class="x" onclick="quitForm()"> x </a>-->
    </form>
</div>

</body>

</html>




