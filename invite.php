<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("Invite Friends to Event");

$eventID = intval($_GET[eventID]);
$queryEvent = db::select("events", "eventID = '$eventID' AND userID = '$_SESSION[uid]'", "1");
$countEvent = db::count($queryEvent);

if($countEvent == 0){
	echo "<p class=\"message error\">Couldn't find the requested event or you do not have permission to invite friends to it.</p>";
}else{
	$event = db::fetch_array($queryEvent);
	
	if($_POST){
		echo "<p class=\"message success\">Invites Sent!</p>";
		foreach($_POST[ids] as $id){
			$query = db::select("eventInvites", "eventID = '$eventID' AND userID = '$id'", "1");
			$count = db::count($query);
			if($count == 0){
				db::insert("eventInvites", "", "NULL, '$eventID', '$id'");
			}
		}
	}else{
	
	echo "<fb:serverFbml>
    <script type=\"text/fbml\">
      <fb:fbml>
          <fb:request-form
                    action=\"http://event.florxlabs.com/edit/1/invite/\"
                    method=\"POST\"
                    invite=\"true\"
                    type=\"event planning\"
                    content=\"You have been invited to an event planning system. Click the button to view the event! 
                 <fb:req-choice url='http://event.florxlabs.com/view/1/'
                       label='View Event'></fb:req-choice>
              \"
              >
 
                    <fb:multi-friend-selector
                    showborder=\"false\"
                    actiontext=\"Invite your friends to this event.\"></fb:multi-friend-selector>
        </fb:request-form>

      </fb:fbml>
 
    </script>
  </fb:serverFbml>";
  }
}

echo core::returnFooter();

?>