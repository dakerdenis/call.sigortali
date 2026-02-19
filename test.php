<?php

$db_hostname = "94.73.148.32";
$db_name = "u1705130_callc";
$db_user = "u1705130_callc";
$db_password = "VSigortali4306.";


$db = mysqli_connect($db_hostname, $db_user, $db_password, $db_name);
mysqli_set_charset($db,"utf8");
mysqli_query($db, "SET GLOBAL sql_mode = ''");


$sql = mysqli_query($db, "SELECT * FROM payments WHERE orderId != '' AND teslimId = 0 AND category = 'Transfer'");
while($row = mysqli_fetch_array($sql)){
    
    echo $row['orderId'];
    
    $title = "Gündəlik Hesabat - ".date("d.m.Y", strtotime($row['paydate']))." - ".$row['fromAccount'];
    $accountId = $row['fromAccount'];
    $toAccount = $row['toAccount'];
    
    // $sqlin = 'INSERT INTO orders ( title, accountId, toAccount ) VALUES ( "'.$title.'", "'.$accountId.'", "'.$toAccount.'" )';
    //     $queryin = mysqli_query($db, $sqlin);
    //     if($queryin){

    //         echo 1;

    //     } else {
    //         echo "Error";
    //     }
    
    
    $sqlup = " UPDATE payments SET teslimId = '3' WHERE id = ".$row['id'];
        if(!mysqli_query($db, $sqlup)){
            echo mysqli_error($db);
        } else {
            //$response = 1;
        }
    
    echo "<hr>";
    
}

?>