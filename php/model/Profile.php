<?php
    
    require_once("../util/connection.php");
    require_once("ProfileInfo.php");
    require_once("Criteria.php");

    class Profile
    {
        public $gender;
        public $screenName;
        public $firstName;
		public $lastName;
		public $location;
		public $birthday;
		public $profileHeadline;
		public $profilePhoto;
		public $email;
        public $password; 
        public $lastActive;
		public $isNominee;
		public $isOnline;
        
        public $profileInfo;
        public $criteria;
        
        /* can't overload constructor
        public function __construct($email, $password, $screenName, $firstName, $lastName, 
                           $birthday, $location, $profileHeadline, $profilePhoto)
        {
                               $this->email = $email;
                               $this->password = $password;
                               $this->screenName = $screenName;
                               $this->firstName = $firstName;
                               $this->lastName = $lastName;
                               $$this->birthday = $birthday;
                               $this->location = $location;
                               $this->profileHeadline = $profileHeadline;
                               $this->profilePhoto = $profilePhoto;
         }
         */
         
         public function __construct($email)// $password need some work
         {
             //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in constructing profile for ".$email."</p>";
                 exit();
             }
             
             //check if the profile exists with given email and password in database
             $query = "select * from profile
                       where email='".$email."'";
             $result = $db->query($query);
             if(!$result->num_rows){
                 echo "<p>no profile in database in constructing profile for ".$email."</p>";
                 exit;
             }
             
             //initialize class from database if validated
             $row = $result->fetch_assoc();
             
             $this->email = $email;
             $this->password = stripslashes($row['password']);       //hashed password
             $this->gender = stripslashes($row['gender']);
             $this->screenName = stripslashes($row['screenName']);
             $this->firstName = stripslashes($row['firstName']);
             $this->lastName = stripslashes($row['lastName']);
             $this->birthday = stripslashes($row['birthday']);        //don't know if this should be stripped or not
             $this->location = stripslashes($row['location']);
             $this->profileHeadline = stripslashes($row['profileHeadline']);
             $this->profilePhoto = stripslashes($row['profilePhoto']);
             $this->lastActive = $row['lastActive'];
             
             //check if the profile is online or not
             $refreshRate = 50;
             $delay = time() - intval($this->lastActive);
             $this->isOnline = ($delay > $refreshRate)? false : true;
             
             $result->free();
             $db->close();
             
             $this->profileInfo = new ProfileInfo($email);
             $this->criteria = new Criteria($email);
         }
                           
         public function update()
         {
             //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in updating profile for ".$this->email."</p>";
                 exit;
             }
             if(empty($this->password)){
                 $passwordQuery = "";
             }
             else{
                 $passwordQuery = "password='".sha1($this->password)."', ";
             }
             //update profile in database
             $query = "update profile
                       set ".$passwordQuery."screenName='".$this->screenName."', 
                           firstName='".$this->firstName."', lastName='".$this->lastName."',
                           location='".$this->location."', profileHeadline='".$this->profileHeadline."',
                           profilePhoto='".$this->profilePhoto."' 
                       where email='".$this->email."'";
             $updated = $db->query($query);
             if(!$updated){
                 echo "<p>can't update profile to database in updating profile for ".$this->email."</p>";
                 return false;
             }
             
             $db->close();
             return true;
         }
         
         public function updateActivity($time)
         {
             //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in updating profile for ".$this->email."</p>";
                 exit;
             }
             
             //update profile in database
             $query = "update profile
                       set lastActive='".$time."' 
                       where email='".$this->email."'";
             $updated = $db->query($query);
             if(!$updated){
                 echo "<p>can't update profile to database in updating activity for ".$this->email."</p>";
                 exit;
             }
             
             $db->close();
             
             $this->lastActive = $time;
         }
         
         
         //add email to nomineeList if it is not there ,otherwise remove it
         public function changeNominee($email){
             $nominees = $this->profileInfo->getNominees();
             
             if(in_array($email,$nominees)){
                 $nominees = array_diff($nominees,[$email]);
                 $this->profileInfo->setNominees($nominees);
                 $this->profileInfo->update();
                 return false;
             }
             else{
                 array_push($nominees,$email);
                 $this->profileInfo->setNominees($nominees);
                 $this->profileInfo->update();
                 return true;
             }
         }
         
         
         function viewAsResult($searchTerm,$url = ""){
             if(!empty($searchTerm)){
                $getUrl = $searchTerm;
             }
             else if(!empty($url)){
                 $getUrl = $url;
             }
             else{
                 $getUrl = 0;
             }
             $birthyear = intval(date('Y',strtotime($this->birthday)));
             $age = intval(date('Y')) - $birthyear;
             $string = ($this->isNominee)? "Remove from nominees list" : "Add to nominees list";
             echo "
                <div class='profile'>
                    <img src='$this->profilePhoto' alt='profile picture'/>
                    <ul>
                        <li style='top:1em;left:-1em;font-size:1.4em;'> $this->screenName </li>
                        <li style='top:6em;left:4em'> Location : $this->location </li>
                        <li style='top:8em;left:4em'> Age : $age</li>
                    </ul>
            
                    <a href='resultPage.php?add=yes&email=$this->email&searchTerm=$getUrl' style='top:12em;left:4em;'> $string </a>
                    <a href='messagePage.php?email=$this->email' style='top:12em;left:20em;'> Send message </a>
                </div> ";
         }
         
         function viewAsChat($interactor){
             $birthyear = intval(date('Y',strtotime($this->birthday)));
             $age = intval(date('Y')) - $birthyear;
             $refreshRate = 50;
             $delay = time() - intval($this->lastActive);
             $onTheLine = "last online<br/>";
             if($delay < $refreshRate){
                 $onTheLine = "online";
             }
             else if ($delay < 60){
                 $onTheLine .= "$delay seconds ago" ;
             }
             else if ($delay < 3600){
                 $delay = intval($delay/60);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay minute$s ago";
             }
             else if ($delay < 86400){
                 $delay = intval($delay/3600);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay hour$s ago";
             }
             else if($delay < 172800){
                 $onTheLine .= "yesterday";
             }
             else{
                 $lastOnline = getdate($this->lastActive);
                 $month = $lastOnline['month'];
                 $mday = $lastOnline['mday'];
                 $onTheLine .= "@ $month, $mday";
             }
             $string = ($this->isNominee)? "Remove from nominees list" : "Add to nominees list";
             echo "
                
                <div class='little'>
                    <a href='messagePage.php?email=$this->email'>
                    <img src='$this->profilePhoto' alt='profile picture'/>
                    </a>
                    <a href='browsePage.php?email=$this->email' style='top:0.6em;left:6em;font-size:1.2em;'> $this->screenName </a>
                    <a href='messagePage.php?nemail=$this->email&email=$interactor&add=yes' style='bottom:1.6em;left:9em;font-size:0.8em;'> $string </a>
                    <a href='messagePage.php?email=$this->email'style='bottom:0.3em;left:9em;font-size:0.8em;'> Send message </a>
                    <span class='like' style='position:relative;top:10px;right:6px;'> $onTheLine<br/> </span>
                </div> ";
         }

         function viewAsUnseen(){
             $birthyear = intval(date('Y',strtotime($this->birthday)));
             $age = intval(date('Y')) - $birthyear;
             $refreshRate = 50;
             $delay = time() - intval($this->lastActive);
             $onTheLine = "last online<br/>";
             if($delay < $refreshRate){
                 $onTheLine = "online";
             }
             else if ($delay < 60){
                 $onTheLine .= "$delay seconds ago" ;
             }
             else if ($delay < 3600){
                 $delay = intval($delay/60);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay minute$s ago";
             }
             else if ($delay < 86400){
                 $delay = intval($delay/3600);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay hour$s ago";
             }
             else if($delay < 172800){
                 $onTheLine .= "yesterday";
             }
             else{
                 $lastOnline = getdate($this->lastActive);
                 $month = $lastOnline['month'];
                 $mday = $lastOnline['mday'];
                 $onTheLine .= "@ $month, $mday";
             }
             $string = ($this->isNominee)? "Remove from nominees list" : "Add to nominees list";
             echo "
                
                <div class='little' style='width:35.5em'>
                    <a href='messagePage.php?email=$this->email'>
                    <img src='$this->profilePhoto' alt='profile picture'/>
                    </a>
                    <a href='browsePage.php?email=$this->email' style='top:0.6em;left:6em;font-size:1.2em;'> $this->screenName has sent you a message </a>
                    <a href='messagePage.php?nemail=$this->email&add=yes' style='bottom:1.6em;left:9em;font-size:0.8em;'> $string </a>
                    <a href='messagePage.php?email=$this->email'style='bottom:0.3em;left:9em;font-size:0.8em;'> Send message </a>
                    <span class='like' style='position:relative;top:10px;right:6px;'> $onTheLine<br/> </span>
                </div> ";
         }

         function viewAsSuggestion($advancedSearch,$browse,$edit,$message,$home,$result,$search){
             $page = "";
             $param = "";
             if(!empty($advancedSearch)){
                 $page = "advancedSearchPage.php";
                 $param = $advancedSearch;
             }
             else if(!empty($browse)){
                 $page = "browsePage.php";
                 $param = $browse;
             }
             else if(!empty($edit)){
                 $page = "editPage.php";
                 $param = $edit;
             }
             else if(!empty($message)){
                 $page = "messagePage.php";
                 $param = $message;
             }
             else if(!empty($home)){
                 $page = "profilePage.php";
                 $param = $home;
             }
             else if(!empty($result)){
                 $page = "resultPage.php";
                 $param = $result;
             }
             else if(!empty($search)){
                 $page = "resultPage.php";
                 $param = $search;
             }
             $birthyear = intval(date('Y',strtotime($this->birthday)));
             $age = intval(date('Y')) - $birthyear;
             $refreshRate = 50;
             $delay = time() - intval($this->lastActive);
             $onTheLine = "last online<br/>";
             if($delay < $refreshRate){
                 $onTheLine = "online";
             }
             else if ($delay < 60){
                 $onTheLine .= "$delay seconds ago" ;
             }
             else if ($delay < 3600){
                 $delay = intval($delay/60);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay minute$s ago";
             }
             else if ($delay < 86400){
                 $delay = intval($delay/3600);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay hour$s ago";
             }
             else if($delay < 172800){
                 $onTheLine .= "yesterday";
             }
             else{
                 $lastOnline = getdate($this->lastActive);
                 $month = $lastOnline['month'];
                 $mday = $lastOnline['mday'];
                 $onTheLine .= "@ $month, $mday";
             }
             $string = ($this->isNominee)? "Remove from nominees list" : "Add to nominees list";
             echo "
                
                <div class='little'>
                    <a href='messagePage.php?email=$this->email'>
                    <img src='$this->profilePhoto' alt='profile picture'/>
                    </a>
                    <a href='browsePage.php?email=$this->email' style='top:0.6em;left:6em;font-size:1.2em;'> $this->screenName </a>
                    <a href='$page?nemail=$this->email&add=yes&$param' style='bottom:1.6em;left:9em;font-size:0.8em;'> $string </a>
                    <a href='messagePage.php?email=$this->email'style='bottom:0.3em;left:9em;font-size:0.8em;'> Send message </a>
                    <span class='like' style='position:relative;top:10px;right:6px;'> $onTheLine<br/> </span>
                </div> ";
         }
        
         function viewAsBrowse($interactor){
             $birthyear = intval(date('Y',strtotime($this->birthday)));
             $age = intval(date('Y')) - $birthyear;
             $refreshRate = 50;
             $delay = time() - intval($this->lastActive);
             $onTheLine = "last online<br/>";
             if($delay < $refreshRate){
                 $onTheLine = "online";
             }
             else if ($delay < 60){
                 $onTheLine .= "$delay seconds ago" ;
             }
             else if ($delay < 3600){
                 $delay = intval($delay/60);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay minute$s ago";
             }
             else if ($delay < 86400){
                 $delay = intval($delay/3600);
                 $s = ($delay!=1)? "s" : "";
                 $onTheLine .= "$delay hour$s ago";
             }
             else if($delay < 172800){
                 $onTheLine .= "yesterday";
             }
             else{
                 $lastOnline = getdate($this->lastActive);
                 $month = $lastOnline['month'];
                 $mday = $lastOnline['mday'];
                 $onTheLine .= "@ $month, $lmday";
             }
             $string = ($this->isNominee)? "Remove from nominees list" : "Add to nominees list";
             echo "
                
                <div class='little' style='margin:0'>
                    
                    <img src='$this->profilePhoto' alt='profile picture'/>

                    <a href='browsePage.php?email=$this->email' style='top:0.6em;left:6em;font-size:1.2em;'> $this->screenName </a>
                    <a href='browsePage.php?nemail=$this->email&email=$interactor&add=yes' style='bottom:0.3em;left:9em;font-size:0.8em;'> $string </a>
                    <a href='messagePage.php?email=$this->email'style='bottom:0.3em;left:35em;font-size:0.8em;'> Send message </a>
                    <span class='like' style='position:relative;top:10px;right:6px;'> $onTheLine<br/> </span>
                </div> ";
         }
         
         function viewAsTable(){
             $birthyear = intval(date('Y',strtotime($this->birthday)));
             $age = intval(date('Y')) - $birthyear;
             echo "<table class='table'>";
             echo "<tr><td>Screen name</td><td>$this->screenName</td></tr>
                   <tr><td>Name</td><td>$this->firstName $this->lastName</td></tr>
                   <tr><td>Age</td><td>$age</td></tr>
                   <tr><td>Location</td><td>$this->location</td></tr>
                   <tr><td>Profile Headline</td><td>$this->profileHeadline</td></tr>";
             echo "</table>";       
                   
                  
         }
         
         function viewAsEditable($screenNameError,$nameError,$profilePhotoError){
             ?> 
                
    <form action="editPage.php" method="post" enctype="multipart/form-data" class="editableForm">
  		 								      	      
	      <div class="col-2">
	        <label>screen name
	        <input <?php echo "value='$this->screenName'" ?> type="text" name="screenName" tabindex="10" min="4"  required>
	        </label>
	      </div>
          <?php if(!empty($screenNameError))
            echo "<div class='col-2 editError'>
                  <label>Screen Name is not valid!
                  <br/>$screenNameError
                  </label></div>";
          ?>
          
          <div class="col-2">
	        <label>
	          Name
	          <input <?php echo "value='$this->firstName $this->lastName'" ?> type="text"  name="name" tabindex="5"  required>
	        </label>
	      </div>    	      
	      <?php if(!empty($nameError))
            echo "<div class='col-2 editError'>
                  <label>Name is not valid!
                  <br/>$nameError
                  </label></div>";
          ?>
          
          <div class="col-3">
	        <label>
	          PROFILE HEADLINE 
	          <input <?php echo "value='$this->profileHeadline'" ?> name="profileHeadline" type="text" />
	        </label>
	      </div>
	      <?php if(!empty($profileHeadlineError))
            echo "<div class='col-2 editError'>
                  <label>Profile Headline is not valid!
                  <br/>$profileHeadlineError
                  </label></div>";
          ?>     
	      
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       LOCATION
	          <select tabindex="3" name="location">
    <?php echo "<option value='$this->location' selected>$this->location</option>"; ?>
	            
                <option value = ">Abiy Addi">Abiy Addi</option>
                <option value = ">Adama">Adama</option>
                <option value = ">Addis Ababa">Addis Ababa</option>
                <option value = ">Addis Alem">Addis Alem</option>
                <option value = ">Addis Zemen">Addis Zemen</option>
                <option value = ">Adet">Adet</option>
                <option value = ">Adigrat">Adigrat</option>
                <option value = ">Adwa">Adwa</option>
                <option value = ">Agaro">Agaro</option>
                <option value = ">Akaki">Akaki</option>
                <option value = ">Alaba">Alaba</option>
                <option value = ">Alitena">Alitena</option>
                <option value = ">Amba Mariam">Amba Mariam</option>
                <option value = ">Ambo">Ambo</option>
                <option value = ">Ankober">Ankober</option>
                <option value = ">Arba Minch">Arba Minch</option>
                <option value = ">Arboye">Arboye</option>
                <option value = ">Asaita">Asaita</option>
                <option value = ">Asella">Asella</option>
                <option value = ">Asossa">Asosa</option>
                <option value = ">Awasa">Awasa</option>
                <option value = ">Awash">Awash</option>
                <option value = ">Axum">Axum</option>
                <option value = ">Alamata">Alamata</option>
                <option value = "Babilla">Babille</option>
                <option value = "Bacon">Baco</option>
                <option value = "Badme">Badme</option>
                <option value = "Bahir Dar">Bahir Dar</option>
                <option value = "Bati">Bati</option>
                <option value = "Bedele">Bedele</option>
                <option value = "Beica">Beica</option>
                <option value = "Bichena">Bichena</option>
                <option value = "Bishoftu">Bishoftu</option>
                <option value = "Bonga">Bonga</option>
                <option value = "Bonga">Burie Damote</option>
                <option value = "Butajira">Butajira</option>
                <option value = "Ciro">Ciro</option>
                <option value = "Chencha">Chencha</option>
                <option value = "Chuahit">Chuahit</option>
                <option value = "Chelenko">chelenko</option>
                <option value = "Dabat">Dabat</option>
                <option value = "Dangila">Dangila</option>
                <option value = "Dabarq">Debarq</option>
                <option value = "Debre Berhan">Debre Berhan</option>
                <option value = "Debre MArqos">Debre Marqos</option>
                <option value = "Debre Tabor">Debre Tabor</option>
                <option value = "Debre Werq">Debre Werq</option>
                <option value = "Debre Zebit">Debre Zebit</option>
                <option value = "Dejen">Dejen</option>
                <option value = "Delgi">Delgi</option>
                <option value = "Dembidolo">Dembidolo</option>
                <option value = "Dessie">Dessie</option>
                <option value = "Dila">Dila</option>
                <option value = "DilYibza (Beyeda)">DilYibza (Beyeda)</option>
                <option value = "Dire Dawa">Dire Dawa</option>
                <option value = "Dolo Bay">Dolo Bay</option>
                <option value = "Dolo Odo">Dolo Odo</option>
                <option value = "Durame">Durame</option>
                <option value = "Finicha'a">Finicha'a</option>
                <option value = "Fichae">Fiche</option>
                <option value = "Finote Selam">Finote Selam</option>
                <option value = "Freweyni">Freweyni</option>
                <option value = "Gambela">Gambela</option>
                <option value = "Gelemso">Gelemso</option>
                <option value = "Ghimbi">Ghimbi</option>
                <option value = "Ginir">Ginir</option>
                <option value = "Goba">Goba</option>
                <option value = "Gode">Gode</option>
                <option value = "Gonder">Gondar</option>
                <option value = "Gongoma">Gongoma</option>
                <option value = "Gore">Gore</option>
                <option value = "Gorgora">Gorgora</option>
                <option value = "Harar">Harar</option>
                <option value = "Hayq">Hayq</option>
                <option value = "Holeta">Holeta</option>
                <option value = "Hosaena">Hosaena</option>
                <option value = "Humera">Humera</option>
                <option value = "Imi">Imi</option>
                <option value = "Jijiga">Jijiga</option>
                <option value = "Jimma">Jimma</option>
                <option value = "Jinka">Jinka</option>
                <option value = "Kabri Dar">Kabri Dar</option>
                <option value = "Kebri Mangest">Kebri Mangest</option>
                <option value = "Kobo">Kobo</option>
                <option value = "Kombolcha">Kombolcha</option>
                <option value = "Konso">Konso</option>
                <option value = "Kulubi">Kulubi</option>
                <option value = "Lalibela">Lalibela</option>
                <option value = "Limmu Genet">Limmu Genet</option>
                <option value = "Maji">Maji</option>
                <option value = "Maychew">Maychew</option>
                <option value = "Mek'ele">Mek'ele</option>
                <option value = "Mendi">Mendi</option>
                <option value = "Metemma">Metemma</option>
                <option value = "Metu">Metu</option>
                <option value = "Mizan Teferi">Mizan Teferi</option>
                <option value = "Mojo">Mojo</option>
                <option value = "Mota">Mota</option>
                <option value = "Moyale">Moyale</option>
                <option value = "Negash">Negash</option>
                <option value = "Negele Boran">Negele Boran</option>
                <option value = "Nekemte">Nekemte</option>
                <option value = "Shashamane">Shashamane</option>
                <option value ="Shire">Shire</option>
                <option value = "Shilavo">Shilavo</option>
                <option value = "Sodo">Sodo</option>
                <option value = "Sodore">Sodore</option>
                <option value = "Sokoru">Sokoru</option>
                <option value = "Tefki">Tefki</option>
                <option value = "Tenta">Tenta</option>
                <option value ="Tiya">Tiya</option>
                <option value = "Tullu Melki">Tullu Melki</option>
                <option value = "Turmi">Turmi</option>
                <option value = "Wacca">Wacca</option>
                <option value = "Walwal">Walwal</option>
                <option value = "Werder">Werder</option>
                <option value = "Wereta">Wereta</option>
                <option value = "Woldia">Woldia</option>
                <option value = "Wolaita Sodo">Wolaita Sodo </option>
                <option value = "Waliso">Waliso</option>
                <option value = "Wolleka">Wolleka</option>
                <option value = "Wuchale">Wuchale</option>
                <option value = "Ziway">Ziway</option>
                <option value = ">Abiy Addi">Abiy Addi</option>
                <option value = ">Adama">Adama</option>
                <option value = ">Addis Ababa">Addis Ababa</option>
                <option value = ">Addis Alem">Addis Alem</option>
                <option value = ">Addis Zemen">Addis Zemen</option>
                <option value = ">Adet">Adet</option>
                <option value = ">Adigrat">Adigrat</option>
                <option value = ">Adwa">Adwa</option>
                <option value = ">Agaro">Agaro</option>
                <option value = ">Akaki">Akaki</option>
                <option value = ">Alaba">Alaba</option>
                <option value = ">Alitena">Alitena</option>
                <option value = ">Amba Mariam">Amba Mariam</option>
                <option value = ">Ambo">Ambo</option>
                <option value = ">Ankober">Ankober</option>
                <option value = ">Arba Minch">Arba Minch</option>
                <option value = ">Arboye">Arboye</option>
                <option value = ">Asaita">Asaita</option>
                <option value = ">Asella">Asella</option>
                <option value = ">Asossa">Asosa</option>
                <option value = ">Awasa">Awasa</option>
                <option value = ">Awash">Awash</option>
                <option value = ">Axum">Axum</option>
                <option value = ">Alamata">Alamata</option>
                <option value = "Babilla">Babille</option>
                <option value = "Bacon">Baco</option>
                <option value = "Badme">Badme</option>
                <option value = "Bahir Dar">Bahir Dar</option>
                <option value = "Bati">Bati</option>
                <option value = "Bedele">Bedele</option>
                <option value = "Beica">Beica</option>
                <option value = "Bichena">Bichena</option>
                <option value = "Bishoftu">Bishoftu</option>
                <option value = "Bonga">Bonga</option>
                <option value = "Bonga">Burie Damote</option>
                <option value = "Butajira">Butajira</option>
                <option value = "Ciro">Ciro</option>
                <option value = "Chencha">Chencha</option>
                <option value = "Chuahit">Chuahit</option>
                <option value = "Chelenko">chelenko</option>
                <option value = "Dabat">Dabat</option>
                <option value = "Dangila">Dangila</option>
                <option value = "Dabarq">Debarq</option>
                <option value = "Debre Berhan">Debre Berhan</option>
                <option value = "Debre MArqos">Debre Marqos</option>
                <option value = "Debre Tabor">Debre Tabor</option>
                <option value = "Debre Werq">Debre Werq</option>
                <option value = "Debre Zebit">Debre Zebit</option>
                <option value = "Dejen">Dejen</option>
                <option value = "Delgi">Delgi</option>
                <option value = "Dembidolo">Dembidolo</option>
                <option value = "Dessie">Dessie</option>
                <option value = "Dila">Dila</option>
                <option value = "DilYibza (Beyeda)">DilYibza (Beyeda)</option>
                <option value = "Dire Dawa">Dire Dawa</option>
                <option value = "Dolo Bay">Dolo Bay</option>
                <option value = "Dolo Odo">Dolo Odo</option>
                <option value = "Durame">Durame</option>
                <option value = "Finicha'a">Finicha'a</option>
                <option value = "Fichae">Fiche</option>
                <option value = "Finote Selam">Finote Selam</option>
                <option value = "Freweyni">Freweyni</option>
                <option value = "Gambela">Gambela</option>
                <option value = "Gelemso">Gelemso</option>
                <option value = "Ghimbi">Ghimbi</option>
                <option value = "Ginir">Ginir</option>
                <option value = "Goba">Goba</option>
                <option value = "Gode">Gode</option>
                <option value = "Gonder">Gondar</option>
                <option value = "Gongoma">Gongoma</option>
                <option value = "Gore">Gore</option>
                <option value = "Gorgora">Gorgora</option>
                <option value = "Harar">Harar</option>
                <option value = "Hayq">Hayq</option>
                <option value = "Holeta">Holeta</option>
                <option value = "Hosaena">Hosaena</option>
                <option value = "Humera">Humera</option>
                <option value = "Imi">Imi</option>
                <option value = "Jijiga">Jijiga</option>
                <option value = "Jimma">Jimma</option>
                <option value = "Jinka">Jinka</option>
                <option value = "Kabri Dar">Kabri Dar</option>
                <option value = "Kebri Mangest">Kebri Mangest</option>
                <option value = "Kobo">Kobo</option>
                <option value = "Kombolcha">Kombolcha</option>
                <option value = "Konso">Konso</option>
                <option value = "Kulubi">Kulubi</option>
                <option value = "Lalibela">Lalibela</option>
                <option value = "Limmu Genet">Limmu Genet</option>
                <option value = "Maji">Maji</option>
                <option value = "Maychew">Maychew</option>
                <option value = "Mek'ele">Mek'ele</option>
                <option value = "Mendi">Mendi</option>
                <option value = "Metemma">Metemma</option>
                <option value = "Metu">Metu</option>
                <option value = "Mizan Teferi">Mizan Teferi</option>
                <option value = "Mojo">Mojo</option>
                <option value = "Mota">Mota</option>
                <option value = "Moyale">Moyale</option>
                <option value = "Negash">Negash</option>
                <option value = "Negele Boran">Negele Boran</option>
                <option value = "Nekemte">Nekemte</option>
                <option value = "Shashamane">Shashamane</option>
                <option value ="Shire">Shire</option>
                <option value = "Shilavo">Shilavo</option>
                <option value = "Sodo">Sodo</option>
                <option value = "Sodore">Sodore</option>
                <option value = "Sokoru">Sokoru</option>
                <option value = "Tefki">Tefki</option>
                <option value = "Tenta">Tenta</option>
                <option value ="Tiya">Tiya</option>
                <option value = "Tullu Melki">Tullu Melki</option>
                <option value = "Turmi">Turmi</option>
                <option value = "Wacca">Wacca</option>
                <option value = "Walwal">Walwal</option>
                <option value = "Werder">Werder</option>
                <option value = "Wereta">Wereta</option>
                <option value = "Woldia">Woldia</option>
                <option value = "Wolaita Sodo">Wolaita Sodo </option>
                <option value = "Waliso">Waliso</option>
                <option value = "Wolleka">Wolleka</option>
                <option value = "Wuchale">Wuchale</option>
                <option value = "Ziway">Ziway</option>
	          </select>
	        </label>
	      </div>
        
        <div class="col-3">
	        <label>
	          Change Password 
	          <input placeholder="your new password" type="password" name="password"/>
	        </label>
	      </div>
          
          
		<div class="col-3">
	        <label>
	          CHANGE PROFILE PICTURE 
	          <input placeholder="profile photo" type="file" name="profilePhoto"/>
	        </label>
	    </div>	
        <?php if(!empty($profilePhotoError))
            echo "<div class='col-2 editError'>
                  <label>Selected file  is not valid!
                  <br/>$profilePhotoError
                  </label></div>";
          ?> 
                
	    <div class="col-submit">
	        <input type="submit" name="basicUpdate" class="submitbtn" value="Update"/>
	    </div>
	      
				
				
	      </form>

    
    <?php
         }
         
        
        
        
        
    }

    
    //testing space
    


?>