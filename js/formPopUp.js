function displayForm(formId){
    var form = document.getElementById(formId);
    form.style.display = "block";
    //some implementation remains
    
}

function quitForm(){
    document.getElementById("login-form").style.display = "none";
    document.getElementById("signup-form").style.display = "none";
    var main = document.getElementsByTagName("body")[0];
    main.style.backgroundColor = "#f4f4f4";
}