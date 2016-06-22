<?php

    require_once("../util/connection.php");
    
    class ProfileInfo
    {
        public $infoEmail;
        public $mStatus;
        public $religion;
        public $haveChildren;
        public $height;
        public $build;
        public $education;
        public $occupation;
        public $smoking;
        public $drinking;
        public $phoneNumber;
        public $nomineeList;
        
        public function __construct($email)
        {
            //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in constructing profileInfo for ".$email."</p>";
                 exit();
             }
             
             //check if the profile exists with given email
             $query = "select * from profileInformation
                       where infoEmail='".$email."'";
             $result = $db->query($query);
             if(!$result->num_rows){
                 echo "<p>no profile in database in constructing profileInfo for ".$email."</p>";
                 exit;
             }
             
             //initialize class from database if validated
             $row = $result->fetch_assoc();
             
             $this->infoEmail = $email;
             $this->mStatus = stripslashes($row['mStatus']);
             $this->religion = stripslashes($row['religion']);
             $this->haveChildren = stripslashes($row['haveChildren']);
             $this->height = stripslashes($row['height']);
             $this->build = stripslashes($row['build']);
             $this->education = stripslashes($row['education']);
             $this->occupation = stripslashes($row['occupation']);
             $this->smoking = stripslashes($row['smoking']);
             $this->drinking = stripslashes($row['drinking']);
             $this->phoneNumber = stripslashes($row['phoneNumber']);
             $this->nomineeList = stripslashes($row['nomineeList']);
             
             $result->free();
             $db->close();
        }
        
        public function update()
         {
             //get database and validate connection to database
             $db = getDatabase();
             if(!$db){
                 echo "<p>can't connect to database in updating profileInfo for ".$this->infoEmail."</p>";
             }
             
             //update profile in database
             $query = "update profileInformation
                       set mStatus='".$this->mStatus."', religion='".$this->religion."', 
                           haveChildren='".$this->haveChildren."', height='".$this->height."',
                           build='".$this->build."', education='".$this->education."',
                           occupation='".$this->occupation."', smoking='".$this->smoking."',
                           drinking='".$this->drinking."', phoneNumber='".$this->phoneNumber."',
                           nomineeList='".$this->nomineeList."' 
                       where infoEmail='".$this->infoEmail."'";
             $updated = $db->query($query);
             if(!$updated){
                 echo "<p>can't update profile to database in updating profile for ".$this->infoEmail."</p>";
                 exit;
             }
             
             $db->close();
             
         }
         
         
         public function getNominees(){
             $delimiter = ",";
             $nominees = explode($delimiter,$this->nomineeList);
             if(empty($nominees[0]))
                return array();
             else
                return $nominees;
         }
         
         public function setNominees($nominees){
             $list = "";
             $nCounter = 0;
             foreach($nominees as $element){
                 if($nCounter == 0)
                    $list = $list.$element;
                 else
                    $list = $list.",".$element;
                 $nCounter++;
             }
             $this->nomineeList = $list;
         }
         
         public function viewAsTable(){
             echo "<table class='table'>";
             if(!empty($this->mStatus)){
                 echo "<tr><td>Marital Status</td><td>$this->mStatus</td></tr>";
             }
             if(!empty($this->religion)){
                 echo "<tr><td>Religion</td><td>$this->religion</td></tr>";
             }
             if(!empty($this->haveChildren)){
                 echo "<tr><td>Have Children</td><td>$this->haveChildren</td></tr>";
             }
             if(!empty($this->phoneNumber)){
                 echo "<tr>Phone Number<td></td><td>$this->phoneNumber</td></tr>";
             }
             if(!empty($this->height)){
                 echo "<tr><td>Height</td><td>$this->height</td></tr>";
             }
             if(!empty($this->build)){
                 echo "<tr><td>Build</td><td>$this->build</td></tr>";
             }
             if(!empty($this->education)){
                 echo "<tr><td>Education</td><td>$this->education</td></tr>";
             }
             if(!empty($this->smoking)){
                 echo "<tr><td>Smoking</td><td>$this->smoking</td></tr>";
             }
             if(!empty($this->drinking)){
                 echo "<tr><td>Drinking</td><td>$this->drinking</td></tr>";
             }
             
             if(empty($this->mStatus) && empty($this->religion) && empty($this->haveChildren) &&
                empty($this->height)  && empty($this->build)    && empty($this->education) &&
                empty($this->occupation) && empty($this->smoking) && empty($this->drinking) &&
                empty($this->phoneNumber)) {
                 echo "<tr style='background-color:#f4f4f4;'>
                            <td style='width:100%;text-align:center;font-weight:normal'>No profile information is filled</td>
                       </tr>";
             }
             
             echo "</table>";
         }
         
         
         function viewAsEditable(){
             ?> 
                
    <form action="php/editPage.php" method="post" enctype="multipart/form-data" class="editableForm">
  		 								      	      
	      <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Marital Status
	          <select tabindex="3" name="mStatus">
    <?php echo "<option value='$this->mStatus' selected>$this->mStatus</option>"; ?>
	            <option value=""> I 'll tell you later </option>
                <option value="Never Married" selected="selected">Never Married</option>
                <option value="Divorced" >Divorced</option>
                <option value="Separated" >Separated</option>
                <option value="Widowed" >Widowed</option>
                <option value="Married" >Married</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Religion
	          <select tabindex="3" name="religion">
    <?php echo "<option value='$this->religion' selected>$this->religion</option>"; ?>
	            <option value=""> I 'll tell you later </option>
                <option value="agnostic" >Agnostic</option>
                <option value="atheist" >Atheist</option>
                <option value="bahai" >Baha'i</option>
                <option value="buddhist" >Buddhist</option>
                <option value="christian" >Christian</option>
                <option value="greek-orthodox" >Greek Orthodox</option>
                <option value="hindu" >Hindu</option>
                <option value="jewish" >Jewish</option>
                <option value="muslim" >Muslim</option>
                <option value="other" >Other</option>
                <option value="sikh" >Sikh</option>
                <option value="spiritual" >Spiritual</option>
                <option value="zoroastrian" >Zoroastrian</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Have children
	          <select tabindex="3" name="haveChildren">
    <?php echo "<option value='$this->haveChildren' selected>$this->haveChildren</option>"; ?>
	            <option value="" >I'll tell you later</option>
              <option value="No" >No</option>
              <option value="Yes" >Yes</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Height
	          <select tabindex="3" name="height">
    <?php echo "<option value='$this->height' selected>$this->height</option>"; ?>
	            <option value=""> I 'll tell you later </option>
              <option value="91 cm" >3'  0"   (91 cm) </option>
              <option value="94 cm" >3'  1"   (94 cm) </option>
              <option value="97 cm" >3'  2"   (97 cm) </option>
              <option value="99 cm" >3'  3"   (99 cm) </option>
              <option value="102 cm" >3'  4"   (102 cm)</option>
              <option value="104 cm" >3'  5"   (104 cm)</option>
              <option value="107 cm" >3'  6"   (107 cm)</option>
              <option value="109 cm" >3'  7"   (109 cm)</option>
              <option value="112 cm" >3'  8"   (112 cm)</option>
              <option value="114 cm" >3'  9"   (114 cm)</option>
              <option value="117 cm" >3' 10"   (117 cm)</option>
              <option value="119 cm" >3' 11"   (119 cm)</option>
              <option value="122 cm" >4'  0"   (122 cm)</option>
              <option value="124 cm" >4'  1"   (124 cm)</option>
              <option value="127 cm" >4'  2"   (127 cm)</option>
              <option value="130 cm" >4'  3"   (130 cm)</option>
              <option value="132 cm" >4'  4"   (132 cm)</option>
              <option value="135 cm" >4'  5"   (135 cm)</option>
              <option value="137 cm" >4'  6"   (137 cm)</option>
              <option value="140 cm" >4'  7"   (140 cm)</option>
              <option value="142 cm" >4'  8"   (142 cm)</option>
              <option value="145 cm" >4'  9"   (145 cm)</option>
              <option value="147 cm" >4' 10"   (147 cm)</option>
              <option value="150 cm" >4' 11"   (150 cm)</option>
              <option value="152 cm" >5'  0"   (152 cm)</option>
              <option value="155 cm" >5'  1"   (155 cm)</option>
              <option value="157 cm" >5'  2"   (157 cm)</option>
              <option value="160 cm" >5'  3"   (160 cm)</option>
              <option value="163 cm" >5'  4"   (163 cm)</option>
              <option value="165 cm" >5'  5"   (165 cm)</option>
              <option value="168 cm" >5'  6"   (168 cm)</option>
              <option value="170 cm" >5'  7"   (170 cm)</option>
              <option value="173 cm" >5'  8"   (173 cm)</option>
              <option value="175 cm" >5'  9"   (175 cm)</option>
              <option value="178 cm" >5' 10"   (178 cm)</option>
              <option value="180 cm" >5' 11"   (180 cm)</option>
              <option value="183 cm" >6'  0"   (183 cm)</option>
              <option value="185 cm" >6'  1"   (185 cm)</option>
              <option value="188 cm" >6'  2"   (188 cm)</option>
              <option value="190 cm" >6'  3"   (190 cm)</option>
              <option value="193 cm" >6'  4"   (193 cm)</option>
              <option value="196 cm" >6'  5"   (196 cm)</option>
              <option value="198 cm" >6'  6"   (198 cm)</option>
              <option value="201 cm" >6'  7"   (201 cm)</option>
              <option value="203 cm" >6'  8"   (203 cm)</option>
              <option value="206 cm" >6'  9"   (206 cm)</option>
              <option value="208 cm" >6' 10"   (208 cm)</option>
              <option value="211 cm" >6' 11"   (211 cm)</option>
              <option value="213 cm" >7'  0"   (213 cm)</option>
              <option value="216 cm" >7'  1"   (216 cm)</option>
              <option value="218 cm" >7'  2"   (218 cm)</option>
              <option value="221 cm" >7'  3"   (221 cm)</option>
              <option value="224 cm" >7'  4"   (224 cm)</option>
              <option value="226 cm" >7'  5"   (226 cm)</option>
              <option value="229 cm" >7'  6"   (229 cm)</option>
	          </select>
	        </label>
	      </div>  
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Build
	          <select tabindex="3" name="build">
    <?php echo "<option value='$this->build' selected>$this->build</option>"; ?>
	            <option value=""> I 'll tell you later </option>
              <option value="Small" >Small</option>
              <option value="Slender" >Slender</option>
              <option value="Average" >Average</option>
              <option value="Athletic" >Athletic</option>
              <option value="Stocky" >Stocky</option>
              <option value="A few extra pounds" >A few extra pounds</option>
              <option value="Heavyset" >Heavyset</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Education
	          <select tabindex="3" name="education">
    <?php echo "<option value='$this->education' selected>$this->education</option>"; ?>
	            <option value=""> I 'll tell you later </option>
							<option value="High School" >High School</option>
							<option value="Some College" >Some College</option>
							<option value="Technical School" >Technical School</option>
							<option value="Associate Degree" >Associate Degree</option>
							<option value="Bachelor's Degree" >Bachelor's Degree</option>
							<option value="Graduate Student" >Graduate Student</option>
							<option value="Professional Degree" >Professional Degree</option>
							<option value="Master's Degree" >Master's Degree</option>
							<option value="Doctoral Degree" >Doctoral Degree</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Occupation
	          <select tabindex="3" name="occupation">
    <?php echo "<option value='$this->occupation' selected>$this->occupation</option>"; ?>
	            <option value="" >I'll tell you later</option>
              <option value="Administrative / Human Resources" >Administrative / Human 

              Resources</option>
              <option value="Advertising / Marketing / Public Relations" >Advertising / Marketing / 

              Public Relations</option>
              <option value="Architecture / Interior Design" >Architecture / Interior Design</option>
              <option value="Automotive / Aviation / Transportation" >Automotive / Aviation / 

              Transportation</option>
              <option value="Communication / Telecom" >Communication / Telecom</option>
              <option value="Construction / Agriculture / Landscaping" >Construction / Agriculture / 

              Landscaping</option>
              <option value="Design / Visual / Graphic Arts" >Design / Visual / Graphic Arts</option>
              <option value="Education / Teaching / Child Care" >Education / Teaching / Child 

              Care</option>
              <option value="Entertainment / Media / Dramatic Arts" >Entertainment / Media / Dramatic 

              Arts</option>
              <option value="Entrepreneurial / Start Up" >Entrepreneurial / Start Up</option>
              <option value="Executive / General Manager / Consulting" >Executive / General Manager / 

              Consulting</option>
              <option value="Fashion / Style / Modeling / Apparel / Beauty" >Fashion / Style / 

              Modeling / Apparel / Beauty</option>
              <option value="Financial / Accounting / Insurance / Real Estate" >Financial / Accounting 

              / Insurance / Real Estate</option>
              <option value="Government / Civil Service / Public Policy" >Government / Civil Service / 

              Public Policy</option>
              <option value="Homemaking / Child Rearing" >Homemaking / Child Rearing</option>
              <option value="Internet / E-Commerce Technology" >Internet / E-Commerce 

              Technology</option>
              <option value="Law / Legal / Judiciary" >Law / Legal / Judiciary</option>
              <option value="Law Enforcement / Military" >Law Enforcement / Military</option>
              <option value="Manufacturing / Warehousing / Shipping" >Manufacturing / Warehousing / 

              Shipping</option>
              <option value="Medical / Health / Fitness / Social Services" >Medical / Health / Fitness 

              / Social Services</option>
              <option value="Nonprofit / Volunteer / Activist" >Nonprofit / Volunteer / 

              Activist</option>
              <option value="" >Other</option>
              <option value="Other Professional Services / Trade" >Other Professional Services / 

              Trade</option>
              <option value="Public Safety / Fire / Paramedic" >Public Safety / Fire / 

              Paramedic</option>
              <option value="Publishing / Print Media" >Publishing / Print Media</option>
              <option value="Restaurant / Food Service / Catering" >Restaurant / Food Service / 

              Catering</option>
              <option value="Retired" >Retired</option>
              <option value="Sales Representative / Retail / Wholesale" >Sales Representative / Retail 

              / Wholesale</option>
              <option value="Student" >Student</option>
              <option value="Technical Science / Engineering" >Technical Science / 

              Engineering</option>
              <option value="Travel / Recreation / Leisure /" >Travel / Recreation / Leisure / 

              Hospitality</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Smoking
	          <select tabindex="3" name="smoking">
    <?php echo "<option value='$this->smoking' selected>$this->smoking</option>"; ?>
	            <option value=""> I 'll tell you later </option>
              <option value="Non-Smoker" >Non-Smoker</option>
              <option value="Smoker" >Smoker</option>
              <option value="Social Smoker" >Social Smoker</option>
              <option value="Trying to Quit" >Trying to Quit</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-4" style="padding-bottom:2.4px">
	      <label>
	       Drinking
	          <select tabindex="3" name="drinking">
    <?php echo "<option value='$this->drinking' selected>$this->drinking</option>"; ?>
	            <option value="" >I'll tell you later</option>
							<option value="Never" >Never</option>
							<option value="Socially" >Socially</option>
							<option value="Regularly" >Regularly</option>
	          </select>
	        </label>
	      </div>
          
          <div class="col-2">
	        <label>
	          Phone Number
	          <input <?php echo "value='$this->phoneNumber'" ?> type="text"  name="phoneNumber" tabindex="5"  required>
	        </label>
	      </div>    	      
	      <?php if(!empty($phoneNumberError))
            echo "<div class='col-2 editError'>
                  <label>Phone number is not valid!
                  <br/>$phoneNumberError
                  </label></div>";
          ?>
                
	    <div class="col-submit">
	        <input type="submit" class="submitbtn" value="Update"/>
	    </div>
	      
				
				
	      </form>

    
    <?php
         }
         
         
    }

?>