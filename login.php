<?php

require("init.php");

core::requireLoggedIn();

echo core::returnHeader("Login");

echo "<p>You are now logged in! <a href=\"../\">Go Home?</a></p><meta http-equiv=\"refresh\" content=\"0; url=../\" />";

echo core::returnFooter();

?>