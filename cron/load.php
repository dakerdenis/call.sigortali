<?php

ini_set("memory_limit", -1);

error_reporting(E_ALL);
ini_set("display_errors", 0);

$db_hostname = "127.0.0.1";
$db_name = "sigommsy_call";
$db_user = "sigommsy_call";
$db_password = "sigortali-123";

$db = mysqli_connect($db_hostname, $db_user, $db_password, $db_name);
mysqli_set_charset($db,"utf8");
//mysqli_query($db, "SET GLOBAL sql_mode = ''");

$connect = new PDO("mysql:host=$db_hostname; dbname=$db_name; charset=utf8", $db_user, $db_password);

$getSettings_startCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'startCustomer'"));
$getSettings_limitCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'limitCustomer'"));
$getSettings_minprice = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'minprice'"));
$getSettings_maxprice = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'maxprice'"));

$startCustomer = $getSettings_startCustomer['setting_value'];
$limitCustomer = $getSettings_limitCustomer['setting_value'];

$today = date("Y-m-d");

$start_date = date('Y-m-d', strtotime($today. ' + '.$startCustomer.' days'));
$end_date = date('Y-m-d', strtotime($start_date. ' + '.$limitCustomer.' days'));

$minprice = $getSettings_minprice['setting_value'];
$maxprice = $getSettings_maxprice['setting_value'];

$truncateOld = mysqli_query($db, "TRUNCATE table customers_temp");
//$queryForUpdate = mysqli_query($db, "INSERT INTO customers_temp (id, identification, company, end_date, pin, pin_serial, serial, name, car_id, make, model, car_pin, type, engineType, premium, bm, phone, valuesp, note, createdby, created) SELECT id, identification, company, end_date, pin, pin_serial, serial, name, car_id, make, model, car_pin, type, engineType, premium, bm, phone, valuesp, note, createdby, created FROM customers WHERE LENGTH(pin) != 10 AND DATE_FORMAT(end_date, '%Y-%m-%d') > '$start_date' AND DATE_FORMAT(end_date, '%Y-%m-%d') < '$end_date' AND valuesp >= '$minprice' AND valuesp <= '$maxprice' AND car_id NOT IN (SELECT car_id FROM call_status) ORDER by id DESC");
$queryForUpdate = mysqli_query($db, "INSERT INTO customers_temp (id, identification, company, end_date, pin, pin_serial, serial, name, car_id, make, model, car_pin, type, engineType, premium, bm, phone, valuesp, note, createdby, created) SELECT id, identification, company, end_date, pin, pin_serial, serial, name, car_id, make, model, car_pin, type, engineType, premium, bm, phone, valuesp, note, createdby, created FROM customers WHERE LENGTH(pin) != 10 AND DATE_FORMAT(end_date, '%Y-%m-%d') > '$start_date' AND DATE_FORMAT(end_date, '%Y-%m-%d') < '$end_date' AND valuesp >= '$minprice' AND valuesp <= '$maxprice' ORDER by id DESC");

$query = "SELECT * FROM customers WHERE DATE_FORMAT(end_date, '%Y-%m-%d') > '$start_date' AND DATE_FORMAT(end_date, '%Y-%m-%d') < '$end_date' AND valuesp >= '$minprice' AND valuesp <= '$maxprice' AND car_id NOT IN (SELECT car_id FROM call_status) ORDER by id DESC";
echo $query;


?>