<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("Create Event");

	if($_POST){
		$name = core::clean($_POST[name]);
		$description = core::clean($_POST[description]);
		$time = time();
		db::insert("events", "`eventID`, `userID`, `name`, `description`, `timestamp`", "NULL, '$_SESSION[uid]', '$name', '$description', '$time'");
		$eventID = db::insert_id();
		echo "<p class=\"message success\">Event added! <a href=\"/view/$eventID/asowner/\">Go to event view?</a></p>";
	}else{
	
		echo "<form action=\"/create/\" method=\"POST\" class=\"big\">
			
			<fieldset>
				<legend>Event Information</legend>
				<label for=\"name\">Name:*</label><input name=\"name\" type=\"text\">
				<label for=\"description\">Description:*</label><textarea cols=\"50\" rows=\"10\" name=\"description\"></textarea>
			</fieldset>
			<input name=\"submit\" value=\"Add\" type=\"submit\" class=\"submit\"/>
		</form><div class=\"clear\"> </div>";
	
	}

echo core::returnFooter();

?>