<?php

require "/path/to/photo-album/image.inc";
require "/path/to/developer/forms/security.inc";

class Article {

    public $board;
    public $allow_comments;
    public $published;

    public $author;
    
    public $subject;
    public $description;
    public $body;
    public $images;

    public $keywords;

    public $synergy;

    public $comments;
    public $shared;

    public $stamps;

    public function disable_comments() { }

    public function post_to_blog($visibility) {
    
        if (isset($_POST['save'])):
        
            unset($_POST['save']);

            $secure = new SecureInput;

            if (isset($_POST['board'])): $board = $secure->post("board"); endif;
            if (isset($_POST['subject'])): $subject = $secure->post("subject"); endif;
            if (isset($_POST['description'])): $description = $secure->post("description"); endif;
            if (isset($_POST['body'])): $body = $secure->post("body"); endif;
            if (isset($_POST['keywords'])): $keywords = $secure->post("keywords"); endif;
            if (isset($_POST['allow_comments'])): $allow_comments = $secure->post("allow_comments"); endif;
            if (isset($_POST['facebook'])): $facebook = $secure->post("facebook"); endif;
        
        endif;
    
    }
    public function post_to_facebook($visibility) { }

    public function post_comment() { }
    public function share_article() { }

}

?>