<?php
require_once('../model/Profile.php');

class ProfileHandler{
    
    public $self;    //current user profile
    public $currentSIndex;
    public function __construct($email){
        $this->self = new Profile($email);
        $this->currentSIndex = 0;
    }
    
    public function isNominee($email){
        $nominees = $this->self->profileInfo->getNominees();
        if(in_array($email,$nominees)){
            return true;
        }
        else{
            return false;
        }
    }
    
    public function isOnline($email){
        //                                                                            online is not totally done
    }
    
    public function init($profile){
        $profile->isNominee = $this->isNominee($profile->email);
        //$profile->isOnline = $this->isOnline($profile->email);                        online don't work if not implemented
    }
    
    public function simpleSearch($searchTerm,$currentSIndex=0,$number=6){
        //get database and validate connection to database
        $db = getDatabase();
        if(!$db){
            echo "<p>can't connect to database on searching profile for ".$this->self->email."</p>";
            exit();
        }
        
        //check if there are profiles exist with given search term
        $query = "select * from profile
                  where (firstName like '%$searchTerm%' or lastName like '%$searchTerm%'
                     or screenName like '%$searchTerm%' or email like '%$searchTerm%') ";
        
        //make query to give only opposite gender results
        $preferredGender = ($this->self->gender == 'M')? 'F' : 'M';
        $query .= "and (gender = '$preferredGender') ";
        $query .= "limit $currentSIndex,$number";
        
        
        
        $result = $db->query($query);
        if(!$result->num_rows){
            return null;       //note that this can also run because of database query is not executed successfully 
        }
        
        //create profile objects from database if validated and push in an array, then retrieve the array
        $profiles = array();
        while($row = $result->fetch_assoc())
        {
            $profile = new Profile($row['email']);
            array_push($profiles,$profile);
        }
             
        $result->free();
        $db->close();
        
        $this->currentSIndex = $currentSIndex + $number;    
        return $profiles;
            
    }

    public function changeToSet($heightArray){
        //create heightSet string using the heightArray for query 
             $heightSet = "(";
             $counter = 0;
             foreach($heightArray as $height){            
                 if($counter == 0)              //enter comma if it is not first
                    $heightSet = $heightSet."'".$height."'";
                 else
                    $heightSet = $heightSet.",'".$height."'";
                 
                 ++$counter;
             }
             $heightSet = $heightSet.")";

             return $heightSet;
    }
    
    public function advancedSearch($currentSIndex=0,$number=6){
         $heightArray = array(); // height specifications
         $query = "select profile.email,profile.location from profile,profileinformation 
                  where profile.email = profileinformation.infoEmail";
        if(!empty($_GET['initialAge'])){
            if(!empty($_GET['finalAge']) && ($_GET['finalAge'] < $_GET['initialAge'])){
                $initialAge = intval($_GET['finalAge']) * 365 * 24 * 3600;
            }
            else{    
                $initialAge = intval($_GET['initialAge']) * 365 * 24 * 3600;
            }
            $initialStamp = time() - $initialAge;
            $query .= " and profile.birthday < $initialStamp"; 
        }
        if(!empty($_GET['finalAge'])){
            if(!empty($_GET['initialAge']) && ($_GET['finalAge'] < $_GET['initialAge'])){
                $finalAge = intval($_GET['initialAge']) * 365 * 24 * 3600;
            }
            else{    
                $finalAge = intval($_GET['finalAge']) * 365 * 24 * 3600;
            }
            $finalStamp = time() - $finalAge;
            $query .= " and profile.birthday > $finalStamp"; 
        }
        if(!empty($_GET['religion'])){
            $religion = $_GET['religion'];
            $query .= " and profileinformation.religion = '$religion'";
        }
        if(!empty($_GET['location'])){
            $location = $_GET['location'];
            $query .= " and profile.location = '$location'";
        }
        if(!empty($_GET['mStatus'])){
            $mStatus = $_GET['mStatus'];
            $query .= " and profileinformation.mStatus = '$mStatus'";
        }
        if(!empty($_GET['initialHeight']) && !empty($_GET['finalHeight'])){
            $initialHeight = array_search($_GET['initialHeight'],$heightArray);
            $finalHeight = array_search($_GET['finalHeight'],$heightArray);
            if($finalHeight < $initialHeight){
                $temp = $finalHeight;
                $finalHeight = $initialHeight;
                $initialHeight = $temp;
            }
            $length = $finalHeight - $initialHeight + 1;
            $heightArray = array_slice($heightArray,$initialHeight,$length);
            
            $heightSet = $this->changeToSet($heightArray);
            $query .= " and profileinformation.height in $heightSet";
        }
        else if(!empty($_GET['initialHeight'])){
            $initialHeight = array_search($_GET['initialHeight'],$heightArray);
            $length = count($heightArray) - $initialHeight; 
            $heightArray = array_slice($heightArray,$initialHeight,$length);

            $heightSet = $this->changeToSet($heightArray);
            $query .= " and profileinformation.height in $heightSet"; 
        }
        else if(!empty($_GET['finalHeight'])){
            $finalHeight = array_search($_GET['finalHeight'],$heightArray);
            $length = $finalHeight + 1; 
            $heightArray = array_slice($heightArray,0,$length);

            $heightSet = $this->changeToSet($heightArray);
            $query .= " and profileinformation.height in $heightSet"; 
        }
        if(!empty($_GET['build'])){
            $build = $_GET['build'];
            $query .= " and profileinformation.build = '$build'";
        }
        if(!empty($_GET['education'])){
            $education = $_GET['education'];
            $query .= " and profileinformation.edcuation = '$education'";
        }
        if(!empty($_GET['occupation'])){
            $occupation = $_GET['occupation'];
            $query .= " and profileinformation.occupation = '$occupation'";
        }
        if(!empty($_GET['drinking'])){
            $drinking = $_GET['drinking'];
            $query .= " and profileinformation.drinking = '$drinking'";
        }
        if(!empty($_GET['smoking'])){
            $smoking = $_GET['smoking'];
            $query .= " and profileinformation.smoking = '$smoking'";
        }
        if(!empty($_GET['haveChildren'])){
            $haveChildren = $_GET['haveChildren'];
            $query .= " and profileinformation.haveChildren = '$haveChildren'";
        }

        //make query to give only opposite gender results
        $preferredGender = ($this->self->gender == 'M')? 'F' : 'M';
        $query .= " and (gender = '$preferredGender') ";
        $query .= "limit $currentSIndex,$number";
        
        
        //get database and validate connection to database
        $db = getDatabase();
        if(!$db){
            echo "<p>can't connect to database on searching profile for ".$this->self->email."</p>";
            exit();
        }
        
        $result = $db->query($query);
        if(!$result->num_rows){
            return null;       //note that this can also run because of database query is not executed successfully 
        }
        
        //create profile objects from database if validated and push in an array, then retrieve the array
        $profiles = array();
        while($row = $result->fetch_assoc())
        {
            $profile = new Profile($row['email']);
            array_push($profiles,$profile);
        }
             
        $result->free();
        $db->close();
        
        $this->currentSIndex = $currentSIndex + $number;    
        return $profiles;
            
    
    }
    
    public function getSuggestedProfiles($currentSIndex=0,$number=10){
        $heightArray = array(); // height specifications
         $query = "select profile.email,profile.location from profile,profileinformation 
                  where profile.email = profileinformation.infoEmail";
        if(!empty($self->ageRange[0])){
            if(!empty($self->ageRange[1]) && ($self->ageRange[1] < $self->ageRange[0])){
                $initialAge = intval($self->ageRange[1]) * 365 * 24 * 3600;
            }
            else{    
                $initialAge = intval($self->ageRange[0]) * 365 * 24 * 3600;
            }
            $initialStamp = time() - $initialAge;
            $query .= " and profile.birthday < $initialStamp"; 
        }
        if(!empty($self->ageRange[1])){
            if(!empty($self->ageRange[0]) && ($self->ageRange[1] < $self->ageRange[0])){
                $finalAge = intval($self->ageRange[0]) * 365 * 24 * 3600;
            }
            else{    
                $finalAge = intval($self->ageRange[1]) * 365 * 24 * 3600;
            }
            $finalStamp = time() - $finalAge;
            $query .= " and profile.birthday > $finalStamp"; 
        }
        if(!empty($self->religion)){
            $religion = $self->religion;
            $query .= " and profileinformation.religion = '$religion'";
        }
        if(!empty($self->location)){
            $location = $self->location;
            $query .= " and profile.location = '$location'";
        }
        
        if(!empty($self->mStatus)){
            $mStatus = $self->mStatus;
            $query .= " and profileinformation.mStatus = '$mStatus'";
        }
        if(!empty($self->heightRange[0]) && !empty($self->heightRange[1])){
            $initialHeight = array_search($self->heightRange[0],$heightArray);
            $finalHeight = array_search($self->heightRange[1],$heightArray);
            if($finalHeight < $initialHeight){
                $temp = $finalHeight;
                $finalHeight = $initialHeight;
                $initialHeight = $temp;
            }
            $length = $finalHeight - $initialHeight + 1;
            $heightArray = array_slice($heightArray,$initialHeight,$length);
            
            $heightSet = $this->changeToSet($heightArray);
            $query .= " and profileinformation.height in $heightSet";
        }
        else if(!empty($self->heightRange[0])){
            $initialHeight = array_search($self->heightRange[0],$heightArray);
            $length = count($heightArray) - $initialHeight; 
            $heightArray = array_slice($heightArray,$initialHeight,$length);

            $heightSet = $this->changeToSet($heightArray);
            $query .= " and profileinformation.height in $heightSet"; 
        }
        else if(!empty($self->heightRange[1])){
            $finalHeight = array_search($self->heightRange[1],$heightArray);
            $length = $finalHeight + 1; 
            $heightArray = array_slice($heightArray,0,$length);

            $heightSet = $this->changeToSet($heightArray);
            $query .= " and profileinformation.height in $heightSet"; 
        }
        if(!empty($self->build)){
            $build = $self->build;
            $query .= " and profileinformation.build = '$build'";
        }
        if(!empty($self->education)){
            $education = $self->education;
            $query .= " and profileinformation.edcuation = '$education'";
        }
        if(!empty($self->occupation)){
            $occupation = $self->occupation;
            $query .= " and profileinformation.occupation = '$occupation'";
        }
        if(!empty($self->drinking)){
            $drinking = $self->drinking;
            $query .= " and profileinformation.drinking = '$drinking'";
        }
        if(!empty($self->smoking)){
            $smoking = $self->smoking;
            $query .= " and profileinformation.smoking = '$smoking'";
        }
        if(!empty($self->haveChildren)){
            $haveChildren = $self->haveChildren;
            $query .= " and profileinformation.haveChildren = '$haveChildren'";
        }

        //make query to give only opposite gender results
        $preferredGender = ($this->self->gender == 'M')? 'F' : 'M';
        $query .= " and (gender = '$preferredGender') ";
        $query .= "limit $currentSIndex,$number";
        
        

        //get database and validate connection to database
        $db = getDatabase();
        if(!$db){
            echo "<p>can't connect to database on searching profile for ".$this->self->email."</p>";
            exit();
        }
        
        $result = $db->query($query);
        if(!$result->num_rows){
            return null;       //note that this can also run because of database query is not executed successfully 
        }
        
        //create profile objects from database if validated and push in an array, then retrieve the array
        $profiles = array();
        while($row = $result->fetch_assoc())
        {
            $profile = new Profile($row['email']);
            array_push($profiles,$profile);
        }
             
        $result->free();
        $db->close();
        
        $this->currentSIndex = $currentSIndex + $number; 
        
        if(empty($initialStamp) && empty($finalStamp) && empty($initialHeight) && empty($finalHeight) && empty($build) 
        && empty($location) && empty($religion) && empty($mStatus) && empty($education) && empty($occupation) 
        && empty($haveChildren) && empty($smoking) && empty($drinking)){
            return $profiles;
        }
        else{
            shuffle($profiles);
            return $profiles;
        }
    }   
}

?>