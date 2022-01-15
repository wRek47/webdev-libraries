<?php

require "/path/to/events/event.inc";
require "/path/to/article.inc";

class Blog {

    public $title;
    public $articles;

    public $articles_per_page;
    public $comments_per_page;

    public $allow_comments;
    public $guests_can_comment;

    public $networks;

}

?>