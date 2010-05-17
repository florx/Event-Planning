<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("View Event");

$eventID = intval($_GET[eventID]);

$queryEvent = db::select("events", "eventID = '$eventID'", "1");
$countEvent = db::count($queryEvent);

if($countEvent == 0){
	echo "<p class=\"message error\">Couldn't find the requested event or you don't have permission to access it.</p>";
}else{

	$event = db::fetch_array($queryEvent);
	
	if($event[userID] == $_SESSION[uid]){
		echo "<meta http-equiv=\"refresh\" content=\"0; url=/view/$eventID/asowner/\" /><p class=\"message notice\">You are the owner of this event. <a href=\"/view/$eventID/asowner/\">Go to the owner event page?</a></p>";
	}else{

	$queryInvite = db::select("eventInvites", "eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
	$countInvite = db::count($queryInvite);
	if($countInvite == 0){
		echo "<p class=\"message error\">Couldn't find the requested event or you don't have permission to access it.</p>";
	}else{
	
	
	$event[description] = nl2br(strip_tags($event[description], '<b><strong><em><i><img>'));
	
	echo "<h2>Viewing $event[name]</h2>";
	
	echo "<p>$event[description]</p>";
	
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
		
		$user = core::selectUser($event[userID]);
		$name = $user[name];
		
		echo "<a href=\"$user[link]\"><img src=\"http://graph.facebook.com/$event[userID]/picture\" class=\"responsepic invited\" title=\"$name (Event Owner)\" alt=\"$name (Event Owner)\" /></a>";
	}
	
	echo "<h2>Dates</h2><p>If you don't see your profile pic in the mugshots, click the icon to say whether you can attend or not!</p>";
	$queryDates = db::select("eventTimes", "eventID = '$eventID'", NULL, "startTimestamp ASC");
	$countDates = db::count($queryDates);
	
	if($countDates == 0){
		echo "<p>There are no dates assigned to this event yet!</p>";
	}else{
		while($date = db::fetch_array($queryDates)){
			$startDate = date("H:i \o\\n D jS F", $date[startTimestamp]);
			$endDate = date("H:i \o\\n D jS F", $date[endTimestamp]);
			echo "<div class=\"dotted\"> </div><strong>Start: $startDate<br />End: $endDate</strong><p>";
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
			
			echo "</p><a href=\"/edit/$eventID/date/$date[timeID]/response/Y/\"><img src=\"/images/tick.png\" /> I can attend</a> | 
			<a href=\"/edit/$eventID/date/$date[timeID]/response/N/\"><img src=\"/images/cross.png\" /> I cannot attend</a><br /> ";
		}
	}
	}
	}

}

echo core::returnFooter();

?>