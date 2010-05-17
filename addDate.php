<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("Add Date");

$eventID = intval($_GET[eventID]);
$queryEvent = db::select("events", "eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
$countEvent = db::count($queryEvent);

if($countEvent == 0){
	echo "<p class=\"message error\">Couldn't find the requested event or you don't have permission to add dates to it.</p>";
}else{
	$event = db::fetch_array($queryEvent);
	
	echo "<h2>Adding a date to $event[name]</h2>";
	
	if($_POST){
		$startTime = core::clean($_POST[startTime]);
		$endTime = core::clean($_POST[endTime]);
		$startDate = core::clean($_POST[startDate]);
		$endDate = core::clean($_POST[endDate]);
		
		//print_r($_POST);
		//echo "s $startTime $startDate. e $endTime $endDate";
		
		$startTimestamp = strtotime($startTime . " " . $startDate);
		$endTimestamp = strtotime($endTime . " " . $endDate);
		
		if($endTimestamp < $startTimestamp){
			echo "<p class=\"message error\">End time was before the start time! Press back and try again!</p>";
		}elseif(!$endTimestamp || !$startTimestamp){
			echo "<p class=\"message error\">Dates/times were in the wrong format!</p>";
		}else{
			db::insert("eventTimes", "", "NULL, '$eventID', '$startTimestamp', '$endTimestamp'");
		
			echo "<p class=\"message success\">Date added! <a href=\"/view/$eventID/asowner/\">Go back to event view?</a> or <a href=\"/edit/$eventID/date/new/\">add another?</a></p>";
		}
	}else{
	
		echo "<form action=\"/edit/$eventID/date/new/\" method=\"POST\" class=\"big\">
			<fieldset>
				<legend>Date Information</legend>
				<label for=\"startTime\">Start Time:*</label><input name=\"startTime\" type=\"text\"> <div class=\"input\">(HH:MM)</div>
				<label for=\"startDate\">Start Date:*</label><input name=\"startDate\" type=\"text\" value=\"(MM/DD/YYYY)\"><div class=\"input\">(MM/DD/YYYY)</div>
				<label for=\"endTime\">End Time:*</label><input name=\"endTime\" type=\"text\"><div class=\"input\">(HH:MM)</div>
				<label for=\"endDate\">End Date:*</label><input name=\"endDate\" type=\"text\" value=\"(MM/DD/YYYY)\"><div class=\"input\">(MM/DD/YYYY)</div>
			</fieldset>
			<input name=\"submit\" value=\"Add\" type=\"submit\" class=\"submit\"/>
		</form><div class=\"clear\"> </div>";
	
	}
	

}

echo core::returnFooter();

?>