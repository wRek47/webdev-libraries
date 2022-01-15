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
        $this->read_mail();
    
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

        $statement->bindParam(":receiver", $profile->handle);
        $data = $statement->fetch_result(); unset($statement);

        $this->sender = $data['sender'];
        $this->receiver = $data['receiver'];
        $this->cc = unserialize($data['cc']);
        $this->log = unserialize($data['log']);
        $this->thread = $data['thread'];
        
        $this->subject = $data['subject'];
        $this->body = $data['body'];
    
    }

    public function read_mail($message = null) {
    
        global $profile;

        $stamp = new Stamp;
            $stamp->target = "read";
            $stamp->profile = $profile->handle;
        
        $log = (is_null($message)) ? $this->log : $message->log;
        array_push($log, $stamp); unset($stamp);
    
    }

    public function send_mail() {

        global $pdo;

        if (isset($_POST['message'])):
        
            # send mail
            $sql = "INSERT INTO `messages`(`sender`, `receiver`, `cc`, `log`, `thread`, `subject`, `body`) VALUES(:sender, :receiver, :cc, :log, :thread, :title, :msg)";

            $statement = $pdo->prepare($sql);
                $statement->bindParam(":sender", $this->sender);
                $statement->bindParam(":receiver", $this->receiver);
                $statement->bindParam(":cc", serialize($this->cc));
                $statement->bindParam(":log", serialize($this->log));
                $statement->bindParam(":thread", $this->thread);
                $statement->bindParam(":title", $this->subject);
                $statement->bindParam(":msg", $this->body);
            $statement->execute();
        
        endif;
    
    }

}

?>