<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("View Event");

$eventID = intval($_GET[eventID]);
$timeID = intval($_GET[timeID]);
$response = $_GET[response];
$queryEvent = db::select("events", "eventID = '$eventID'", "1");
$countEvent = db::count($queryEvent);

if($countEvent == 0){
	echo "<p class=\"message error\">Couldn't find the requested event.</p>";
}else{
	
	$queryInvite = db::select("eventInvites", "eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
	$countInvite = db::count($queryInvite);
	if($countInvite == 0){
		echo "<p class=\"message error\">Couldn't find the requested event or you don't have permission to access it.</p>";
	}else{
	
	$event = db::fetch_array($queryEvent);

	$queryDates = db::select("eventTimes", "timeID = '$timeID' AND eventID = '$eventID'", "1");
	$countDates = db::count($queryDates);
	
	if($countDates == 0){
		echo "<p class=\"message error\">Couldn't find the requested date.</p>";
	}else{
		$queryResponse = db::select("eventResponses", "timeID = '$timeID' AND eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
		$countResponse = db::count($queryResponse);
	
		if($countResponse == 0){
			db::insert("eventResponses", "", "NULL, '$eventID', '$timeID', '$_SESSION[uid]', '$response'");
		}else{
			$row = db::fetch_array($queryResponse);
			db::update("eventResponses", "response = '$response'", "responseID = '$row[responseID]'");
		}
		echo "<p class=\"message success\">Done! <a href=\"/view/$eventID/\">Go back?</a></p><meta http-equiv=\"refresh\" content=\"1; url=/view/$eventID/\" />";
	}
	}
}

echo core::returnFooter();

?>