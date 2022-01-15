<?php

if (!class_exists("NewsletterFeed")): require "/path/to/feed.inc"; endif;
if (!class_exists("Subscriber")): require "/path/to/subscriber.inc"; endif;

class Newsletter {

    public $title;
    public $meta;
    public $subscribers;

}

?>