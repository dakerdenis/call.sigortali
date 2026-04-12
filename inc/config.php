<?php
define('BASE_URL', '/call');
error_reporting(E_ALL);
ini_set("display_errors", 0);

ini_set("memory_limit", -1);

if(!isset($_SESSION)) { 
    session_start();
}

$db_hostname = "localhost";
$db_name = "sigommsy_call";
$db_user = "callsig";
$db_password = "CallSig_2026!";


$db = mysqli_connect($db_hostname, $db_user, $db_password, $db_name);
mysqli_set_charset($db,"utf8");
//mysqli_query($db, "SET GLOBAL sql_mode = ''");

$connect = new PDO("mysql:host=$db_hostname; dbname=$db_name; charset=utf8", $db_user, $db_password);

$user_id = $_SESSION['id'];

mysqli_query($db, "UPDATE users SET lastseen = NOW() WHERE id = '$user_id'");

//lang

    if(empty($_COOKIE['lang'])){
        setcookie("lang", "Azərbaycanca", time() + (86400 * 30), '/call/');
        $_COOKIE['lang'] = "Azərbaycanca";
    }

    if(!empty($_GET['lang'])){
        setcookie("lang", $_GET['lang'], time() + (86400 * 30), '/call/');
        $_COOKIE['lang'] = $_GET['lang'];
    }

    $lang = $_COOKIE['lang'];

//lang

//include_once('settings.php');
include_once('functions.php');

$user = $_SESSION['login'];
$session_id = $_SESSION['id'];
$res = mysqli_query($db, "SELECT * FROM users WHERE id = '$session_id' AND deletedby = 0 AND groupId != 0 ");
$user_data = mysqli_fetch_array($res);

$user_id = $user_data['id'];
$userGroup = $user_data['groupId'];
$access_company = $user_data['access_company'];
$defaultCompany = $user_data['defaultCompany'];

$selectedCompany = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM companies WHERE id = '$defaultCompany'"));

if(empty($user_data['attachment'])){
    $user_data['attachment'] = "noimage.png";
}

if(!empty($user_data['mainPage'])){
  $userMainPage = $user_data['mainPage'];
} else{
  $userMainPage = "home.php";
}


// $sqlGetGroups = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM usergroups WHERE id = '$userGroup'"));
// $access = json_decode($sqlGetGroups['access'], true);

// foreach($access as $key => $accessObject){
//     $access[$accessObject['module']] = $access[$key];
// }

?>
