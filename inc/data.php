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
    if ($_GET['type'] == 2){ // search identification
        $q = mysqli_real_escape_string($db, trim($_POST['q']));
        if(strlen($q) >= 2) {
            $sql = mysqli_query($db, "SELECT identification, car_id, name FROM customers WHERE identification LIKE '%$q%' ORDER BY identification ASC LIMIT 10");
            $results = [];
            while($row = mysqli_fetch_array($sql)) {
                $results[] = [
                    'identification' => $row['identification'],
                    'car_id' => $row['car_id'],
                    'name' => $row['name']
                ];
            }
            echo json_encode($results);
        } else {
            echo '[]';
        }
    }
}
?>