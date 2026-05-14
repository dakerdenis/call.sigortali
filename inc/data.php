<?php
if(isset($_SESSION['login']) && isset($_SESSION['id'])){
    if ($_GET['type'] == 1){ // customers
        $car_id = htmlspecialchars(trim($_POST['car_id']));
        $car_pin = htmlspecialchars(trim($_POST['car_pin']));
        $getItem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM customers WHERE car_id = '$car_id' AND car_pin = '$car_pin'"));
        $data = array('name' => $getItem['name'], 'phone' => $getItem['phone'], 'pin' => $getItem['pin'], 'pin_serial' => $getItem['pin_serial'], 'serial' => $getItem['serial'], 'end_date' => $getItem['end_date'], 'premium' => $getItem['premium'], 'bm' => $getItem['bm'], 'valuesp' => $getItem['valuesp'], 'note' => $getItem['note']);
        echo json_encode($data);
    } else if ($_GET['type'] == 2){ // search identification
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
    } else if ($_GET['type'] == 20) { // payments2 summary
        $where = "WHERE deletedby = 0";
        if (!empty($_POST['date_from'])) {
            $df = mysqli_real_escape_string($db, $_POST['date_from']);
            $where .= " AND paydate >= '$df'";
        }
        if (!empty($_POST['date_to'])) {
            $dt = mysqli_real_escape_string($db, $_POST['date_to']);
            $where .= " AND paydate <= '$dt'";
        }

        $inc = mysqli_fetch_array(mysqli_query($db, "SELECT COALESCE(SUM(amount),0) as s FROM payments2 $where AND effect = 1"));
        $exp = mysqli_fetch_array(mysqli_query($db, "SELECT COALESCE(SUM(amount),0) as s FROM payments2 $where AND effect = -1"));

        echo json_encode([
            'income'  => $inc['s'],
            'expense' => $exp['s'],
            'balance' => $inc['s'] - $exp['s']
        ]);
    }
        else if ($_GET['type'] == 21) { // search car_id (DQN)
        $q = mysqli_real_escape_string($db, trim($_POST['q']));
        if (strlen($q) >= 2) {
            $sql = mysqli_query($db, "SELECT DISTINCT car_id, name, pin FROM customers WHERE car_id LIKE '%$q%' ORDER BY car_id ASC LIMIT 10");
            $results = [];
            while ($row = mysqli_fetch_array($sql)) {
                $results[] = ['car_id' => $row['car_id'], 'name' => $row['name'], 'pin' => $row['pin']];
            }
            echo json_encode($results);
        } else { echo '[]'; }
    }
    else if ($_GET['type'] == 22) { // search FIN/VOEN (pin)
        $q = mysqli_real_escape_string($db, trim($_POST['q']));
        if (strlen($q) >= 2) {
            $sql = mysqli_query($db, "SELECT DISTINCT pin, name, car_id FROM customers WHERE pin LIKE '%$q%' ORDER BY pin ASC LIMIT 10");
            $results = [];
            while ($row = mysqli_fetch_array($sql)) {
                $results[] = ['pin' => $row['pin'], 'name' => $row['name'], 'car_id' => $row['car_id']];
            }
            echo json_encode($results);
        } else { echo '[]'; }
    }
}
?>