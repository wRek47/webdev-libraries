$("body").ready(function() {

// load config from the html
var json = $("#json").text();
var config = JSON.parse(json); delete json;

var popup_id = 0; // use the first set of tokens for the donation pop-up

    var target = config.tokens[popup_id]; // contains token expiration and html target config
    var popup = config.popup[popup_id]; // contains popup token and expiration config
    delete popup_id;

// if the popup is being directly requested, let it popup
if (window.location.hash != "") {

    if ($(window.location.hash).hasClass("modal")) { reset_token(popup.name); }

}

showPopUp(target, popup); delete target, popup;

delete config;

});

function reset_token(name) {

localStorage.removeItem(name);
return true;

}

function showPopUp(target, token_config) {

// get the current timestamp
var timestampNow = Date.now();

// define common variables
    var tracker = token_config;
    var master_token = target;

// how many milliseconds will we expire the tracker in?
var increment = [1000, 60000, 3600000, 86400000];

if (tracker.expiration.units == "seconds") { increment = increment[0]; }
if (tracker.expiration.units == "minutes") { increment = increment[1]; }
if (tracker.expiration.units == "hours") { increment = increment[2]; }
if (tracker.expiration.units == "days") { increment = increment[3]; }

var tracker_increment = tracker.expiration.duration * increment;

// handle tokens
var client_token = get_token(tracker.name);

// if there is no client token, we'll make sure there is.
if (client_token == null) { save_token(tracker.name, tracker.timestamp); client_token = get_token(tracker.name); }

// we'll also need to determine when this token expires; by adding the token start time with the increment amount
master_token.expires = client_token.started + tracker_increment;

// for testing purposes; let's 0 out the countdown and log some data
// var expiration = timestampNow - master_token.expires;

// console.log("Tracker Name: " + tracker.name);
// console.log("HTML ID: " + target.id);
// console.log("Readable Expiration: " + tracker.expiration.duration + " " + tracker.expiration.units);
// console.log("Expiration: " + expiration);
// console.log("Tracker Increment: " + tracker_increment);
// console.log("Client Token: " + client_token.started);
// console.log("Master Token: " + master_token.expires);
// console.log("Time Now: " + timestampNow);

// if it is expired, we'll show the donation modal
if (timestampNow >= master_token.expires) {

    // console.log("Did it popup?");
    // but first, let's reset the timer on the token.
    tracker.timestamp.started = timestampNow;
    save_token(tracker.name, tracker.timestamp);

    $("#" + target.id).modal("show");

}

}

function get_token(name) {

var result = localStorage.getItem(name);
return JSON.parse(result);

}

function save_token(name, value) {

localStorage.setItem(name, JSON.stringify(value));
return true;

}