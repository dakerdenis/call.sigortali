<?php

session_start();

unset($_SESSION['login']);
unset($_SESSION['id']);

setcookie("login", "", time()-3600,"/");
setcookie("id", "", time()-3600,"/");

$_SESSION = array();
session_destroy();

header("location: /call/");

?>