<?php

if (!class_exists("Event")): require "/path/to/event.inc"; endif;
if (!class_exists("Ballot")): require "/path/to/ballot.inc"; endif;

$poll = new Event("Label", "Description");

    $poll->voter = new Voter($profile);
    $poll->ballot = new Ballot($poll->voter);

$poll->ballot_box = new BallotBox;
$poll->ballot_box->save_ballot($poll->ballot);

?>