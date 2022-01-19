<?php

/*

Metaverse Profiler
v0.0.1
1/18/2022

Upgrading the profiler to incorporate economic representations could be a win-win
for me evaluating and bettering aspects of my lifestyles and behaviours.

*/

class Profile {

    public $credentials;
    public $economics;
    public $portfolio;
    public $stamps;

}

class Economy {

    public $title;
    public $description;

    public $roles;
    public $classes;

    public $lows;
    public $averages;
    public $highs;

    public $accumulated;
    public $dispersed;
    public $qualified;
    public $disqualified;

    public $population;
    public $calendar;

}

class Economicus {

    public $economy;
    public $role;

    public $class;
    public $tenure;

    public $labor;
    public $purchases;
    public $sales;
    public $popularity;
    
    public $credits;
    public $debts;
    public $payments;

}

class Credentials {

    public $handle;
    public $token;

    public $owner = false;
    public $new_user = true;

    public $user = true;
    public $assistant = false;
    public $moderator = false;
    public $super = false;
    public $operator = false;

    public $new_customer = false;
    public $customer = false;
    public $community = false;

    public $new_hire = false;
    public $employee = false;
    public $manager = false;
    public $director = false;
    public $officer = false;

    public $developer = false;

}

class Portfolio {

    public $name = "Guest";
    public $birthdate;
    public $geolocation;

    public $emails;
    public $phones;
    public $mailboxes;

    public $microsoft;
    public $google;
    public $apple;
    public $facebook;
    public $twitter;
    public $reddit;
    public $tumblr;

    public $activities;
    public $education;
    public $employment;
    public $goals;

    public $autobiographies;
    public $credibility;
    public $schedules;
    public $hygiene;
    public $finances;

}

?>