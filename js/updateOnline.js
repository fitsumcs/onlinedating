function onTheLine(){
    var xmlhttp;
    if(window.XMLHttpRequest){
        xmlhttp = new XMLHttpRequest();
    }
    else{
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    
    //xmlhttp.onreadystatechange = function(){alert(xmlhttp.status + "with" + xmlhttp.readyState + "with" + xmlhttp.responseText)}
    xmlhttp.open("GET","../util/onTheLine.php",true);
    xmlhttp.send(null);
    
    
    
}

setInterval(onTheLine,5000);
