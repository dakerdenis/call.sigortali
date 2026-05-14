<?php
error_reporting(E_ALL);
ini_set("display_errors", 0);
if (isset($_SESSION['login']) && isset($_SESSION['id'])) {
    if ($_GET['type'] == 1) { // inline update
        $rejectIt = array("id", "itemId", "deletedby", "module_name");
        $itemId = htmlspecialchars(trim($_POST['itemId']));
        $module_name = $_GET['module_name'];
        if (empty($module_name)) {
            $module_name = $_POST['module_name'];
        }
        $response = 0;
        foreach ($_POST as $key => $value) {
            if (in_array($key, $rejectIt)) {
                continue;
            }
            // Поле status может быть '0' — это валидное значение, нельзя пропускать
            if ($key !== 'status' && $value === '') {
                continue;
            }
            if (DateTime::createFromFormat('d.m.Y', $value) !== false) {
                $value = date("Y-m-d", strtotime($value));
            }
            if ($key == 'password') {
                if ($value === '') {
                    continue; // пустой пароль не сохраняем
                }
                $value = password_hash($value, PASSWORD_BCRYPT);
            }
            $value_esc = mysqli_real_escape_string($db, $value);
            $sql = " UPDATE $module_name SET $key = '$value_esc' WHERE id = '$itemId' ";
            if (!mysqli_query($db, $sql)) {
                echo mysqli_error($db);
            } else {
                $response = 1;
            }
        }
        echo $response;
    } else if ($_GET['type'] == 2) { // inline delete
        $itemId = htmlspecialchars(trim($_POST['itemId']));
        $module_name = $_POST['module_name'];
        if ($module_name == 'orders') {
            $sql = " UPDATE payments SET teslimId = '0' WHERE teslimId = '$itemId' ";
            if (!mysqli_query($db, $sql)) {
                echo mysqli_error($db);
            } else {
                //$response = 1;
            }
        }
        $sql = " UPDATE $module_name SET deletedby = '$user_id' WHERE id = '$itemId' ";
        if (!mysqli_query($db, $sql)) {
            echo mysqli_error($db);
        } else {
            $response = 1;
        }
        echo $response;
    } else if ($_GET['type'] == 3) { // customer add
        $identification = htmlspecialchars(trim($_POST['identification']));
        $company = htmlspecialchars(trim($_POST['company']));
        $end_date = htmlspecialchars(trim($_POST['end_date']));
        $pin = htmlspecialchars(trim($_POST['pin']));
        $pin_serial = htmlspecialchars(trim($_POST['pin_serial']));
        $serial = htmlspecialchars(trim($_POST['serial']));
        $name = htmlspecialchars(trim($_POST['namesurname']));
        $phone = htmlspecialchars(trim($_POST['phone']));
        $car_id = htmlspecialchars(trim($_POST['car_id']));
        $car_pin = htmlspecialchars(trim($_POST['car_pin']));
        $make = htmlspecialchars(trim($_POST['make']));
        $model = htmlspecialchars(trim($_POST['model']));
        $type = htmlspecialchars(trim($_POST['type']));
        $engineType = htmlspecialchars(trim($_POST['engineType']));
        $premium = htmlspecialchars(trim($_POST['premium']));
        $bm = htmlspecialchars(trim($_POST['bm']));
        $valuesp = htmlspecialchars(trim($_POST['valuesp']));
        $note = htmlspecialchars(trim($_POST['note']));
        $createdOperator = htmlspecialchars(trim($_POST['createdOperator']));
        $sql = 'INSERT INTO customers ( identification, company, end_date, pin, pin_serial, serial, name, car_id, car_pin, make, model, type, engineType, premium, bm, phone, valuesp, note, createdby ) VALUES ( "' . $identification . '", "' . $company . '", "' . $end_date . '", "' . $pin . '", "' . $pin_serial . '", "' . $serial . '", "' . $name . '", "' . $car_id . '", "' . $car_pin . '", "' . $make . '", "' . $model . '", "' . $type . '", "' . $engineType . '", "' . $premium . '", "' . $bm . '", "' . $phone . '", "' . $valuesp . '", "' . $note . '", "' . $createdOperator . '" )';
        $query = mysqli_query($db, $sql);
        if ($query) {
            $itemId = mysqli_insert_id($db);
            echo $car_id;
        } else {
            echo "Error";
        }
    } else if ($_GET['type'] == 4) { // chat add
        $messageInput = htmlspecialchars(trim($_POST['messageInput']));
        $sql = 'INSERT INTO chat ( message, createdby ) VALUES ( "' . $messageInput . '", "' . $user_id . '" )';
        $query = mysqli_query($db, $sql);
        if ($query) {
            echo 1;
        } else {
            echo "Error";
        }
    } else if ($_POST['type'] == 5) { // callcenter status add
        $orderDate = date("Y-m-d");
        $status_type = htmlspecialchars(trim($_POST['status_type']));
        $car_id = htmlspecialchars(trim($_POST['car_id']));
        $note = htmlspecialchars(trim($_POST['note']));
        $next_date = htmlspecialchars(trim($_POST['next_date']));
        $write_date = htmlspecialchars(trim($_POST['write_date']));
        $identification = htmlspecialchars(trim($_POST['identification']));
        $end_date = htmlspecialchars(trim($_POST['end_date']));
        $selectCompany = htmlspecialchars(trim($_POST['selectCompany']));
        $selectProduct = htmlspecialchars(trim($_POST['selectProduct']));
        $agreePrice = htmlspecialchars(trim($_POST['agreePrice']));
        $bonus = htmlspecialchars(trim($_POST['bonus']));
        $price = htmlspecialchars(trim($_POST['price']));
        $paycode = htmlspecialchars(trim($_POST['paycode']));
        $forwardTo = htmlspecialchars(trim($_POST['forwardTo']));
        $fast = htmlspecialchars(trim($_POST['fast']));
        $disableEarn = htmlspecialchars(trim($_POST['disableEarn']));
        $disableEarnAll = htmlspecialchars(trim($_POST['disableEarnAll']));
        $getDefaultPrice = mysqli_fetch_array(mysqli_query($db, "SELECT id, valuesp, note, premium, createdby FROM customers WHERE car_id = '$car_id' ORDER by id DESC LIMIT 1"));
        $premiumPrice = $getDefaultPrice['premium'];
        $sql = 'INSERT INTO call_status ( type, car_id, content, next_date, companyId, productId, agreePrice, price, bonus, paycode, fast, forwardTo, disableEarn, disableEarnAll, status, createdby ) VALUES ( "' . $status_type . '", "' . $car_id . '", "' . $note . '", "' . $next_date . '", "' . $selectCompany . '", "' . $selectProduct . '", "' . $agreePrice . '", "' . $premiumPrice . '", "' . $bonus . '", "' . $paycode . '", "' . $fast . '", "' . $forwardTo . '", "' . $disableEarn . '", "' . $disableEarnAll . '", 1, "' . $user_id . '" )';
        $query = mysqli_query($db, $sql);
        if ($query) {
            //echo "(events) OK! <br>";
            $statusId = mysqli_insert_id($db);
            $getSuccessStatus = mysqli_fetch_array(mysqli_query($db, "SELECT id FROM paramitems WHERE code = 'success'"));
            if ($status_type == $getSuccessStatus['id']) {
                $getSaller = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM call_status WHERE YEAR(created) = YEAR(CURDATE()) AND car_id = '$car_id' AND type IN (SELECT id FROM paramitems WHERE code = 'confirm' AND status = 1 AND deletedby = 0) ORDER by id DESC"));
                $saller = $getSaller['createdby'];
                $selectedCompany = $getSaller['companyId'];
                $selectedProduct = $getSaller['productId'];
                $agreePrice = $getSaller['agreePrice'];
                if (empty($getSaller['id'])) {
                    $getSaller = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM call_status WHERE car_id = '$car_id' AND type IN (SELECT id FROM paramitems WHERE code = 'payed' AND status = 1 AND deletedby = 0) ORDER by id DESC"));
                    //$saller = $getSaller['createdby'];
                    $selectedCompany = $getSaller['companyId'];
                    $selectedProduct = $getSaller['productId'];
                    $agreePrice = $getSaller['agreePrice'];
                }
                $getPayWaitStatus = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM call_status WHERE car_id = '$car_id' AND type IN (SELECT id FROM paramitems WHERE code = 'paywait' AND status = 1 AND deletedby = 0) ORDER by id DESC"));
                $price = $getPayWaitStatus['price'];
                if (empty($getPayWaitStatus['id'])) {
                    $price = $getSaller['agreePrice'];
                }
                if ($disableEarn == 0) {
                    // $getCurrentSetting = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE id = 43"));
                    // $adetails = json_decode($getCurrentSetting['adetails'], true);
                    // foreach ($adetails as $obj) {

                    //     if($saller == $obj['one'] && $selectedCompany == $obj['two'] && $price >= $obj['three'] && $price <= $obj['four']){
                    //         $bonus = $obj['five'];
                    //     }

                    // }
                    // $userEarn = $agreePrice * $bonus / 100;
                }
                if ($disableEarnAll == 0) {
                    // $getCurrentSetting = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE id = 43"));
                    // $details = json_decode($getCurrentSetting['details'], true);
                    // foreach ($details as $obj) {

                    //     if($selectedCompany == $obj['one'] && $price >= $obj['two'] && $price <= $obj['three']){
                    //         $bonus = $obj['four'];
                    //     }

                    // }
                    // $companyEarn = $price * $bonus / 100;
                }
                $totalEarn = $companyEarn - $userEarn;
                $customerId = $getDefaultPrice['id'];
                $defaultPrice = $getDefaultPrice['valuesp'];
                $carContent = $getDefaultPrice['note'];
                if (!empty($_POST['changeUser'])) {
                    $saller = $_POST['changeUser'];
                } else {
                    if (empty($saller)) {
                        $saller = $getDefaultPrice['createdby'];
                    }
                }
                mysqli_query($db, "UPDATE call_status SET identification = '$identification' , companyId = '$selectedCompany' , productId = '$selectedProduct' , agreeUser = '$saller' , agreePrice = '$agreePrice' , defaultPrice = '$defaultPrice' , userEarn = '$userEarn' , companyEarn = '$companyEarn' , write_date = '$write_date' , content = '$carContent' WHERE id = '$statusId'");
                mysqli_query($db, "UPDATE customers SET identification = '$identification' , company = '$selectedCompany' , end_date = '$end_date' , createdby = '$saller' WHERE car_id = '$car_id'");
                mysqli_query($db, "UPDATE customers_temp SET identification = '$identification' , company = '$selectedCompany' , end_date = '$end_date' , createdby = '$saller' WHERE car_id = '$car_id'");
            }

            // add payment

            $paymentMethod = $_POST['paymentMethod'];

            foreach ($paymentMethod as $key => $payMethodId) {

                $getPaymentAccount = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM finaccounts WHERE title = '$payMethodId'"));

                $allprice = $_POST['allprice'][$key];

                if ($allprice > 0) {

                    $title = $note;

                    $finaccounts = $getPaymentAccount['title'];
                    $fincategory = 'Sığorta ödənişləri';
                    $amount = $allprice;

                    $orderId = $identification;

                    if ($status_type == $getSuccessStatus['id']) {

                        $to = $car_id;
                        $from = $finaccounts;
                    } else {

                        $from = $car_id;
                        $to = $finaccounts;
                    }

                    $sql = 'INSERT INTO payments ( fromAccount, toAccount, orderId, title, amount, paydate, category, status, createdby ) VALUES ( "' . $from . '", "' . $to . '", "' . $orderId . '", "' . $title . '", "' . $amount . '", "' . $orderDate . '", "' . $fincategory . '", 1, "' . $user_id . '" )';
                    $query = mysqli_query($db, $sql);
                    if ($query) {

                        $costId = mysqli_insert_id($db);
                        //addlog($user_id, "35", $costId, "#".$costId." nömrəli xərc əlavə etdi.");

                        //echo "1";
                    } else {
                        echo mysqli_error($db);
                    }
                }
            }

            // add payment

            mysqli_query($db, "UPDATE users SET currentCustomer = '' WHERE id = '$user_id'");


            echo 1;
        } else {

            echo "(stock) Error: " . $sql . "<br>" . mysqli_error($db);
            //header('Location: /index.php?action=error');
        }
    } else if ($_GET['type'] == 6) { // users add

        $name = htmlspecialchars(trim($_POST['name']));
        $surname = htmlspecialchars(trim($_POST['surname']));
        $email = htmlspecialchars(trim($_POST['email']));
        $groupId = htmlspecialchars(trim($_POST['groupId']));
        $password = mysqli_real_escape_string($db, $_POST['password']);

        $newpass = password_hash($password, PASSWORD_BCRYPT);

        $sql = 'INSERT INTO users ( name, surname, email, groupId, password, status, createdby ) VALUES ( "' . $name . '", "' . $surname . '", "' . $email . '", "' . $groupId . '", "' . $newpass . '", 1, "' . $user_id . '" )';
        $query = mysqli_query($db, $sql);
        if ($query) {

            $itemId = mysqli_insert_id($db);

            echo "1";
        } else {
            echo "Error";
        }
    } else if ($_GET['type'] == 8) { // payment add

        $category = htmlspecialchars(trim($_POST['category']));
        $from = htmlspecialchars(trim($_POST['from']));
        $to = htmlspecialchars(trim($_POST['to']));
        $amount = floatval($_POST['amount']);
        $note = htmlspecialchars(trim($_POST['note']));
        $paydate = htmlspecialchars(trim($_POST['paydate']));
        $identification = htmlspecialchars(trim($_POST['identification']));

        if (empty($from) || empty($to) || $amount <= 0) {
            echo "Error: wrong data";
            exit;
        }

        if (empty($identification)) {
            $getLastIdentification = mysqli_fetch_array(mysqli_query($db, "
            SELECT identification 
            FROM call_status 
            WHERE (car_id = '$from' OR car_id = '$to') 
            AND identification != '' 
            ORDER by id DESC 
            LIMIT 1
        "));
            $identification = $getLastIdentification['identification'] ?? '';
        }

        $sql = "INSERT INTO payments 
        (category, fromAccount, toAccount, orderId, amount, paydate, title, status, createdby) 
        VALUES 
        ('$category','$from','$to','$identification','$amount','$paydate','$note',1,'$user_id')";

        $query = mysqli_query($db, $sql);
        if ($query) {

            $itemId = mysqli_insert_id($db);

            echo "1";
        } else {
            echo mysqli_error($db);
        }
    } else if ($_GET['type'] == 10) { // orders add

        $title = htmlspecialchars(trim($_POST['title']));
        $note = htmlspecialchars(trim($_POST['note']));
        $paydate = htmlspecialchars(trim($_POST['paydate']));
        $to = htmlspecialchars(trim($_POST['to']));
        $from = htmlspecialchars(trim($_POST['from']));

        $car_ids = $_POST['car_ids'] ?? [];
        $cost_ids = $_POST['cost_ids'] ?? [];

        $totalAmount = 0;
        $totalCost = 0;

        $sql = 'INSERT INTO orders ( title, accountId, toAccount, note, createdby ) 
            VALUES ( "' . $title . '", "' . $from . '", "' . $to . '", "' . $note . '", "' . $user_id . '" )';

        $query = mysqli_query($db, $sql);

        if ($query) {

            $orderId = mysqli_insert_id($db); // ← сохраняем ID orders

            foreach ($car_ids as $car_id) {
                mysqli_query($db, "UPDATE payments SET teslimId = '$orderId' WHERE id = '$car_id'");

                $getPayment = mysqli_fetch_array(mysqli_query($db, "SELECT amount FROM payments WHERE id = '$car_id'"));
                $totalAmount += $getPayment['amount'];
            }

            foreach ($cost_ids as $cost_id) {
                mysqli_query($db, "UPDATE payments SET teslimId = '$orderId' WHERE id = '$cost_id'");

                $getPayment = mysqli_fetch_array(mysqli_query($db, "SELECT amount FROM payments WHERE id = '$cost_id'"));
                $totalCost += $getPayment['amount'];
            }

            $transfer = ($totalAmount - $totalCost);

            mysqli_query($db, 'INSERT INTO payments 
            ( category, fromAccount, toAccount, orderId, amount, paydate, title, teslimId, status, createdby ) 
            VALUES 
            ( "Transfer", "' . $from . '", "' . $to . '", "' . $orderId . '", "' . $transfer . '", "' . $paydate . '", "' . $note . '", 3, 1, "' . $user_id . '" )');

            echo $orderId; // ← возвращаем ТОЛЬКО ID orders
            exit;
        } else {
            echo mysqli_error($db);
            exit;
        }
    } else if ($_GET['type'] == 11) { // confirm

        $itemId = $_POST['itemId'];
        $module_name = $_POST['module_name'];

        $sql = " UPDATE " . $module_name . " SET confirmed = '$user_id' WHERE id = '$itemId' ";

        if (!mysqli_query($db, $sql)) {
            echo "Error";
        } else {

            echo "1";
        }
    } else if ($_GET['type'] == 12) { // status

        $itemId = $_POST['itemId'];
        $status = $_POST['status'];
        $module_name = $_POST['module_name'];

        $sql = " UPDATE " . $module_name . " SET status = '$status' WHERE id = '$itemId' ";

        if (!mysqli_query($db, $sql)) {
            echo "Error";
        } else {

            echo "1";
        }
    } else if ($_GET['type'] == 13) { // transferdata

        $from = $_POST['from'];
        $to = $_POST['to'];

        $sqlRow = mysqli_query($db, "SELECT * FROM call_status WHERE createdby = '$from' AND type = '5'");
        while ($rowRow = mysqli_fetch_array($sqlRow)) {

            $itemId = $rowRow['id'];

            $sql = " UPDATE call_status SET createdby = '$to' WHERE id = '$itemId' ";

            if (!mysqli_query($db, $sql)) {
                echo "Error";
            } else {

                echo "1";
            }
        }
    }else if ($_GET['type'] == 20) { // payments2 add
        $category     = mysqli_real_escape_string($db, $_POST['category']);
        $subcategory  = mysqli_real_escape_string($db, $_POST['subcategory']);
        $subtype      = mysqli_real_escape_string($db, $_POST['subtype'] ?? '');
        $insurance_type = ($subcategory === 'sigorta') ? mysqli_real_escape_string($db, $_POST['subtype'] ?? '') : '';
        $payer_type   = mysqli_real_escape_string($db, $_POST['payer_type'] ?? '');
        $from_account = mysqli_real_escape_string($db, $_POST['from_account'] ?? '');
        $to_account   = mysqli_real_escape_string($db, $_POST['to_account'] ?? '');
        $identification = mysqli_real_escape_string($db, $_POST['identification'] ?? '');
        $car_id       = mysqli_real_escape_string($db, $_POST['car_id'] ?? '');
        $amount       = floatval($_POST['amount']);
        $effect       = intval($_POST['effect']);
        $paydate      = mysqli_real_escape_string($db, $_POST['paydate']);
        $note         = mysqli_real_escape_string($db, $_POST['note'] ?? '');

        // For sigorta: insurance_type = subtype
        if ($subcategory === 'sigorta') {
            $insurance_type = $subtype;
            $subtype = '';
        }

        $sql = "INSERT INTO payments2 (category, subcategory, insurance_type, payer_type, subtype, from_account, to_account, identification, car_id, amount, effect, paydate, note, createdby)
                VALUES ('$category','$subcategory','$insurance_type','$payer_type','$subtype','$from_account','$to_account','$identification','$car_id','$amount','$effect','$paydate','$note','$user_id')";

        if (mysqli_query($db, $sql)) {
            echo '1';
        } else {
            echo mysqli_error($db);
        } 
    }
}
