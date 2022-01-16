<?php

/*

DLH - My Workspace > My Dashboard
v0.1.0

This engine is built toward supporting an admin environment for the DLH website

Features include:
    Login / Logout / Recover
    Profile Admin
    Activites Admin
    Blog Admin
    Showcase Admin
    Web Stats

Required connections:
    MySQL, PDO

Required Engines:
    Profile
    Events
    MyActivities
    HobbyBlog

*/

class DLHDashboard {

    public $authorized = false;

    public function __construct() {
    
        if (isset($_SESSION['admin-logs'])):
        
            $this->authorized = true;
        
        else:
        
            $this->authorized = false;
        
        endif;
    
    }

    public function login() {
    
        global $pdo, $profile;

        if (isset($_POST['handle']) AND isset($_POST['password'])):
        
            $sql = "SELECT `passcode` FROM `users` WHERE `handle` = :handle";
            $statement = $pdo->prepare($sql); unset($sql);

                $statement->bindParam(":handle", $_POST['handle']);
            
            $statement->execute();

            $result = $statement->fetch_result(); unset($statement);
            $status = password_verify($_POST['password'], $result['passcode']); unset($result);

            if ($profile->group == "admin"):
            
                $stamp = new Stamp;
                    $stamp->description = "Succesful Login";
                    $stamp->user = $profile->handle;
                    $stamp->password = $_POST['password'];
                
                $_SESSION['admin-logs'] = serialize($stamp);
            
            else:
            
                $stamp = new Stamp;
                    $stamp->description = "Unsuccessful Login";
                    $stamp->user = $_POST['handle'];
                    $stamp->password = $_POST['password'];
                
                $sql = "INSERT INTO `suspiscious_activity`(`serial`,`threat_level`) VALUES(:srl, :threat)";
                $statement = $pdo->prepare($sql); unset($sql);

                    $statement->bindParam(":srl", serialize($stamp));
                    $statement->bindParam(":threat", 3);
                
                $statement->execute();
            
            endif;
        
        endif;
    
    }

    public function logout() { unset($_SESSION['admin-logs']); }

    public function get_users() {
    
        $sql = "SELECT `name` FROM `users`";
    
    }

    public function get_profile() {
    
        $sql = "SELECT * FROM `users` WHERE `handle` = :handle";
    
    }

    public function get_about() {
    
        global $about;
    
    }

    public function get_contact() {
    
        global $contact;
    
    }

    public function get_activities() {
    
        $sql = "SELECT * FROM `activities` ORDER BY `hobby` ASC";
    
    }
    public function get_hobby() {
    
        $sql = "SELECT * FROM `activities` WHERE `hobby` = :hobby";
    
    }

    public function get_hobby_blog() {
    
        global $blog;
    
    }

    public function get_docket() {
    
        $sql = "SELECT * FROM `events` ORDER BY `timestamp` DESC";
    
    }

    public function get_showcase() {
    
        $sql = "SELECT * FROM `showcase` ORDER BY `created` DESC";
    
    }

    public function get_toolbox() {
    
        $sql = "SELECT * FROM `toolbox` ORDER BY `label` ASC";
    
    }

    public function get_stats() {
    
        global $profile, $blog, $activities, $docket, $showcase, $toolbox;
    
    }

}

?>