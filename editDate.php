<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("Add Date");

$eventID = intval($_GET[eventID]);
$timeID = intval($_GET[timeID]);
$queryEvent = db::select("events", "eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
$countEvent = db::count($queryEvent);

if($countEvent == 0){
	echo "<p class=\"message error\">Couldn't find the requested event or you don't have permission to edit dates in it.</p>";
}else{
	$event = db::fetch_array($queryEvent);
	
	echo "<h2>Editing date in $event[name]</h2>";
	
	$queryDate = db::select("eventTimes", "eventID = '$eventID' AND timeID = '$timeID'", "1");
	$countDate = db::count($queryDate);

	if($countDate == 0){
		echo "<p class=\"message error\">Couldn't find the requested date or you don't have permission to edit it.</p>";
	}else{
		$date = db::fetch_array($queryDate);
	
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
				db::update("eventTimes", "startTimestamp = '$startTimestamp'", "timeID = '$timeID'");
				db::update("eventTimes", "endTimestamp = '$endTimestamp'", "timeID = '$timeID'");
			
				echo "<p class=\"message success\">Date edited! <a href=\"/view/$eventID/asowner/\">Go back to event view?</a></p>";
			}
		}else{
		
			$startTime = date("H:i", $date[startTimestamp]);
			$endTime = date("H:i", $date[endTimestamp]);
			$startDate = date("m/d/y", $date[startTimestamp]);
			$endDate = date("m/d/y", $date[endTimestamp]);
		
			echo "<form action=\"/edit/$eventID/date/$timeID/\" method=\"POST\" class=\"big\">
				<fieldset>
					<legend>Date Information</legend>
					<label for=\"startTime\">Start Time:*</label><input name=\"startTime\" type=\"text\" value=\"$startTime\"> <div class=\"input\">(HH:MM)</div>
					<label for=\"startDate\">Start Date:*</label><input name=\"startDate\" type=\"text\" value=\"$startDate\"><div class=\"input\">(MM/DD/YYYY)</div>
					<label for=\"endTime\">End Time:*</label><input name=\"endTime\" type=\"text\" value=\"$endTime\"><div class=\"input\">(HH:MM)</div>
					<label for=\"endDate\">End Date:*</label><input name=\"endDate\" type=\"text\" value=\"$endDate\"><div class=\"input\">(MM/DD/YYYY)</div>
				</fieldset>
				<input name=\"submit\" value=\"Edit\" type=\"submit\" class=\"submit\"/>
			</form><div class=\"clear\"> </div>";
		
		}
	}

}

echo core::returnFooter();

?>