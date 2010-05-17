<?php

require("init.php");

echo core::returnHeader("Home");

echo "<p>Welcome! This site will allow you to plan events with your friends. Simply create an event, select dates on which this event could occur (provide as many options as you want) and then invite your friends. They will then be allowed to vote on all of the dates to say <em>'Yes! I can attend this date'</em>, or <em>'This date is not possible for me'</em>, that way you can find the right date for everybody!</p>";

if($_SESSION[loggedin] == TRUE){

	echo "<p><a href=\"create/\">Create an event?</a></p>";

	echo "<h2>My Invites</h2><p>";

	$queryInvites = db::select("eventInvites", "userID = '$_SESSION[uid]'");
	$countInvites = db::count($queryInvites);

	if($countInvites == 0){
		echo "You currently have no events that you are invited to!";
	}else{
		while($invite = db::fetch_array($queryInvites)){
			$queryEvent = db::select("events", "eventID = '$invite[eventID]'", "1");
			$event = db::fetch_array($queryEvent);
			echo "<a href=\"view/$event[eventID]/\">$event[name]</a><br />";
		}
	}
	
	echo "</p><h2>My Events</h2><p>";

	$queryEvents = db::select("events", "userID = '$_SESSION[uid]'");
	$countEvents = db::count($queryEvents);

	if($countEvents == 0){
		echo "You currently have no events! <a href=\"create/\">Create one?</a>";
	}else{
		while($event = db::fetch_array($queryEvents)){
			echo "<a href=\"view/$event[eventID]/asowner/\">$event[name]</a><br />";
		}
	}

	echo "</p>";

}else{

	echo "<p><a href=\"login/\">Login to get started or view your events/invites!</a></a></p>";

}

echo core::returnFooter();

?>