<!doctype html>
<html>
    <head>
        <title> Online Dating System </title>
        <meta charset="utf-8" />
        <meta name="keywords" content="dating" /> <!-- keywords not finished -->
        <meta name="description" content="Online Dating System" /> <!-- description not finished -->
        <meta name="viewport" content="width=device-width,initial-scale=1" />
        
        <link rel="stylesheet" type="text/css" href="../../css/headers_2.css">
        <link rel="stylesheet" type="text/css" href="../../css/post.css">
        <link rel="stylesheet" type="text/css" href="../../css/profile.css">
        <link rel="stylesheet" type="text/css" href="../../css/message.css">
        <link rel="stylesheet" type="text/css" href="../../css/browse.css">

        <script src="../../js/updateOnline.js"></script>

<?php 
    if($active === "edit" || $active === "advancedSearch"){
        echo "<link rel='stylesheet' type='text/css' href='../../css/edit.css'>";
    }
    
?>
    </head>
    
<body>
    
    <div id="header">
        <img src="../../images/love-icon.jpg" alt="logo" style = "float : left;"/>
        <h1 style = "margin-left:190px;">Ketero Dating Website </h1>
    </div>