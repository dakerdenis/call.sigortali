<?php

if(isset($_SESSION['login']) && isset($_SESSION['id'])){

    if ($_GET['type'] == 1){ // customers

        $car_id = htmlspecialchars(trim($_POST['car_id']));
        $car_pin = htmlspecialchars(trim($_POST['car_pin']));

        $getItem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM customers WHERE car_id = '$car_id' AND car_pin = '$car_pin'"));


        $data = array('name' => $getItem['name'], 'phone' => $getItem['phone'], 'pin' => $getItem['pin'], 'pin_serial' => $getItem['pin_serial'], 'serial' => $getItem['serial'], 'end_date' => $getItem['end_date'], 'premium' => $getItem['premium'], 'bm' => $getItem['bm'], 'valuesp' => $getItem['valuesp'], 'note' => $getItem['note']);
        $data = json_encode($data);
    
        echo $data;

    }

}

?>