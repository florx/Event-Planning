<?php

class core{

	/*
		function init
		
		Inputs: None
		Return: None
		Description: Runs at every page load. Connects to the database, and runs the login check script.
		
	*/
	public function init(){
		global $settings;
		
		db::connect($settings['db']['server'], $settings['db']['username'],
			$settings['db']['password']);
		//Pass the settings variables to the connect function to connect to the MySQL database
		db::select_db($settings['db']['name']);
		//Then pass the database name to select which database to use.
		
		if($_SESSION[userCacheExpire] < time()){
			$_SESSION[userCacheExpire] = time() + (60*10);
			$_SESSION[userCache] = array();
		} //Expire the userCache after 10 minutes.
		
	}
	

	
	public function selectUser($uid){
		if(isset($_SESSION[userCache][$uid])){
			return $_SESSION[userCache][$uid];
		}else{
			$queryUser = db::select("users", "id = '$uid'", "1");
			$countUser = db::count($queryUser);

			if($countUser == 0){
				$cookie = core::get_facebook_cookie();
				if($cookie){
					$user = json_decode(file_get_contents(
						"https://graph.facebook.com/$uid?access_token=" . $cookie['access_token']));
					if($user->name != ""){
						db::insert("users", "`id`, `name`, `first_name`, `last_name`, `email`, `link`, `updated_time`", "'$user->id', '$user->name', '$user->first_name', '$user->last_name', '$user->email', '$user->link', '0'");
						$queryUser = db::select("users", "id = '$uid'", "1");
						$data = db::fetch_array($queryUser);
						$_SESSION[userCache][$uid] = $data;
						return $data;
					}
				}
				$error[name] = "Facebook User";
				$_SESSION[userCache][$uid] = $error;
				return $error;
			}else{
				$data = db::fetch_array($queryUser);
				$_SESSION[userCache][$uid] = $data;
				return $data;
			}
		}
	}
	
	public function returnHeader($pagetitle, $bodytitle = ""){
		if($bodytitle == ""){
			$bodytitle = $pagetitle;
		}
		//<script type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js\"></script> 
		return "<!DOCTYPE html> 
<html> 
	<head> 
		<title>$pagetitle - Event Planning</title> 
		
		<link href=\"http://event.florxlabs.com/style.css\" media=\"screen\" rel=\"stylesheet\" type=\"text/css\"/> 		
	</head> 
	<body> 
		<div id=\"wrapper\"> 
			<h1>Event Planning - $bodytitle</h1> ";
		
	}
	
	public function returnFooter(){
		global $settings;
		$app_id = $settings['fb']['app_id'];
		$select_queries = $settings['stats']['select_queries'];
		return "<div id=\"footer\">Copyright &copy 2010. $select_queries queries executed.</div> 
		</div> 
		 <div id=\"fb-root\"></div>
    <script src=\"http://connect.facebook.net/en_US/all.js\"></script>
    <script>
      FB.init({appId: '$app_id', status: true,
               cookie: true, xfbml: true});
      FB.Event.subscribe('auth.login', function(response) {
        window.location.reload();
      });
    </script>
	</body> 
</html>";
		
	}
	

	
	public function requireLoggedIn(){
		$cookie = core::get_facebook_cookie();
		if($cookie){
		
			//echo core::returnHeader("Already logged in");
			//print_r($cookie);
			
			$_SESSION[uid] = $cookie[uid];
		
			if($_SESSION[loggedin] == FALSE){
				$_SESSION[loggedin] = TRUE;
				
				$user = json_decode(file_get_contents(
					'https://graph.facebook.com/me?access_token=' . $cookie['access_token']));
				$updatedtime = strtotime($user->updated_time);
				
				//print_r($user);
				
				$query = db::select("users", "id = '$cookie[uid]'", "1");
				$count = db::count($query);
				
				if($count == 0){
					//New user
					//echo "Hi there $user->first_name, you are new around these parts!";
					
					db::insert("users", "`id`, `name`, `first_name`, `last_name`, `email`, `link`, `updated_time`", "'$user->id', '$user->name', '$user->first_name', '$user->last_name', '$user->email', '$user->link', '$updatedtime'");
				}else{
					//Existing user
					//echo "Welcome back, $user->first_name!";
					
					$userrow = db::fetch_array($query);
					if($userrow[updated_time] < $updatedtime){
						db::update("users", "name = '$user->name'", "id = '$cookie[uid]'");
						db::update("users", "first_name = '$user->first_name'", "id = '$cookie[uid]'");
						db::update("users", "last_name = '$user->last_name'", "id = '$cookie[uid]'");
						db::update("users", "email = '$user->email'", "id = '$cookie[uid]'");
						db::update("users", "link = '$user->link'", "id = '$cookie[uid]'");
						db::update("users", "updated_time = '$updatedtime'", "id = '$cookie[uid]'");
					}
				}
			
			}
			
			//echo core::returnFooter();
			
		}else{
			$_SESSION[loggedin] = FALSE;
			echo core::returnHeader("Login");
			echo "<p>Hi there! We are using Facebook Connect in order to let you login. This is currently the only way to login. If you don't have a facebook account... <strong>tough</strong>. Get one!</p><fb:login-button perms=\"read_friendlists,email\"></fb:login-button>";
			echo core::returnFooter();
			exit;
			//Oops, this person needs to login!
		}
	}
	
	public function get_facebook_cookie() {
		global $settings;
		$app_id = $settings['fb']['app_id'];
		$application_secret = $settings['fb']['app_secret'];
		
		$args = array();
		parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
		ksort($args);
		$payload = '';
		
		foreach ($args as $key => $value) {
			if ($key != 'sig') {
				$payload .= $key . '=' . $value;
			}
		}
		
		if (md5($payload . $application_secret) != $args['sig']) {
			return null;
		}
		
		return $args;
	}

	
	/*
		function clean
		
		Inputs: $str (string) string to clean
		Return: (string) the clean string
		Description: A simple function to get data ready to be manipulated or inserted into the database.
		
	*/
	public function clean($str){
		
		$str = trim(addslashes($str));
		//Make the string save to put in the database by taking off any spaces on the start or end of the string and adding slashes to it. Slashes are used to escape ' or " which can cause SQL Injection attacks if not escaped by changing ' and " to \' and \"
		return $str;
	}
	
	/*
		function error
		
		Inputs: $error (int) the ID of the error, $extra (string) any other information for debugging.
		Return: None
		Description: Makes nice looking errors when something goes wrong.
		
	*/
	public function error($error, $extra = "")
    {
		$errors = array(
			"1" => "Couldn't connect to the MySQL server, or you have the wrong information.",
            "2" => "Couldn't connect to the MySQL database.", 
			"3" => "Couldn't process your query.", 
			"4" => "Couldn't SELECT from the table.", 
			"5" => "Couldn't INSERT into the table.",
			"6" => "Couldn't UPDATE the row.",
			"7" => "Couldn't COUNT the rows.",
			"8" => "Couldn't FETCH_ARRAY from the table.",
			"403" => "You do not have access to this page. Please contact the administrator if you require access.",
			"404" => "The {$extra} you requested could not be found, or it doesn't exist."
		);
		//This holds the text array of the error numbers passed from various places in the scripts.
			
		if($error <= 8){
			$title = "MySQL server error";
		}elseif($error == 403){
			$title = "Access Denied";
		}elseif($error == 404){
			$title = ucfirst($extra) . " Not Found";
		}else{
			$title = "General Error";
		}
		//Pick an error title based on the error number.
		
		echo "<h2>$title</h2>\n<p></p>\n<p>$errors[$error]</p>" . mysql_error();
		exit;
		//Show the error and a mysql error string if applicable and then stop the program running immediately.
		
    }  

	
	/*
		function getHowLongAgo
		
		Inputs: $date (int) the timestamp to be converted.
		Return: (string) the nice looking date e.g. Today, 6:53 pm
		Description: Turns timestamps into human readable time, and uses the keyword Today and Yesterday if necessary.
		
	*/
	public function getHowLongAgo($date){
		$odate = $date;
		$date = $date - 3600; //Get yesterdays date
		$date = getdate($date);
		
		$now = getdate();
		
		if($date[year] == $now[year]){ //Check if the years are the same
			if($date[mon] == $now[mon]){ //Check if the months are the same
				if($date[mday] == $now[mday]){ //Check if the date is the same
					$prefix = "Today";
				}elseif($date[mday] == ($now[mday] - 1)){ //Check if the date is yesterday
					$prefix = "Yesterday";
				}
			}	
		}
		
		if($prefix == ""){
			$prefix = date('j/n/Y', $odate); //If not today or yesterday then show a date DD/YY/MMMM
		}
		
		return $prefix . ", " . date('h:i a', $odate); //Add HH:MM AM/PM onto the end.
	}
	
	

}

?>