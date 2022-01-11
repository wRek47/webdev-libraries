<?php

require_once "event.inc";

class Events {

    public $ledger;

    public function add_event($event, $target) {
    
        $event->target = $target;
        array_push($this->ledger, $event);
    
    }

    public function get_events($target) {
    
        $result = array();

        foreach ($this->ledger as $event):
        
            if ($target == $event->target):
            
                array_push($result, $event);
            
            endif;
        
        endforeach; unset($event);

        if (count($result) == 1): $result = reset($result); endif;
        return $result;
    
    }

}

?>