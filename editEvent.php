<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("Edit Event");

$eventID = intval($_GET[eventID]);
$queryEvent = db::select("events", "eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
$countEvent = db::count($queryEvent);

if($countEvent == 0){
	echo "<p class=\"message error\">Couldn't find the requested event, or you don't have permission to edit it!</p>";
}else{
	$event = db::fetch_array($queryEvent);
	
	echo "<h2>Editing $event[name]</h2>";
	
	if($_POST){
		$name = core::clean($_POST[name]);
		$description = core::clean($_POST[description]);
		db::update("events", "name = '$name'", "eventID = '$eventID'");
		db::update("events", "description = '$description'", "eventID = '$eventID'");
		echo "<p class=\"message success\">Event edited! <a href=\"/view/$eventID/asowner/\">Go back to event view?</a></p>";
	}else{
	
		echo "<form action=\"/edit/$eventID/\" method=\"POST\" class=\"big\">
			
			<p>Did you want to edit the dates? Go back to the event view page and click on the edit button next to the dates!</p>
			<p>Did you want to invite more people? <a href=\"/edit/$eventID/invite/\">Click here</a></p>
			<fieldset>
				<legend>Event Information</legend>
				<label for=\"name\">Name:*</label><input name=\"name\" value=\"$event[name]\" type=\"text\">
				<label for=\"description\">Description:*</label><textarea cols=\"50\" rows=\"10\" name=\"description\">$event[description]</textarea>
			</fieldset>
			<input name=\"submit\" value=\"Edit\" type=\"submit\" class=\"submit\"/>
		</form><div class=\"clear\"> </div>";
	
	}
}

echo core::returnFooter();

?>