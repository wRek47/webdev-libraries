<?php

class Message {

    public $sender;
    public $receiver;
    public $cc = array();

    public $log;
    public $thread = null;

    public $subject;
    public $body;

    public function get_mail() {
    
        $this->find_message();
        # read mail
    
    }

    public function find_message() {
    
        global $pdo, $profile;

        if (isset($_GET['message'])): $target = $_GET['message']; endif;

        if (isset($target)):
        
            $sql = "SELECT * FROM `messages` WHERE `receiver` = :receiver AND `message` = :msg ORDER BY `created` DESC LIMIT 1";
            $statement = $pdo->prepare($sql); unset($sql);
            $statement->bindParam(":msg", $target);
        
        else:
        
            $sql = "SELECT * FROM `messages` WHERE `receiver` = :receiver ORDER BY `created` DESC LIMIT 1";
            $statement = $pdo->prepare($sql); unset($sql);
        
        endif;

        $statement->bindParam(":receiver", $profile->target);
        $data = $statement->fetch_result(); unset($statement);

        $this->sender = $data['sender'];
        $this->receiver = $data['receiver'];
        $this->cc = unserialize($data['cc']);
        $this->log = unserialize($data['log']);
        $this->thread = $data['thread'];
        
        $this->subject = $data['subject'];
        $this->body = $data['body'];
    
    }

    public function read_mail($message) {
    
        global $profile;

        $stamp = new Stamp;
            $stamp->target = "read";
            $stamp->profile = $profile->target;
        
        array_push($message->log, $stamp); unset($stamp);
    
    }

    public function send_mail() {

        if (isset($_POST['message'])):
        
            # send mail
        
        endif;
    
    }

}

?>