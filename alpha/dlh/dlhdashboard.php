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

    public function __construct() { }

    public function login() { }
    public function logout() { }

    public function get_users() { }
    public function get_profile() { }

    public function get_activities() { }
    public function get_hobby() { }
    public function get_hobby_blog() { }

    public function get_workspace() { }
    public function get_docket() { }
    public function get_showcase() { }
    public function get_toolbox() { }

    public function get_stats() { }

}

?>