<?php

/*

    In theory, this system uses a toxicity-index to evaluate how the website is being used,
    granting or subtracting points based on a users behaviour vs expected behaviours.

    They do not directly affect the viewers experience, at this point in time.
    At this time, (non)toxicity-points cannot be relied on for intelligently authorizing blacklisting/whitelisting actions.

    Should the concept of toxicity-points be a reliable means of evaluating behaviour, the system can then
    employ the actions of other such systems (ie: banning profiles/ips/etc).

    For the purpose of creating a theoretical test environment, this system only logs a users interaction,
    and (re)-evaluates their `toxicity` against a public `warrant level`.

    Possible nontoxicities include:
    - viewing page sources
    - website/page bookmarking
    - feature usage
    - unique page referals
    - surface-to-depth urls
    - high traffic behaviour
    - familiar browsers
    - idling the website
    - cross-site redirection

    Possible toxicities include:
    - viewing page sources
    - uncommon page referals
    - form tampering
    - url tampering
    - cookie tampering
    - session tampering
    - strange agents/browsers
    - excess traffic behaviour
    - leaving the website
    - idling the website

    From these a profile can be assigned points, which can then be compared to an `average toxic user`.
    This theory is a essentially a system that finds out:
        `$is_hacker = true|false;`
        `$is_person = true|false;`
    And says:
        if ($toxicity <=> $warrant_level) { # do what you need }

*/

class ProfileToxicity {

    public $session = "points-log";

    public function __construct() {
    
        $this->warrant_level = $this->get_warrant_level();

        $target = $this->session;

        if (!isset($_SESSION[$target])):
        
            $session = (object) array();
                $session->started = time();
                $session->stamps = array();
            
            $_SESSION[$target] = serialize($session); unset($session);
        
        endif;
    
    }

    public function stamp_log($description = "No description.") {

        $target = $this->session;
        $log = unserialize($_SESSION[$target]);

        $stamp = (object) array();
            $stamp->logged = time();
            $stamp->referer = $_SERVER['HTTP_REFERER'];
            $stamp->description = $description;
        
        array_push($log->stamps, $stamp); unset($stamp);

        $_SESSION[$target] = serialize($log);
    
    }

    public function get_warrant_level() {

        global $db;

        $sql = "SELECT AVG(`points`) FROM `users`";
        $query = $db->prepare($sql);
        $query->execute();

        $result = $query->fetch_result();
        $warrant_level = $result; unset($result);

        return $warrant_level;
    
    }

    public function has_warrant($profile) {
    
        if ($profile->security->toxicity > $this->warrant_level):
        
            return true;
        
        else:
        
            return false;
        
        endif;
    
    }

}

?>