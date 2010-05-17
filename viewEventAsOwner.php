<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("View Your Event");

$eventID = intval($_GET[eventID]);

$queryEvent = db::select("events", "eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
$countEvent = db::count($queryEvent);

if($countEvent == 0){
	echo "<p class=\"message error\">Couldn't find the requested event. Either that or you don't own the event. Try <a href=\"/view/$eventID/\">here instead?</a></p>";
}else{
	
	$event = db::fetch_array($queryEvent);
	
	echo "<h2>Viewing $event[name]</h2>";

	echo "<p><a href=\"/edit/$eventID/\">Edit the event?</a> or <a href=\"/edit/$eventID/date/new/\">Add dates?</a> or <a href=\"/edit/$eventID/invite/\">Invite friends?</a></p>";
	
	echo "<h2>Guest List</h2>";
	
	$queryInvites = db::select("eventInvites", "eventID = '$eventID'", NULL, "userID ASC");
	$countInvites = db::count($queryInvites);
	
	if($countInvites == 0){
		echo "<p>There are no invites people in this event! <a href=\"/edit/$eventID/invite/\">Invite people?</a></p>";
	}else{
		while($invite = db::fetch_array($queryInvites)){
			
			$uid = $invite[userID];
			$user = core::selectUser($uid);
			$name = $user[name];
					
			echo "<a href=\"$user[link]\"><img src=\"http://graph.facebook.com/$uid/picture\" class=\"responsepic invited\" title=\"$name\" alt=\"$name\" /></a>";
			
			
		}
		
		echo "<img src=\"http://graph.facebook.com/$_SESSION[uid]/picture\" class=\"responsepic invited\" title=\"You\" alt=\"You\" />";
	}
	
	echo "<h2>Dates</h2>";
	
	$queryDates = db::select("eventTimes", "eventID = '$eventID'", NULL, "startTimestamp ASC");
	$countDates = db::count($queryDates);
	
	if($countDates == 0){
		echo "<p>There are no dates assigned to this event! <a href=\"/edit/$eventID/date/new/\">Add dates?</a></p>";
	}else{
	
		
	
		while($date = db::fetch_array($queryDates)){
			$startDate = date("H:i \o\\n D jS F", $date[startTimestamp]);
			$endDate = date("H:i \o\\n D jS F", $date[endTimestamp]);
			echo "<div class=\"dotted\"> </div><strong>Start: $startDate<br />End: $endDate</strong> <a href=\"/edit/$eventID/date/$date[timeID]/\"><img src=\"/images/edit.png\" /></a><p>";
			$queryResponses = db::select("eventResponses", "eventID = '$eventID' AND timeID = '$date[timeID]'", NULL, "response ASC, userID ASC");
			$countResponses = db::count($queryResponses);
			
			if($countResponses == 0){
				echo "Nobody has replied to this date yet.";
			}else{
				while($response = db::fetch_array($queryResponses)){
					if($response[response] == "Y"){
						$imgclass = "attending";
					}else{
						$imgclass = "notattending";
					}
					
					$uid = $response[userID];
					$user = core::selectUser($uid);
					$name = $user[name];
					
					echo "<img src=\"http://graph.facebook.com/$uid/picture\" class=\"responsepic $imgclass\" title=\"$name\" alt=\"$name\" />";
				}
			}
			echo "</p>";
		}
	}

}

echo core::returnFooter();

?>