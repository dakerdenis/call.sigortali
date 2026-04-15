<?php
if (isset($_SESSION['login']) && isset($_SESSION['id'])) {
  $limit = $_POST['limit'];
  $page = 1;
  if ($_POST['page'] > 1) {
    $start = (($_POST['page'] - 1) * $limit);
    $page = $_POST['page'];
  } else {
    $start = 0;
  }
  $regexp = addslashes('[\\[\\]\\" ]');
  $break = 0;
  //start dynamic
?>
<?php
  if ($_GET['type'] == 1) { // customers grid
    $getSettings_startCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'startCustomer'"));
    $getSettings_limitCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'limitCustomer'"));
    $getSettings_minprice = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'minprice'"));
    $getSettings_maxprice = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'maxprice'"));
    $currentCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT currentCustomer FROM users WHERE id = '$user_id'"));
    $startCustomer = $getSettings_startCustomer['setting_value'];
    $limitCustomer = $getSettings_limitCustomer['setting_value'];
    $today = date("Y-m-d");
    $start_date = date('Y-m-d', strtotime($today . ' + ' . $startCustomer . ' days'));
    $end_date = date('Y-m-d', strtotime($start_date . ' + ' . $limitCustomer . ' days'));
    $minprice = $getSettings_minprice['setting_value'];
    $maxprice = $getSettings_maxprice['setting_value'];
    $query_alltotal = 'SELECT id FROM customers';
    $query = "SELECT *  FROM customers WHERE 1=1";
    if ($_POST['query'] != '') {
      //$query .= ' AND ( id = "'.str_replace(' ', '%', $_POST['query']).'" ';
      $query .= ' AND ( car_id = "' . $_POST['query'] . '"';
      $query .= ' OR phone = "' . $_POST['query'] . '"';
      $query .= ' OR pin = "' . $_POST['query'] . '"';
      $query .= ' )';
      if ($userGroup == 3 && $user_id != 14) {
        //if($userGroup == 3){
        $query .= " AND ((car_id IN (SELECT car_id FROM call_status WHERE createdby = '$user_id' AND created > now() - INTERVAL 60 day)) OR createdby = '$user_id' OR car_id IN (SELECT car_id FROM call_status WHERE forwardTo = '$user_id' AND created > now() - INTERVAL 60 day))";
      }

      $query .= ' ORDER BY id DESC';

      $getCustomerCount = mysqli_num_rows(mysqli_query($db, $query));
    } else {

      if (!empty($currentCustomer['currentCustomer'])) {
        $query .= " AND id = " . $currentCustomer['currentCustomer'];
      } else {

        if ($userGroup == 3) {
          $query .= " AND DATE(end_date) >= CURDATE() AND car_id IN (SELECT car_id FROM call_status WHERE created > now() - INTERVAL 60 day AND createdby = '$user_id' AND DATE(next_date) <= CURDATE() AND type IN (SELECT id FROM paramitems WHERE (code = 'waiting' OR code = 'forward') AND status = 1 AND deletedby = 0) AND id IN (SELECT MAX(id) FROM call_status GROUP BY car_id))";
          $getCustomer = mysqli_fetch_array(mysqli_query($db, $query));
          if (empty($getCustomer['id'])) {
            if ($user_id == 14) {
              $query .= " AND end_date = CURDATE() + INTERVAL 30 DAY AND (createdby = '$user_id' OR createdby = 2)";
              //$query .= " AND createdby = '$user_id' AND end_date > now() - INTERVAL 1 day AND end_date < now() + INTERVAL 31 day AND car_id NOT IN (SELECT car_id FROM call_status WHERE year(created) = year(curdate()))";
            } else {
              $query .= " AND end_date = CURDATE() + INTERVAL 30 DAY AND createdby = '$user_id'";
            }
            $getCustomer = mysqli_fetch_array(mysqli_query($db, $query));
            if (empty($getCustomer['id'])) {
              $allKey = 1;
              $query = "SELECT * FROM customers_temp WHERE 1=1";
              if (!empty($minprice) && !empty($maxprice)) {
                $query .= " AND valuesp >= $minprice";
                $query .= " AND valuesp <= $maxprice";
              }
              $query .= " AND end_date between '$start_date' AND '$end_date'";
              $query .= " AND car_id NOT IN (SELECT car_id FROM call_status WHERE deletedby = 0)";
              $query .= " AND id NOT IN (SELECT currentCustomer FROM users WHERE id != '$user_id')";
            }
          }
        } else {
          $query .= " AND car_id IN (SELECT car_id FROM call_status WHERE created > now() - INTERVAL 60 day AND deletedby = 0 AND type IN (SELECT id FROM paramitems WHERE (code = 'confirm' OR code = 'payed') AND status = 1 AND deletedby = 0) AND id IN (SELECT MAX(id) FROM call_status GROUP BY car_id))";
        }
      }
      $query .= ' ORDER BY RAND()';
    }
} else if ($_GET['type'] == 2) { // customers table
    $query_alltotal = 'SELECT id FROM customers';
    $query = "SELECT *  FROM customers WHERE 1=1";
    if ($userGroup == 3) {
      $query .= " AND createdby = '$user_id' AND end_date > now() + INTERVAL 29 day AND end_date < now() + INTERVAL 31 day AND car_id NOT IN (SELECT car_id FROM call_status WHERE year(created) = year(curdate()))";
    }
    if ($_POST['query'] != '') {
      $q = mysqli_real_escape_string($db, $_POST['query']);
      $query .= " AND ( id = '$q' ";
      $query .= " OR car_id LIKE '%$q%' ";
      $query .= " OR car_pin LIKE '%$q%' ";
      $query .= " OR name LIKE '%$q%' ";
      $query .= " OR pin LIKE '%$q%' ";
      $query .= " OR identification LIKE '%$q%' ";
      $query .= " OR phone LIKE '%$q%' ";
      $query .= " )";
    }

    // Сортировка по end_date
    $sortDir = (isset($_POST['sort']) && $_POST['sort'] === 'desc') ? 'DESC' : 'ASC';
    $query .= " ORDER BY end_date $sortDir";
  } else if ($_GET['type'] == 22) { // companies table
    $query_alltotal = 'SELECT id FROM customers';
    $query = "SELECT * FROM customers WHERE LENGTH(pin)=10 AND pin REGEXP '^[0-9]+$'";
    $date_from = $_POST['date_from'] ?? '';
    $date_to = $_POST['date_to'] ?? '';
    if (!empty($date_from)) {
      $df = mysqli_real_escape_string($db, $date_from);
      $query .= " AND DATE(end_date)>='$df'";
    }
    if (!empty($date_to)) {
      $dt = mysqli_real_escape_string($db, $date_to);
      $query .= " AND DATE(end_date)<='$dt'";
    }
    if ($userGroup == 3) {
      $query .= " AND end_date > now() + INTERVAL 29 day AND end_date < now() + INTERVAL 31 day AND car_id NOT IN (SELECT car_id FROM call_status WHERE year(created) = year(curdate()))";
    }
    if ($_POST['query'] != '') {
      $query .= ' AND ( id = "' . str_replace(' ', '%', $_POST['query']) . '" ';
      $query .= ' OR car_id LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR car_pin LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR name LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR pin LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR identification LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR phone LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' )';
    }
    $query .= ' ORDER BY end_date ASC';
  } else if ($_GET['type'] == 3) { // chat
    $query_alltotal = 'SELECT id FROM chat';
    $query = "SELECT *  FROM chat";
    // if ($_POST['query'] !='') {
    //     $query .= ' AND ( id = "'.str_replace(' ', '%', $_POST['query']).'" ';
    //     $query .= ' OR car_id LIKE "%'.str_replace(' ', '%', $_POST['query']).'%"';
    //     $query .= ' OR name LIKE "%'.str_replace(' ', '%', $_POST['query']).'%"';
    //     $query .= ' )';
    // }
    $query .= ' ORDER BY id ASC';
  } else if ($_GET['type'] == 4) { // users table
    $query_alltotal = 'SELECT id FROM users';
    $query = "SELECT *  FROM users WHERE deletedby = 0";
    if ($_POST['query'] != '') {
      $query .= ' AND ( id = "' . str_replace(' ', '%', $_POST['query']) . '" ';
      $query .= ' OR name LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR surname LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' )';
    }
    $query .= ' ORDER BY id DESC';
  } else if ($_GET['type'] == 5) { // accounting
    $query_alltotal = 'SELECT id FROM call_status';
    $query = "SELECT * FROM call_status WHERE deletedby = 0 AND type IN (SELECT id FROM paramitems WHERE code = 'success')";
    $date_from = $_POST['date_from'] ?? '';
    $insurance_type = $_POST['insurance_type'] ?? '';
    $company_filter = $_POST['company'] ?? '';
    $payment_type = $_POST['payment_type'] ?? '';
    $date_to = $_POST['date_to'] ?? '';
    $price_min = $_POST['price_min'] ?? '';
    $price_max = $_POST['price_max'] ?? '';

    if (!empty($date_from)) {
      $df = mysqli_real_escape_string($db, $date_from);
      $query .= " AND DATE(write_date)>='$df'";
    }
    if (!empty($date_to)) {
      $dt = mysqli_real_escape_string($db, $date_to);
      $query .= " AND DATE(write_date)<='$dt'";
    }

    if ($price_min !== '' && is_numeric($price_min)) {
      $min = (float)$price_min;
      $query .= " AND agreePrice >= $min";
    }
    if ($price_max !== '' && is_numeric($price_max)) {
      $max = (float)$price_max;
      $query .= " AND agreePrice <= $max";
    }
    if (!empty($insurance_type)) {
      $insurance_type = mysqli_real_escape_string($db, $insurance_type);
      $query .= " AND insuranceType='$insurance_type'";
    }
    if (!empty($company_filter)) {
      $company_filter = (int)$company_filter;
      $query .= " AND companyId=$company_filter";
    }
    if (!empty($payment_type)) {

      $payment_type = mysqli_real_escape_string($db, $payment_type);

      if ($payment_type == 'KART') {
        $query .= " AND car_id IN (
SELECT fromAccount FROM payments
WHERE deletedby=0
AND toAccount!='Özü ödədi'
)";
      }

      if ($payment_type == 'NAĞD') {
        $query .= " AND car_id IN (
SELECT fromAccount FROM payments
WHERE deletedby=0
AND toAccount='Özü ödədi'
)";
      }
    }
    if ($_POST['query'] != '') {
      $query .= ' AND ( id = "' . str_replace(' ', '%', $_POST['query']) . '" ';
      $query .= ' OR car_id LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' )';
    }
    // Borclular parametri üçün SQL səviyyəsində daha dəqiq şərt
    // if ($_POST['params'] == 'Borclular') {
    //     $query .= " AND car_id IN (
    //         SELECT t.car_id
    //         FROM (
    //             SELECT cs.car_id, MAX(cs.id) as max_id
    //             FROM call_status cs
    //             WHERE cs.deletedby = 0
    //             AND cs.type IN (SELECT id FROM paramitems WHERE code = 'success')
    //             GROUP BY cs.car_id
    //         ) t
    //         JOIN call_status cs2 ON cs2.id = t.max_id
    //         LEFT JOIN (
    //             SELECT fromAccount, SUM(amount) as totalPayment
    //             FROM payments
    //             WHERE deletedby = 0
    //             GROUP BY fromAccount
    //         ) p ON p.fromAccount = t.car_id
    //         WHERE (cs2.agreePrice - COALESCE(p.totalPayment, 0)) > 0
    //     )";
    // }
    if ($_POST['params'] == 'Borclular') {
      //$query .= " AND agreeUser = '$user_id' ";
    }
    if ($userGroup == 3) {
      $query .= " AND agreeUser = '$user_id' ";
    }
    $query .= ' ORDER BY id DESC';
  } else if ($_GET['type'] == 6) { // finance categories
    $query_alltotal = 'SELECT id FROM fincategory';
    $query = "SELECT * FROM fincategory WHERE deletedby = 0";
    if ($_POST['query'] != '') {
      $query .= ' AND ( id = "' . str_replace(' ', '%', $_POST['query']) . '" ';
      $query .= ' OR title LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' )';
    }
    $query .= ' ORDER BY id DESC';
  } else if ($_GET['type'] == 7) { // finaccounts
    $query_alltotal = 'SELECT id FROM finaccounts';
    $query = "SELECT * FROM finaccounts WHERE status = 1 AND deletedby = 0";
    if ($_POST['query'] != '') {
      $query .= ' AND ( id = "' . str_replace(' ', '%', $_POST['query']) . '" ';
      $query .= ' OR title LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' )';
    }
    $query .= ' ORDER BY id DESC';
  } else if ($_GET['type'] == 8) { // finance payments
    if ($_POST['filter'] == 1) {
      $filter = $_POST['filter'];
      $filter1 = implode(",", $_POST['filter1']);
      $filter2 = implode(",", $_POST['filter2']);
      $filter3 = implode(",", $_POST['filter3']);
      $filter4 = implode(",", $_POST['filter4']);
      $filter6 = $_POST['filter6'];
      $filter7 = $_POST['filter7'];
    }
    $query_alltotal = 'SELECT id FROM payments';
    $query = "SELECT *  FROM payments WHERE deletedby = 0";
    if ($_POST['payType'] == 1) {
      $query .= ' AND orderId IN (SELECT id FROM orders WHERE opencredit = 1) ';
    }
    if ($_POST['query'] != '') {
      $query .= ' AND ( id = "' . str_replace(' ', '%', $_POST['query']) . '" ';
      $query .= ' OR title LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR fromAccount LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR toAccount LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR orderId LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' OR amount LIKE "%' . str_replace(' ', '%', $_POST['query']) . '%"';
      $query .= ' )';
    }
    if (!empty($_POST['fromAccount'])) {
      $fa = mysqli_real_escape_string($db, $_POST['fromAccount']);
      $query .= " AND (fromAccount = '$fa' OR toAccount = '$fa')";
    }
    if ($_POST['account'] != '') {
      $query .= ' AND ( fromAccount LIKE "' . str_replace(' ', '%', $_POST['account']) . '" OR toAccount LIKE "' . str_replace(' ', '%', $_POST['account']) . '" )';
    } else if ($_POST['query'] == '') {
      $query .= " AND fromAccount != 'Özü ödədi' AND toAccount != 'Özü ödədi'";
    }
    if ($_POST['category'] != '') {
      $query .= ' AND category = "' . str_replace(' ', '%', $_POST['category']) . '"';
    }
    if ($filter == 1) {
      $query .= " AND ( 1=1 ";
      if ($filter2 != "") {
        $query .= "AND ( fromAccount IN ('$filter2') OR toAccount IN ('$filter2') )";
      }
      if ($filter4 != "") {
        $query .= "AND category IN ('$filter4') ";
      }
      if ($filter6 != "") {
        $query .= "AND ((DATE(paydate) >= '$filter6' AND DATE(paydate) <= '$filter7')) ";
      }
      $query .= ")";
    }
    // --- NEW FILTERS (finance2) ---
    $date_from = $_POST['date_from'] ?? '';
    $date_to   = $_POST['date_to'] ?? '';
    $amount_min = $_POST['amount_min'] ?? '';
    $amount_max = $_POST['amount_max'] ?? '';
    $category2 = $_POST['category2'] ?? '';   // category exact
    $hesabat_id = $_POST['hesabat_id'] ?? ''; // teslimId
    $seh_no = $_POST['seh_no'] ?? '';         // orderId search (Şəhadətnamə)
    // Date range
    if (!empty($date_from) && !empty($date_to)) {
      $df = mysqli_real_escape_string($db, $date_from);
      $dt = mysqli_real_escape_string($db, $date_to);
      $query .= " AND DATE(paydate) BETWEEN '$df' AND '$dt'";
    } else if (!empty($date_from)) {
      $df = mysqli_real_escape_string($db, $date_from);
      $query .= " AND DATE(paydate) >= '$df'";
    } else if (!empty($date_to)) {
      $dt = mysqli_real_escape_string($db, $date_to);
      $query .= " AND DATE(paydate) <= '$dt'";
    }
    // Amount range
    if ($amount_min !== '' && is_numeric($amount_min)) {
      $minv = (float)$amount_min;
      $query .= " AND amount >= $minv";
    }
    if ($amount_max !== '' && is_numeric($amount_max)) {
      $maxv = (float)$amount_max;
      $query .= " AND amount <= $maxv";
    }
    // Category exact
    if (!empty($category2)) {
      $cat = mysqli_real_escape_string($db, $category2);
      $query .= " AND category = '$cat'";
    }

    // Hesabat ID = teslimId
    if (!empty($hesabat_id)) {
      $hid = (int)$hesabat_id;
      $query .= " AND teslimId = $hid";
    }
    // Şəhadətnamə nömrəsi = payments.orderId (LIKE)
    if (!empty($seh_no)) {
      $sn = mysqli_real_escape_string($db, $seh_no);
      $query .= " AND orderId LIKE '%$sn%'";
    }
    $query .= ' ORDER BY id DESC';
  } else if ($_GET['type'] == 9) { // statuses
    $query_alltotal = 'SELECT id FROM call_status';
    $query = "SELECT * FROM call_status WHERE deletedby = 0";


    if ($_POST['query'] != '') {

      $q = mysqli_real_escape_string($db, $_POST['query']);

      $query .= " AND ( car_id LIKE '%$q%' )";
    }



    if (!empty($_POST['status_filter'])) {

      $status = (int)$_POST['status_filter'];

      $query .= " AND type = $status";
    }



    if (!empty($_POST['date_from'])) {

      $date_from = mysqli_real_escape_string($db, $_POST['date_from']);

      $query .= " AND DATE(created) >= '$date_from'";
    }


    if (!empty($_POST['date_to'])) {

      $date_to = mysqli_real_escape_string($db, $_POST['date_to']);

      $query .= " AND DATE(created) <= '$date_to'";
    }


    if ($_POST['params'] != '') {
      $query .= ' ';
    }
 //   if ($userGroup == 3) {
   //   $query .= " AND agreeUser = '$user_id' ";
   // }
    $query .= ' ORDER BY id DESC';
  } else if ($_GET['type'] == 10) { // orders
    $query_alltotal = 'SELECT id FROM orders';
    $query = "SELECT * FROM orders WHERE deletedby = 0";

    $date_from  = isset($_POST['date_from']) ? trim($_POST['date_from']) : '';
    $date_to    = isset($_POST['date_to']) ? trim($_POST['date_to']) : '';
    $accountId  = isset($_POST['accountId']) ? trim($_POST['accountId']) : '';
    $toAccount2 = isset($_POST['toAccount']) ? trim($_POST['toAccount']) : '';
    $profit_min = isset($_POST['profit_min']) ? trim($_POST['profit_min']) : '';
    $profit_max = isset($_POST['profit_max']) ? trim($_POST['profit_max']) : '';
    $confirmed  = isset($_POST['confirmed']) ? trim($_POST['confirmed']) : '';
    $search_q   = isset($_POST['query']) ? trim($_POST['query']) : '';

    if ($date_from !== '') {
      $df = mysqli_real_escape_string($db, $date_from);
      $query .= " AND DATE(created) >= '$df'";
    }

    if ($date_to !== '') {
      $dt = mysqli_real_escape_string($db, $date_to);
      $query .= " AND DATE(created) <= '$dt'";
    }

    if ($accountId !== '') {
      $aid = mysqli_real_escape_string($db, $accountId);
      $query .= " AND accountId = '$aid'";
    }

    if ($toAccount2 !== '') {
      $ta = mysqli_real_escape_string($db, $toAccount2);
      $query .= " AND toAccount = '$ta'";
    }

    if ($confirmed === 'not') {
      $query .= " AND confirmed = 0";
    } else if ($confirmed === 'yes') {
      $query .= " AND confirmed != 0";
    }

    $profit_min_num = ($profit_min !== '' && is_numeric($profit_min)) ? (float)$profit_min : null;
    $profit_max_num = ($profit_max !== '' && is_numeric($profit_max)) ? (float)$profit_max : null;

    if ($profit_min_num !== null || $profit_max_num !== null) {
      $min = ($profit_min_num !== null) ? $profit_min_num : -999999999;
      $max = ($profit_max_num !== null) ? $profit_max_num : 999999999;

      $query .= " AND (
      (
        COALESCE((
          SELECT SUM(p1.amount)
          FROM payments p1
          WHERE p1.deletedby = 0
            AND p1.teslimId = orders.id
            AND p1.fromAccount != 'Özü ödədi'
            AND p1.fromAccount NOT IN (SELECT title FROM finaccounts)
            AND p1.category = 'Sığorta ödənişləri'
            AND p1.toAccount != 'Özü ödədi'
        ), 0)
        -
        COALESCE((
          SELECT SUM(p2.amount)
          FROM payments p2
          WHERE p2.deletedby = 0
            AND p2.teslimId = orders.id
            AND p2.category != 'Sığorta ödənişləri'
        ), 0)
      ) BETWEEN $min AND $max
    )";
    }

    if ($search_q !== '') {
      $sq = mysqli_real_escape_string($db, $search_q);
      $query .= " AND (
      id LIKE '%$sq%'
      OR created LIKE '%$sq%'
      OR accountId LIKE '%$sq%'
      OR toAccount LIKE '%$sq%'
      OR note LIKE '%$sq%'
    )";
    }

    $query .= " ORDER BY id DESC";
} else if ($_GET['type'] == 11) { // current
    $query_alltotal = 'SELECT id FROM call_status';
    $query = "SELECT * FROM call_status WHERE deletedby = 0 AND type IN (SELECT id FROM paramitems WHERE code = 'success') AND write_date <= DATE_SUB(DATE(DATE_Add(NOW(), INTERVAL 10 DAY)), INTERVAL 1 YEAR)";

    // Поиск по DQN, шəhadətnamə, ID
    if ($_POST['query'] != '') {
      $q = mysqli_real_escape_string($db, $_POST['query']);
      $query .= " AND ( id = '$q' ";
      $query .= " OR car_id LIKE '%$q%' ";
      $query .= " OR identification LIKE '%$q%' ";
      $query .= " OR car_id IN (SELECT car_id FROM customers WHERE name LIKE '%$q%') ";
      $query .= " )";
    }

    // Фильтр по страховой компании
    if (!empty($_POST['company'])) {
      $companyFilter = (int)$_POST['company'];
      $query .= " AND companyId = $companyFilter";
    }

    if ($userGroup == 3) {
      $query .= " AND agreeUser = '$user_id' ";
    }

    // Сортировка по дате (write_date)
    $sortDir = (isset($_POST['sort']) && $_POST['sort'] === 'asc') ? 'ASC' : 'DESC';
    $query .= " ORDER BY write_date $sortDir, id $sortDir";
  }
?>
<?php
  $filter_query = $query . ' LIMIT ' . $start . ', ' . $limit . '';

  // Быстрый подсчёт через COUNT(*) вместо тащить все строки
  $count_query = preg_replace('/^SELECT\s+.*?\s+FROM/is', 'SELECT COUNT(*) as cnt FROM', $query, 1);
  // Убираем ORDER BY из COUNT-запроса — он не нужен и замедляет
  $count_query = preg_replace('/\s+ORDER\s+BY\s+.*$/is', '', $count_query);

  $statement = $connect->prepare($count_query);
  $statement->execute();
  $countRow = $statement->fetch(PDO::FETCH_ASSOC);
  $total_data = (int)$countRow['cnt'];

  $statement = $connect->prepare($filter_query);
  $statement->execute();
  $result = $statement->fetchAll();
  $total_filter_data = count($result);
  $fetchMethod = 'DB FETCH OTHER';
?>
<?php
  $output = '<table id="dynamic_content" class="table table-hover">';
  if ($_GET['type'] == 1) { // customers grid
    foreach ($result as $row) {
      $customerId = $row['id'];
      if ($_POST['query'] == '') {
        mysqli_query($db, "UPDATE users SET currentCustomer = '$customerId' WHERE id = '$user_id'");
      }
      $getPayCode = mysqli_fetch_array(mysqli_query($db, "SELECT paycode, created FROM call_status WHERE paycode != '' AND car_id = '" . $row['car_id'] . "' ORDER by id DESC"));
      $getAcceptedPrice = mysqli_fetch_array(mysqli_query($db, "SELECT agreePrice FROM call_status WHERE agreePrice != '' AND car_id = '" . $row['car_id'] . "' ORDER by id DESC"));
      $getCompany = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM companies WHERE id = " . $row['company']));
      $getStatusDate = $getPayCode['created'];
      $thisTime = date("Y-m-d H:i:s");
      $hourdiff = round((strtotime($thisTime) - strtotime($getStatusDate)) / 3600, 1) . " S";
      $getUser = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['createdby']));
      $output_grid = '

            <div class="modal fade modalShowStatus" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
              <div class="modal-dialog">
                  <div class="modal-content">

                    <div class="modal-body" style="min-height: 470px; max-height: 470px; overflow: auto;">';

      $queryShow = "SELECT * FROM call_status WHERE car_id = '" . $row['car_id'] . "' ORDER by id DESC";
      $sqlShow = mysqli_query($db, $queryShow);
      $countStatus = 0;
      while ($rowShow = mysqli_fetch_array($sqlShow)) {

        $countStatus++;

        $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $rowShow['createdby']));
        $getParamitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = " . $rowShow['type'] . " AND parentId IN (SELECT id FROM params WHERE (code = 'operator' OR code = 'manager') AND status = 1 AND deletedby = 0)"));

        if (!empty($rowShow['companyId'])) {
          $selectedCompany = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM companies WHERE id = " . $rowShow['companyId']));
        }

        if ($countStatus == 1) {
          $lastStatus = $getParamitem['id'];
          $status = '<span style="display: block; padding: 5px; background-color: green; width: 100%; color: #fff;">' . $getParamitem['title'] . '</span>';
        }

        $output_grid .= '

                                    <div class="alert alert-danger ' . $getParamitem['code'] . '" role="alert">
                                        <strong>' . $getCreated['name'] . ' ' . $getCreated['surname'] . ': </strong> ' . $getParamitem['title'] . '<br>
                                        ' . (($rowShow['next_date'] != '0000-00-00 00:00:00') ? '<strong>Xatırlatma Tarixi: </strong> ' . $rowShow['next_date'] . '<br> ' : "") . '
                                        ' . ((!empty($rowShow['content'])) ? '<strong>Qeyd: </strong> ' . $rowShow['content'] . '<br> ' : "") . '
                                        ' . ((!empty($rowShow['companyId'])) ? '<strong>Şirkət: </strong> ' . $selectedCompany['title'] . '<br> ' : "") . '
                                        ' . (($rowShow['price'] != '0.00') ? '<strong>Qiymət: </strong> ' . $rowShow['price'] . '<br> ' : "") . '
                                        ' . (($rowShow['agreePrice'] != '0.00') ? '<strong>Razılaşdığı Qiymət: </strong> ' . $rowShow['agreePrice'] . '<br> ' : "") . '
                                        ' . ((!empty($rowShow['paycode'])) ? '<strong>Ödəniş Kodu: </strong> ' . $rowShow['paycode'] . '<br> ' : "") . '
                                        <strong>Status Tarixi: </strong> ' . $rowShow['created'] . '
                                        <span class="fright d-none">' . $rowShow['created'] . '</span>
                                    </div>

                                ';

        if (empty($agreePrice) && $rowShow['agreePrice'] != '0.00') {
          $agreePrice = $rowShow['agreePrice'];
        }

        if (empty($paycode) && !empty($getPayCode['paycode'])) {
          $paycode = $getPayCode['paycode'];
        }

        if (!empty($rowShow['companyId']) && empty($companyId)) {
          $companyId = $selectedCompany['title'];
        }
      }

      $output_grid .= '

                    </div>

                  </div>
              </div>
            </div>
        
            <div class="projects-section-line" style="margin-top: 10px;">
                <div class="projects-status">

                    <button class="my-btn btn btn-primary active" title="' . lang('Zəng et') . '">
                    <p><i class="fa fa-phone" aria-hidden="true"></i> ' . lang('Zəng et') . '</p>
                    </button>

                    <a href="https://wa.me/' . $row['phone'] . '?text=SalamH%C3%B6rm%C9%99tli+m%C3%BC%C5%9Ft%C9%99ri%2C+' . $row['car_id'] . '+DQN-li+n%C9%99qliyyat+vasit%C9%99sinin+icbari+s%C4%B1%C4%9Forta+polisi+dem%C9%99k+olar+ki%2C+haz%C4%B1rd%C4%B1r.+S%C4%B1%C4%9Forta+haqq%C4%B1n%C4%B1+%C3%B6yr%C9%99nm%C9%99k+v%C9%99+ya+s%C4%B1%C4%9Forta+polisinizi+%C9%99ld%C9%99+etm%C9%99k+%C3%BC%C3%A7%C3%BCn+biz%C9%99+m%C3%BCraci%C9%99t+ed%C9%99+bil%C9%99rsiniz." class="my-btn btn btn-success active" title="' . lang('Cavab Vermədi') . '" target="_blank">
                    <p><i class="fa fa-whatsapp" aria-hidden="true"></i> 1</p>
                    </a>
                    <a href="https://wa.me/' . $row['phone'] . '?text=H%C3%B6rm%C9%99tli+m%C3%BC%C5%9Ft%C9%99ri%2C+' . $row['car_id'] . '+DQN-li+n%C9%99qliyyat+vasit%C9%99sinin+icbari+s%C4%B1%C4%9Forta+polisini+r%C9%99smil%C9%99%C5%9Fdirm%C9%99k+%C3%BC%C3%A7%C3%BCn+siz%C9%99+kod+g%C3%B6nd%C9%99rildi.+A%C5%9Fa%C4%9F%C4%B1da+qeyd+edil%C9%99n+koda+%C3%B6d%C9%99ni%C5%9F+etm%C9%99kl%C9%99+s%C4%B1%C4%9Forta+polisinizi+r%C9%99smil%C9%99%C5%9Fdir%C9%99+bil%C9%99rsiniz.+%C3%96d%C9%99ni%C5%9F+kodu+48+saat+m%C3%BCdd%C9%99tind%C9%99+q%C3%BCvv%C9%99d%C9%99dir.+%C3%96d%C9%99ni%C5%9Fl%C9%99+ba%C4%9Fl%C4%B1+%C3%A7%C9%99tinliyiniz+v%C9%99+ya+h%C9%99r+hans%C4%B1+sual%C4%B1n%C4%B1z+olarsa%2C+biziml%C9%99+%C9%99laq%C9%99+saxlay%C4%B1n." class="my-btn btn btn-success active" title="' . lang('Razılaşdı') . '" target="_blank">
                    <p><i class="fa fa-whatsapp" aria-hidden="true"></i> 2</p>
                    </a>
                    <a href="https://wa.me/' . $row['phone'] . '?text=%C3%96d%C9%99ni%C5%9Finizi+%C5%9F%C9%99xsi+bank+kartlar%C4%B1n%C4%B1z+il%C9%99+gpp.az+sayt%C4%B1ndan+v%C9%99+ya+%E2%80%9CMilliON%E2%80%9D+v%C9%99+%E2%80%9CEManat%E2%80%9D+%C3%B6d%C9%99ni%C5%9F+terminallar%C4%B1+vasit%C9%99si+il%C9%99+ed%C9%99+bil%C9%99rsiniz." class="my-btn btn btn-success active" title="' . lang('Ödəniş forması') . '" target="_blank">
                    <p><i class="fa fa-whatsapp" aria-hidden="true"></i> 3</p>
                    </a>
                    <a href="https://wa.me/' . $row['phone'] . '?text=Salam+h%C3%B6rm%C9%99tli+m%C3%BC%C5%9Ft%C9%99ri.+' . $row['car_id'] . '+DQN-li+n%C9%99qliyyat+vasit%C9%99sinin+bu+ilki+s%C4%B1%C4%9Forta+haqq%C4%B1n%C4%B1+hesablaya+bilm%C9%99yimiz+%C3%BC%C3%A7%C3%BCn+z%C9%99hm%C9%99t+olmasa+s%C3%BCr%C3%BCc%C3%BCl%C3%BCk+v%C9%99siq%C9%99nizin+%C5%9F%C9%99klini+v%C9%99+ya+seriya+v%C9%99+n%C3%B6mr%C9%99sini+yaz%C4%B1l%C4%B1+%C5%9F%C9%99kild%C9%99+biz%C9%99+g%C3%B6nd%C9%99rin." class="my-btn btn btn-success active" title="' . lang('SV Sorğusu') . '" target="_blank">
                    <p><i class="fa fa-whatsapp" aria-hidden="true"></i> 4</p>
                    </a>
                    <a href="https://wa.me/' . $row['phone'] . '?text=%E2%9C%85+M%C9%99lumat+verm%C9%99k+ist%C9%99rdim+ki%2C+G%C9%99l%C9%99n+aydan+etibar%C9%99n+%C4%B0cbari+S%C4%B1%C4%9Fortas%C4%B1+olmayan+M%C9%99nzill%C9%99r%C9%99++v%C9%99+F%C9%99rdi+ya%C5%9Fay%C4%B1%C5%9F+evl%C9%99rin%C9%99+C%C9%99rim%C9%99+t%C9%99tbiq+olunaca%C4%9F%C4%B1+g%C3%B6zl%C9%99nilir.%0D%0A%E2%9C%85+Qeyd+edim+ki%2C+M%C9%99nzill%C9%99rinizin+icbari+S%C4%B1%C4%9Fortas%C4%B1n%C4%B1+tez+v%C9%99+operativ+%C5%9F%C9%99kild%C9%99+bizd%C9%99n+%C9%99ld%C9%99+ed%C9%99+bil%C9%99rsiniz.%0D%0A%0D%0A%E2%9C%85+Qiym%C9%99t%3A+Bak%C4%B1+%C5%9F%C9%99h%C9%99ri+%C3%BCzr%C9%99%3A+50+AZN%2C+G%C9%99nc%C9%99%2C+Sumqay%C4%B1t%2C+Nax%C3%A7%C4%B1van+%C3%BCzr%C9%99%3A+40+AZN%2C+Dig%C9%99r+%C5%9F%C9%99h%C9%99r+v%C9%99+rayonlar+%C3%BCzr%C9%99%3A+30+AZN+t%C9%99%C5%9Fkil+edir.%0D%0A%0D%0A%E2%9C%8D%EF%B8%8F+%C6%8Fld%C9%99+etm%C9%99k+%C3%BC%C3%A7%C3%BCn%3A+Evin+s%C9%99n%C9%99dinin+%28%C3%A7%C4%B1xar%C4%B1%C5%9F%2C+m%C3%BCqavil%C9%99+v%C9%99.s%29+v%C9%99+s%C9%99n%C9%99dd%C9%99+ad%C4%B1+qeyd+edil%C9%99n+%C5%9F%C9%99xsin+%C5%9E%C9%99xsiyy%C9%99t+v%C9%99siq%C9%99sinin+%C5%9F%C9%99kill%C9%99rini+biz%C9%99+g%C3%B6nd%C9%99rin+s%C4%B1%C4%9Fortan%C4%B1z%C4%B1+r%C9%99smil%C9%99%C5%9Fdir%C9%99k." class="my-btn btn btn-success active" title="' . lang('Əmlak sığortası') . '" target="_blank">
                    <p><i class="fa fa-whatsapp" aria-hidden="true"></i> 5</p>
                    </a>
                </div>
                <div class="view-actions">
                    <button class="view-btn active" title="' . lang('Status ver') . '" data-bs-toggle="modal" data-bs-target=".modalAddStatus">
                    <p>' . lang('Status ver') . '</p>
                    </button>
                    <button class="view-btn" title="' . lang('Statuslara bax') . ' (' . $countStatus . ')" data-bs-toggle="modal" data-bs-target=".modalShowStatus">
                    <p>' . lang('Statuslara bax') . ' (' . $countStatus . ')</p>
                    </button>';

      //if($lastStatus != 23){

      $output_grid .= '

                        <button style="display: none;" id="' . $row['id'] . '" module="customers" type="button" class="my-btn active inlineSaveButton inlineSave-' . $row['id'] . ' btn btn-success" title="' . lang("Yadda Saxla") . '">
                            <i class="fa fa-check" aria-hidden="true"></i>
                        </button>
                        <button id="' . $row['id'] . '" type="button" class="my-btn active inlineEditButton inlineEdit-' . $row['id'] . ' btn btn-primary" title="' . lang("Düzəliş et") . '">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                        </button>

                      ';

      //}

      $output_grid .= '

                </div>
            </div>';

      if ($getCustomerCount > 1) {

        $output_grid .= '<div class="projects-status" style="margin-top: 20px;">';


        $sqlGetAnotherCustomer = mysqli_query($db, $query);
        while ($rowGetAnotherCustomer = mysqli_fetch_array($sqlGetAnotherCustomer)) {

          if ($rowGetAnotherCustomer['id'] == $getCustomer['id']) {
            $multiBg = "background-color: red;";
          } else {
            $multiBg = "";
          }

          echo '

                      <a style="' . $multiBg . '" class="my-btn btn btn-primary active" href="/call/' . $rowGetAnotherCustomer['car_id'] . '" title="' . $rowGetAnotherCustomer['car_id'] . '">
                        <p>' . $rowGetAnotherCustomer['car_id'] . '</p>
                      </a>

                      ';
        }


        $output_grid .= '</div>';
      }

      $end_date_car = date("Y-m-d", strtotime($row['end_date']));

      $output_grid .= '

            <div class="invoice" style="overflow: hidden; overflow-y: auto;">

                <div class="invoice-body">

                    <div class="panel panel-default">
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <th>' . lang('Ad, Soyad') . '</th>
                            <th class="text-center colfix">' . lang('Xidmət') . '</th>
                            <th class="text-center colfix">' . lang('Telefon') . '</th>
                            <th class="text-center colfix">' . lang('Pin kod') . '</th>
                            <th class="text-center colfix">' . lang('Ş/Seriya') . '</th>
                            <th class="text-center colfix">' . lang('Seriya') . '</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td class="editable-' . $row['id'] . ' copycolor pointer" data-column="name"><span onclick="copyToClipboard(`' . $row['name'] . '`);">' . $row['name'] . '</span></td>
                            <td class="text-right">İcbari avto</td>
                            <td class="editable-' . $row['id'] . ' copycolor pointer" data-column="phone"><span onclick="copyToClipboard(`' . substr($row['phone'], 5) . '`);">' . $row['phone'] . '</span></td>
                            <td class="editable-' . $row['id'] . ' copycolor pointer" data-column="pin"><span onclick="copyToClipboard(`' . $row['pin'] . '`);">' . $row['pin'] . '</span></td>
                            <td class="editable-' . $row['id'] . ' copycolor pointer" data-column="pin_serial"><span onclick="copyToClipboard(`' . $row['pin_serial'] . '`);">' . $row['pin_serial'] . '</span></td>
                            <td class="editable-' . $row['id'] . ' copycolor pointer" data-column="serial"><span onclick="copyToClipboard(`' . $row['serial'] . '`);">' . $row['serial'] . '</span></td>
                        </tr>
                        </tbody>
                    </table>
                    </div>

                    <div class="panel panel-default">
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <td class="text-center col-xs-1">' . lang('DQN') . '</td>
                            <td class="text-center col-xs-1">' . lang('Seriya') . '</td>
                            <td class="text-center col-xs-1">' . lang('Marka') . '</td>
                            <td class="text-center col-xs-1">' . lang('Model') . '</td>
                            <td class="text-center col-xs-1">' . lang('Növ') . '</td>
                            <td class="text-center col-xs-1">' . lang('Həcm') . '</td>
                            <td class="text-center col-xs-1">' . lang('İl') . '</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th class="text-center rowtotal mono copycolor pointer"><span onclick="copyToClipboard(`' . $row['car_id'] . '`);">' . $row['car_id'] . '</span></th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . ' copycolor pointer" data-column="car_pin"><span onclick="copyToClipboard(`' . $row['car_pin'] . '`);">' . $row['car_pin'] . '</span></th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . '" data-column="make">' . $row['make'] . '</th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . '" data-column="model">' . $row['model'] . '</th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . '" data-column="type">' . $row['type'] . '</th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . '" data-column="engineType">' . $row['engineType'] . '</th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . '" data-column="caryear">' . $row['caryear'] . '</th>
                        </tr>
                        </tbody>
                    </table>
                    </div>

                    <div class="panel panel-default">
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <td class="text-center col-xs-1">' . lang('Sığorta Şirkəti') . '</td>
                            <td class="text-center col-xs-1">' . lang('Şəhadətnamə') . '</td>
                            <td class="text-center col-xs-1">' . lang('Sığorta Haqqı') . '</td>
                            <td class="text-center col-xs-1">' . lang('İSB Qiyməti') . '</td>
                            <td class="text-center col-xs-1">' . lang('B/M Əmsalı') . '</td>
                            <td class="text-center col-xs-1">' . lang('Bitmə Tarixi') . '</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . ' copycolor pointer" data-column="company"><span onclick="copyToClipboard(`' . $getCompany['title'] . '`);">' . $getCompany['title'] . '</span></th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . ' copycolor pointer" data-column="identification"><span onclick="copyToClipboard(`' . $row['identification'] . '`);">' . $row['identification'] . '</span></th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . ' copycolor pointer" data-column="premium"><span onclick="copyToClipboard(`' . $row['premium'] . '`);">' . $row['premium'] . '</span></th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . ' copycolor pointer" data-column="valuesp"><span onclick="copyToClipboard(`' . $row['valuesp'] . '`);">' . $row['valuesp'] . '</span></th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . ' copycolor pointer" data-column="bm"><span onclick="copyToClipboard(`' . $row['bm'] . '`);">' . $row['bm'] . '</span></th>
                            <th class="text-center rowtotal mono editable-' . $row['id'] . ' copycolor pointer" data-column="end_date" style="background: ' . (strtotime($row['end_date']) < strtotime(date('Y-m-d')) ? 'red' : 'inherit') . ';"><span onclick="copyToClipboard(`' . date("Y-m-d", strtotime($row['end_date'])) . '`);">' . $end_date_car . '</span></th>
                        </tr>
                        </tbody>
                    </table>
                    </div>

                    <div class="panel panel-default">
                    <table class="table table-bordered table-condensed">
                        <thead>
                        <tr>
                            <td class="text-center col-xs-1">' . lang('Status') . '</td>
                            <td class="text-center col-xs-1">' . lang('Seçilən şirkət') . '</td>
                            <td class="text-center col-xs-1">' . lang('Ödəniş Kodu') . '</td>
                            <td class="text-center col-xs-1">' . lang('Kodun müddəti') . '</td>
                            <td class="text-center col-xs-1">' . lang('Razılaşdığı Qiymət') . '</td>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th class="text-center rowtotal mono">' . $status . '</th>
                            <th class="text-center rowtotal mono copycolor pointer"><span onclick="copyToClipboard(`' . $companyId . '`);">' . $companyId . '</span></th>
                            <th class="text-center rowtotal mono copycolor pointer"><span onclick="copyToClipboard(`' . $paycode . '`);">' . $paycode . '</span></th>
                            <th class="text-center rowtotal mono">' . ((!empty($paycode)) ? '' . $hourdiff . '' : "") . '</th>
                            <th class="text-center rowtotal mono copycolor pointer"><span onclick="copyToClipboard(`' . $agreePrice . '`);">' . $agreePrice . '</span></th>
                        </tr>
                        </tbody>
                    </table>
                    </div>

                    <div class="row">
                      <div class="col-12">
                          <div class="panel panel-default">
                          <div class="panel-body">
                              <i>' . lang('Qeydlər') . '</i>
                              <hr style="margin:3px 0 5px" />
                              <p class="editable-' . $row['id'] . '" data-column="note">' . $row['note'] . '</p>
                          </div>
                          </div>
                      </div>
                      <div class="col-12">
                          <div class="panel panel-default">
                          <div class="panel-body">
                              <i>' . lang('İstifadəçi') . '</i>
                              <hr style="margin:3px 0 5px" />
                              <p class="editableSelect-' . $row['id'] . '" data-column="createdby">' . $getUser['name'] . ' ' . $getUser['surname'] . '</p>
                          </div>
                          </div>
                      </div>
                    </div>

                </div>

            </div>

            <script>$("#car_id_input").val("' . $row['car_id'] . '");</script>
            <script>$("#addFromJS").val("' . $row['car_id'] . '");</script>
            <script>
            function copyToClipboard(element) {
              var $temp = $("<input>");
              $("body").append($temp);
              $temp.val(element).select();
              document.execCommand("copy");
              $temp.remove();
            }
        
            $(".copycolor").click(function(e){
              $(this).css("color", "#00A46C");
            });
            $("#thisChangeUser").val("' . $row['createdby'] . '");
            </script>
        
        ';
    }
  } else if ($_GET['type'] == 2) { // customers table

    $output .= '
    <thead>
      <tr>
        <th>#</th>
        <th>' . lang('Ad Soyad') . '</th>
        <th>' . lang('DQN') . '</th>
        <th>' . lang('Pin') . '</th>
        <th>' . lang('Seriya') . '</th>
        <th>' . lang('Şəhadətnamə') . '</th>
        <th>' . lang('Telefon') . '</th>
        <th>' . lang('Bitmə tarixi') . '</th>
        <th></th>
      </tr>
      </thead>
        <tbody>
    ';

    foreach ($result as $row) {

      $output .= '
              <tr id="data-' . $row['id'] . '" class="forProgress">
              
                <td>' . $row['id'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="name">' . $row['name'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="car_id"><a href="/call/' . $row['car_id'] . '">' . $row['car_id'] . '</a></td>
                <td class="editable-' . $row['id'] . '" data-column="pin">' . $row['pin'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="serial">' . $row['serial'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="identification">' . $row['identification'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="phone">' . $row['phone'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="end_date">' . $row['end_date'] . '</td>
                <td>
                  <div class="btn-group" role="group" aria-label="Basic example">
                          <button style="display: none;" id="' . $row['id'] . '" module="customers" type="button" class="inlineSaveButton inlineSave-' . $row['id'] . ' btn btn-primary" title="' . lang("Yadda Saxla") . '">
                            <i class="fa fa-check" aria-hidden="true"></i>
                          </button>
                          <button id="' . $row['id'] . '" type="button" class="inlineEditButton inlineEdit-' . $row['id'] . ' btn btn-primary" title="' . lang("Düzəliş et") . '">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                          </button>
                          <button id="' . $row['id'] . '" type="button" module="customers" class="delete delete-' . $row['id'] . ' btn btn-primary" title="Sil">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                          </button>
                    </div>
                </td>
      ';

      $output .= '</tr>';
    }
  } else if ($_GET['type'] == 22) { // companies table

    $output .= '
    <thead>
      <tr>
        <th>#</th>
        <th>' . lang('Ad Soyad') . '</th>
        <th>' . lang('DQN') . '</th>
        <th>' . lang('Pin') . '</th>
        <th>' . lang('Seriya') . '</th>
        <th>' . lang('Şəhadətnamə') . '</th>
        <th>' . lang('Telefon') . '</th>
        <th>' . lang('Bitmə tarixi') . '</th>
        <th></th>
      </tr>
      </thead>
        <tbody>
    ';

    foreach ($result as $row) {

      $output .= '
              <tr id="data-' . $row['id'] . '" class="forProgress">
              
                <td>' . $row['id'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="name">' . $row['name'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="car_id"><a href="/call/' . $row['car_id'] . '">' . $row['car_id'] . '</a></td>
                <td class="editable-' . $row['id'] . '" data-column="pin">' . $row['pin'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="serial">' . $row['serial'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="identification">' . $row['identification'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="phone">' . $row['phone'] . '</td>
                <td class="editable-' . $row['id'] . '" data-column="end_date">' . $row['end_date'] . '</td>
                <td>
                  <div class="btn-group" role="group" aria-label="Basic example">
                          <button style="display: none;" id="' . $row['id'] . '" module="customers" type="button" class="inlineSaveButton inlineSave-' . $row['id'] . ' btn btn-primary" title="' . lang("Yadda Saxla") . '">
                            <i class="fa fa-check" aria-hidden="true"></i>
                          </button>
                          <button id="' . $row['id'] . '" type="button" class="inlineEditButton inlineEdit-' . $row['id'] . ' btn btn-primary" title="' . lang("Düzəliş et") . '">
                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                          </button>
                          <button id="' . $row['id'] . '" type="button" module="customers" class="delete delete-' . $row['id'] . ' btn btn-primary" title="Sil">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                          </button>
                    </div>
                </td>
      ';

      $output .= '</tr>';
    }
  } else if ($_GET['type'] == 3) { // chat

    $output_grid = '';

    foreach ($result as $row) {

      $getUser = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['createdby']));

      $output_grid .= '

        <div class="message-box">
          <img src="/assets/profile.png" alt="profile image">
          <div class="message-content">
            <div class="message-header">
              <div class="name">' . $getUser['name'] . ' ' . $getUser['surname'] . '</div>
            </div>
            <p class="message-line">' . $row['message'] . '</p>
            <p class="message-line time">
              ' . date("d.m.Y H:i", strtotime($row['created'])) . '
            </p>
          </div>
        </div>
      
      ';
    }
  } else if ($_GET['type'] == 4) { // users table

    $output .= '
    <thead class="table-light">
      <tr>
        <th style="width:60px;">#</th>
        <th style="width:60px;"></th>
        <th>' . lang('Ad Soyad') . '</th>
        <th>' . lang('İstifadəçi adı') . '</th>
        <th>' . lang('Rol') . '</th>
        <th>' . lang('Status') . '</th>
        <th>' . lang('Son giriş') . '</th>
        <th style="width:180px;text-align:right;">' . lang('Əməliyyatlar') . '</th>
      </tr>
    </thead>
    <tbody>
    ';

    foreach ($result as $row) {

      // ===== Status badge =====
      if ($row['status'] == 0) {
        $statusBadge      = '<span class="badge bg-secondary"><i class="fa fa-circle-o"></i> Passiv</span>';
        $datastatus       = "1";
        $datastatusTitle  = "Aktiv et";
        $datastatusIcon   = "unlock-alt";
        $datastatusBtnCls = "btn-outline-success";
      } else {
        $statusBadge      = '<span class="badge bg-success"><i class="fa fa-check-circle"></i> Aktiv</span>';
        $datastatus       = "0";
        $datastatusTitle  = "Passiv et";
        $datastatusIcon   = "lock";
        $datastatusBtnCls = "btn-outline-warning";
      }

      // ===== Rol badge =====
      if ($row['groupId'] == 1) {
        $roleBadge = '<span class="badge bg-danger"><i class="fa fa-shield"></i> Admin</span>';
      } else if ($row['groupId'] == 2) {
        $roleBadge = '<span class="badge bg-primary"><i class="fa fa-user-circle"></i> Menecer</span>';
      } else if ($row['groupId'] == 3) {
        $roleBadge = '<span class="badge bg-info text-dark"><i class="fa fa-headphones"></i> Operator</span>';
      } else {
        $roleBadge = '<span class="badge bg-light text-dark">—</span>';
      }

      // ===== Avatar =====
      $initials = strtoupper(mb_substr($row['name'], 0, 1) . mb_substr($row['surname'], 0, 1));
      $avatarColors = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#6f42c1','#fd7e14'];
      $avatarBg = $avatarColors[$row['id'] % count($avatarColors)];
      $avatar = '<div style="width:38px;height:38px;border-radius:50%;background:' . $avatarBg . ';color:#fff;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:14px;">' . $initials . '</div>';

      // ===== Last seen =====
      if (!empty($row['lastseen']) && $row['lastseen'] != '0000-00-00 00:00:00') {
        $lastseen = '<small class="text-muted"><i class="fa fa-clock-o"></i> ' . date("d.m.Y H:i", strtotime($row['lastseen'])) . '</small>';
      } else {
        $lastseen = '<small class="text-muted">—</small>';
      }

      // safe для data-атрибутов
      $safeName    = htmlspecialchars($row['name'], ENT_QUOTES);
      $safeSurname = htmlspecialchars($row['surname'], ENT_QUOTES);
      $safeEmail   = htmlspecialchars($row['email'], ENT_QUOTES);

      $output .= '
        <tr id="data-' . $row['id'] . '" class="forProgress align-middle">
          <td><span class="text-muted">#' . $row['id'] . '</span></td>
          <td> </td>
          <td>
            <div style="font-weight:600;">' . $row['name'] . '</div>
            <div class="text-muted" style="font-size:12px;">' . $row['surname'] . '</div>
          </td>
          <td><i class="fa fa-user-o text-muted"></i> ' . $row['email'] . '</td>
          <td>' . $roleBadge . '</td>
          <td class="statusLabel-' . $row['id'] . '">' . $statusBadge . '</td>
          <td>' . $lastseen . '</td>
          <td style="text-align:right;">

<div class="btn-group" role="group">
              <button type="button"
                      class="btn btn-outline-primary openEditUser"
                      data-id="' . $row['id'] . '"
                      data-name="' . $safeName . '"
                      data-surname="' . $safeSurname . '"
                      data-email="' . $safeEmail . '"
                      data-group="' . $row['groupId'] . '"
                      data-active="' . $row['status'] . '"
                      title="' . lang("Düzəliş et") . '"
                      style="padding:8px 14px;font-size:15px;">
                <i class="fa fa-pencil"></i>
              </button>
              <button type="button"
                      class="btn btn-outline-danger openDeleteUser"
                      data-id="' . $row['id'] . '"
                      data-name="' . $safeName . ' ' . $safeSurname . '"
                      title="Sil"
                      style="padding:8px 14px;font-size:15px;">
                <i class="fa fa-trash"></i>
              </button>
            </div>

          </td>
        </tr>
      ';
    }} else if ($_GET['type'] == 5) { // accounting

    $output .= '
<thead class="table-light">
  <tr>
    <th>#</th>
    <th>Sığorta növü</th>
    <th>Şirkət</th>
    <th>Şəhadətnamə</th>
    <th>DQN</th>
    <th>Agent</th>

    <th class="text-end">Baza qiymət</th>
    <th class="text-end">Razı qiymət</th>
    <th class="text-end">Sığorta haqqı</th>

    <th class="text-end">Agent %</th>
    <th class="text-end">Agent qazancı</th>

    <th>Agent ödənişi</th>
    <th>Yazılma tarixi</th>
        
        ' . (($userGroup == 1 || $userGroup == 2) ? '
        <th>SIGORTA SIRKETI KOMISYA FAIZI</th>
        <th>KOMISYA MEBLEGI</th>
        <th>SIGORTA SIRKETINDEN KOMISYANIN ODENILME TARIXI</th>
        <th>GELIR</th>
        ' : "") . '
        
        <th>ODENIS TIPI</th>
        <th>KART HESABATI</th>
        <th>MUSTERININ ODENIS MEBLEGI</th>
        <th>MUSTERININ QALIQ BORCU QALIQ BORC</th>
        <th>MÜŞTERİ ÖDENİŞ FORMASI</th>
        <th>MUSTERININ ODENIS TARIXI</th>
        <th>QEYD</th>
        <th>ÇATDIRILMA</th>
        <th></th>
      </tr>
      </thead>
        <tbody>
    ';

    $shown_cars = array(); // Təkrarlanan avtomobil nömrələrini izləmək üçün

    error_reporting(E_ALL);



    foreach ($result as $row) {

      // Əgər bu avtomobil nömrəsi artıq göstərilibsə, keç
      if (in_array($row['car_id'], $shown_cars)) {
        continue;
      }

      $shown_cars[] = $row['car_id']; // Bu avtomobil nömrəsini göstərildi kimi qeyd et

      $createdDateYear = date("Y", strtotime($row['created']));

      $sqlGetUser = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['createdby']));
      $sqlGetParamItems = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = " . $row['type']));

      $getUserAgree = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['agreeUser']));
      $getSupplier = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM companies WHERE id = " . $row['companyId']));

      $getPayments = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment FROM payments WHERE deletedby = 0 AND fromAccount = '" . $row['car_id'] . "' AND YEAR(created) = '$createdDateYear'"));

      $incomingCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment FROM payments WHERE deletedby = 0 AND toAccount = 'Özü ödədi' AND fromAccount = '" . $row['car_id'] . "' AND YEAR(created) = '$createdDateYear'"));
      $incomingCustomerFrom = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment, toAccount as cardName, paydate as paydate FROM payments WHERE deletedby = 0 AND fromAccount = '" . $row['car_id'] . "' AND toAccount != 'Özü ödədi' AND YEAR(created) = '$createdDateYear'"));

      $incomingWeTo = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment, fromAccount as cardName, paydate FROM payments WHERE deletedby = 0 AND toAccount = '" . $row['car_id'] . "' AND YEAR(created) = '$createdDateYear'"));
      $incomingOwn = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment, toAccount as cardName, paydate FROM payments WHERE deletedby = 0 AND fromAccount = '" . $row['car_id'] . "' AND toAccount = 'Özü ödədi' AND YEAR(created) = '$createdDateYear'"));

      if ($row['confirmed'] == 1) {
        $confirmed = '<i class="fa fa-check-circle-o" style="color: blue;" aria-hidden="true" title=' . lang('Təsdiqlənib') . '></i>';
      } else {
        $confirmed = "";
      }

      $debt = round(($row['agreePrice'] - $getPayments['totalPayment']), 2);

      $show = 1;

      if ($debt < 0) {
        $debt = 0;
      }

      if ($_POST['params'] == 'Borclular' && $debt == 0) {
        $show = 0;
      }

      if ($incomingCustomerFrom['paydate'] != '0000-00-00') {
        $paydate = date("d.m.Y", strtotime($incomingCustomerFrom['paydate']));
      } else {
        $paydate = '';
      }

      if ($paydate == '01.01.1970') {
        $paydate = '';
      }

      if (empty($incomingWeTo['cardName'])) {
        $incomingWeTo['cardName'] = 'ÖZÜ ÖDƏDİ';

        if ($incomingOwn['paydate'] != '0000-00-00') {
          $paydate = date("d.m.Y", strtotime($incomingOwn['paydate']));
        } else {
          $paydate = '';
        }

        if ($paydate == '01.01.1970') {
          $paydate = '';
        }

        if ($_POST['params'] == 'Borclular') {
          $show = 0;
        }
      }

      $incomingAll = $incomingCustomerFrom['totalPayment'] + $incomingCustomer['totalPayment'];



      // test

      if ($show == 1) {

        $output .= '
                <tr id="data-' . $row['id'] . '" class="forProgress" style="' . $bgcolor . '">
                  <td>' . $row['id'] . ' ' . $confirmed . '</td>
                  <td>İCBARİ</td>
                  <td>' . $getSupplier['title'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="identification">' . $row['identification'] . '</td>
                  <td><a href="/call/' . $row['car_id'] . '" target="_blank">' . $row['car_id'] . '</a></td>
                  <td>' . $getUserAgree['name'] . ' ' . $getUserAgree['surname'] . '</td>

                  <td class="editable-' . $row['id'] . '" data-column="defaultPrice">' . $row['defaultPrice'] . '</td>

                  <td class="editable-' . $row['id'] . '" data-column="agreePrice">' . $row['agreePrice'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="price">' . $row['price'] . '</td>
                  <td></td>
                  <td class="text-end editable-' . $row['id'] . '" data-column="userEarn">
<strong>' . number_format($row['userEarn'], 2, '.', ' ') . '</strong>
</td>
                  <td></td>
                  <td class="editable-' . $row['id'] . '" data-column="write_date">' . date("d.m.Y", strtotime($row['write_date'])) . '</td>
                  ' . (($userGroup == 1 || $userGroup == 2) ? '
                  <td class="editable-' . $row['id'] . '" data-column="companyEarnPercent">' . $row['companyEarnPercent'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="companyEarn">' . $row['companyEarn'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="companyEarnDate">' . $row['companyEarnDate'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="companyEarnProfit">' . $row['companyEarnProfit'] . '</td>
                  ' : "") . '
                  
                  <td>' . $incomingWeTo['cardName'] . '</td>
                  
                  <td>' . $incomingWeTo['totalPayment'] . '</td>
                  <td>' . $incomingAll . '</td>
                  <td>' . $debt . '</td>
                  <td>' . $incomingCustomerFrom['cardName'] . '</td>
                  <td>' . $paydate . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="content">' . $row['content'] . '</td>
                  <td></td>
                  <td>
                    ' . (($userGroup == 1 || $userGroup == 2) ? '
                      <button style="display: none;" id="' . $row['id'] . '" module="call_status" type="button" class="inlineSaveButton inlineSave-' . $row['id'] . ' btn btn-primary" title="' . lang("Yadda Saxla") . '">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </button>
                      <button id="' . $row['id'] . '" type="button" class="inlineEditButton inlineEdit-' . $row['id'] . ' btn btn-primary" title="' . lang("Düzəliş et") . '">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                      </button>
                    ' : "") . '
                    ' . (($userGroup == 1 || $userGroup == 2) ? '
                      <button id="' . $row['id'] . '" type="button" module="call_status" class="delete delete-' . $row['id'] . ' btn btn-primary" title="Sil">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                      </button>
                    ' : "") . '
                  </td>
          ';
      }

      $output .= '</tr>';
    }
  } else if ($_GET['type'] == 6) { // finance category
    $output_grid = '';

    if ($total_data > 0) {
      foreach ($result as $row) {

        $output_grid .= '
                  <div class="posgrid search_category" data-id="' . $row['id'] . '">
                    <span class="dynatree-node dynatree-folder dynatree-has-children dynatree-exp-c dynatree-ico-cf">
                      <span class="dynatree-icon"></span>
                      <a href="#" class="dynatree-title">' . $row['title'] . '</a>
                    </span>
                  </div>
                ';
      }
    } else {
      $output_grid .= lang('Məlumat Tapılmadı.');
    }
  } else if ($_GET['type'] == 7) { // finaccounts
    $output_grid = '';

    if ($total_data > 0) {
      foreach ($result as $row) {

        $getIncomes = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as amount FROM payments WHERE toAccount = '" . $row['title'] . "' AND deletedby = 0"));
        $getOutgoing = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as amount FROM payments WHERE fromAccount = '" . $row['title'] . "' AND deletedby = 0"));
        $balance = round($getIncomes['amount'] - $getOutgoing['amount'], 2);

        if ($row['title'] == 'Özü ödədi') {
          $balance = '';
        } else {
          $balance = '(' . $balance . ')';
        }

        $output_grid .= '
          <button data-id="' . $row['title'] . '" style="width: auto; margin: 5px;" type="button" class="search_account btn btn-primary" data-toggle="modal" data-target=".modalAddIncome">' . $row['title'] . ' ' . $balance . '</button>
        ';
      }
    } else {
      $output_grid .= lang('Məlumat Tapılmadı.');
    }
  } else if ($_GET['type'] == 8) { // finance payments
    $output = '
    <table id="dynamic_content" class="table table-hover">
    <thead>
      <tr>
        <th>№</th>
        <th>Hardan</th>
        <th>Hara</th>
        <th>Şəhadətnamə nömrəsi</th>
        <th>Açıqlama</th>
        <th>Məbləğ</th>
        <th>Tarix</th>
        <th>Kateqoriya</th>
        <th>Hesabat ID</th>
        <th>İstifadəçi</th>
        <th></th>
      </tr>
      </thead>
        <tbody>
    ';

    if ($total_data > 0) {
      foreach ($result as $row) {

        $getUser = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['createdby']));


        if (empty($row['orderId'])) {
          $getOrder = mysqli_fetch_array(mysqli_query($db, "SELECT identification FROM call_status WHERE identification != '' AND car_id = '" . $row['fromAccount'] . "' ORDER by id DESC LIMIT 1"));
          $row['orderId'] = $getOrder['identification'];
        }

        $getConfirmed = mysqli_fetch_array(mysqli_query($db, "SELECT confirmed FROM orders WHERE id = " . $row['teslimId']));
        $confirmedAct = $getConfirmed['confirmed'];

        if ($confirmedAct == 1) {
          $confirmed = '<i class="fa fa-check-circle-o" style="color: blue;" aria-hidden="true" title=' . lang('Təsdiqlənib') . '></i>';
          $confirmedAct = 1;
        } else {
          $confirmed = "";
        }

        $output .= '
                <tr id="data-' . $row['id'] . '" class="forProgress">
                  <td>' . $row['id'] . ' ' . $confirmed . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="fromAccount"><a href="/call/' . $row['fromAccount'] . '" target="_blank">' . $row['fromAccount'] . '</a></td>
                  <td class="editable-' . $row['id'] . '" data-column="toAccount"><a href="/call/' . $row['toAccount'] . '" target="_blank">' . $row['toAccount'] . '</a></td>
                  <td class="editable-' . $row['id'] . '" data-column="orderId">' . $row['orderId'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="title">' . $row['title'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="amount">' . number_format($row['amount'], 2, ',', '.') . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="paydate">' . date("d.m.Y", strtotime($row['paydate'])) . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="category">' . $row['category'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="teslimId">' . $row['teslimId'] . '</td>
                  <td>' . $getUser['name'] . ' ' . $getUser['surname'] . '</td>
                  <td>
                    <div class="btn-group" role="group" aria-label="Basic example">
                    ' . (($confirmedAct == 0 || $userGroup == 1) && ($userGroup == 1 || $userGroup == 2 || $user_id == 14) ? '
                      <button style="display: none;" id="' . $row['id'] . '" module="payments" type="button" class="inlineSaveButton inlineSave-' . $row['id'] . ' btn btn-primary" title="' . lang("Yadda Saxla") . '">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </button>
                      <button id="' . $row['id'] . '" type="button" class="inlineEditButton inlineEdit-' . $row['id'] . ' btn btn-primary" title="' . lang("Düzəliş et") . '">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                      </button>
                    ' : "") . '
                    ' . (($confirmedAct == 0 || $userGroup == 1) && ($userGroup == 1 || $userGroup == 2 || $user_id == 14) ? '
                      <button id="' . $row['id'] . '" type="button" module="payments" class="delete delete-' . $row['id'] . ' btn btn-primary" title="Sil">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                      </button>
                    ' : "") . '
                      </div>
                  </td>
                </tr>
                ';
      }
    } else {
      $output .= '
      <tr>
        <td colspan="11" align="center">' . lang('Məlumat Tapılmadı.') . '</td>
      </tr>
      ';
    }
  } else if ($_GET['type'] == 9) { // statuses

    $output .= '
    <thead>
      <tr>
        <th>#</th>
        <th>DQN</th>
        <th>Status</th>
        <th>İstifadəçi</th>
        <th>Tarix</th>
        <th>Qeyd</th>
        <th></th>
      </tr>
      </thead>
        <tbody>
    ';

    foreach ($result as $row) {

      $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['createdby']));
      $getParamitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = " . $row['type'] . ""));

      if (!empty($row['companyId'])) {
        $selectedCompany = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM companies WHERE id = " . $row['companyId']));
      }

      $status = '<span style="display: block; padding: 5px; background-color: green; width: 100%; color: #fff;">' . $getParamitem['title'] . '</span>';

      $output .= '
                <tr id="data-' . $row['id'] . '" class="forProgress" style="' . $bgcolor . '">
                  <td>' . $row['id'] . '</td>
                  <td><a href="/call/' . $row['car_id'] . '" target="_blank">' . $row['car_id'] . '</a></td>
                  <td>' . $status . '</td>
                  <td>' . $getCreated['name'] . ' ' . $getCreated['surname'] . '</td>
                  <td>' . $row['created'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="content">' . $row['content'] . '</td>
<td>
                    ' . (($user_id == 2) ? '
                      <button style="display: none;" id="' . $row['id'] . '" module="call_status" type="button" class="inlineSaveButton inlineSave-' . $row['id'] . ' btn btn-primary" title="' . lang("Yadda Saxla") . '">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </button>
                      <button id="' . $row['id'] . '" type="button" class="inlineEditButton inlineEdit-' . $row['id'] . ' btn btn-primary" title="' . lang("Düzəliş et") . '">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                      </button>
                      <button id="' . $row['id'] . '" type="button" module="call_status" class="delete delete-' . $row['id'] . ' btn btn-primary" title="Sil">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                      </button>
                    ' : "") . '
                  </td>
          ';

      $output .= '</tr>';
    }
  } else if ($_GET['type'] == 10) { // orders

    $output .= '
    <thead>
      <tr>
        <th>#</th>
        <th>Hesab</th>
        <th>Tarix</th>
        <th>Gəlir</th>
        <th>Xərc</th>
        <th>Qazanc</th>
        <th>Hara ödənilib</th>
        <th>İstifadəçi</th>
        <th>Qeyd</th>
        <th>Status</th>
        <th></th>
      </tr>
      </thead>
        <tbody>
    ';

    foreach ($result as $row) {

      $orderId = $row['id'];

      $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['createdby']));
      $getAmount = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as total FROM payments WHERE teslimId = " . $row['id']));

      if ($row['confirmed'] == 0) {
        $confirmedTitle = 'Təsdiq gözləyir';
      } else {
        $confirmedTitle = 'Təsdiqləndi';
      }

      $totalAmount = 0;

      $queryAmount = "SELECT * FROM payments WHERE fromAccount != 'Özü ödədi' AND fromAccount NOT IN (SELECT title FROM finaccounts) AND category = 'Sığorta ödənişləri' AND toAccount != 'Özü ödədi' AND teslimId = '$orderId' AND deletedby = 0";
      $sqlAmount = mysqli_query($db, $queryAmount);
      while ($rowAmount = mysqli_fetch_array($sqlAmount)) {

        $totalAmount += $rowAmount['amount'];
      }

      $totalCost = 0;

      $queryCost = "SELECT * FROM payments WHERE (category != 'Sığorta ödənişləri') AND teslimId = '$orderId' AND deletedby = 0";
      $sqlCost = mysqli_query($db, $queryCost);
      while ($rowCost = mysqli_fetch_array($sqlCost)) {

        $totalCost += $rowCost['amount'];
      }

      $profit = ($totalAmount - $totalCost);

      $totalAmountFmt = number_format((float)$totalAmount, 2, '.', '');
      $totalCostFmt   = number_format((float)$totalCost, 2, '.', '');
      $profitFmt      = number_format((float)$profit, 2, '.', '');

      $confirmedBadge = ($row['confirmed'] == 0)
        ? '<span class="badge bg-warning text-dark">Təsdiq gözləyir</span>'
        : '<span class="badge bg-success">Təsdiqləndi</span>';

      $output .= '
                <tr id="data-' . $row['id'] . '" class="forProgress" style="' . $bgcolor . '">
                  <td>' . $row['id'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="accountId">' . $row['accountId'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="created">' . date("d.m.Y", strtotime($row['created'])) . '</td>

                  <td><strong>' . $totalAmountFmt . '</strong> AZN</td>
                  <td>' . $totalCostFmt . ' AZN</td>
                  <td><strong>' . $profitFmt . '</strong> AZN</td>
                  <td class="editable-' . $row['id'] . '" data-column="toAccount">' . $row['toAccount'] . '</td>
                  <td>' . $getCreated['name'] . ' ' . $getCreated['surname'] . '</td>
                  
                  <td class="editable-' . $row['id'] . '" data-column="note">' . $row['note'] . '</td>
                  <td>' . $confirmedBadge . '</td>
                  <td>
                    <a href="/call/orderitems/' . $row['id'] . '" class="btn btn-primary" title="' . lang("Bax") . '">
                      <i class="fa fa-eye" aria-hidden="true"></i>
                    </a>
                    ' . (($userGroup == 1 && $row['confirmed'] == 0) ? '
                      <button id="' . $row['id'] . '" module="orders" type="button" class="confirm confirm-' . $row['id'] . ' btn btn-success" title="Təsdiqlə">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </button>
                    ' : "") . '
                    
                      <button style="display: none;" id="' . $row['id'] . '" module="orders" type="button" class="inlineSaveButton inlineSave-' . $row['id'] . ' btn btn-primary" title="' . lang("Yadda Saxla") . '">
                        <i class="fa fa-check" aria-hidden="true"></i>
                      </button>
                      <button id="' . $row['id'] . '" type="button" class="inlineEditButton inlineEdit-' . $row['id'] . ' btn btn-primary" title="' . lang("Düzəliş et") . '">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                      </button>
                      ' . ($row['confirmed'] == 0 && ($userGroup == 1 || $userGroup == 2) ? '
                    ' : "") . '
                    ' . ($row['confirmed'] == 0 && ($userGroup == 1 || $userGroup == 2) ? '
                    ' : "") . '
                      <button id="' . $row['id'] . '" type="button" module="orders" class="delete delete-' . $row['id'] . ' btn btn-primary" title="Sil">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                      </button>
                    
                  </td>
          ';

      $output .= '</tr>';
    }
  } else if ($_GET['type'] == 11) { // current

    $output .= '
    <thead>
      <tr>
        <th>SIĞORTA NÖVÜ</th>
        <th>SIGORTA SIRKETI</th>
        <th>SEHADETNAME NOMRESI</th>
        <th>DQN</th>
        <th>KIM YAZIB</th>
        <th>MUHHERIKE GORE SIGORTA HAQQI</th>
        <th>YAZILMA QIYMETI</th>
        <th>SIĞORTA HAQQI</th>
        <th>YAZILMA TARİXİ</th>
        <th>QEYD</th>
      </tr>
      </thead>
        <tbody>
    ';

    foreach ($result as $row) {

      $sqlGetUser = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['createdby']));
      $sqlGetParamItems = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = " . $row['type']));

      $getUserAgree = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . $row['agreeUser']));
      $getSupplier = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM companies WHERE id = " . $row['companyId']));

      $getPayments = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment FROM payments WHERE deletedby = 0 AND fromAccount = '" . $row['car_id'] . "'"));

      $incomingCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment FROM payments WHERE deletedby = 0 AND toAccount = 'Özü ödədi' AND fromAccount = '" . $row['car_id'] . "'"));
      $incomingCustomerFrom = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment, toAccount as cardName, paydate as paydate FROM payments WHERE deletedby = 0 AND fromAccount = '" . $row['car_id'] . "' AND toAccount != 'Özü ödədi'"));

      $incomingWeTo = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment, fromAccount as cardName, paydate FROM payments WHERE deletedby = 0 AND toAccount = '" . $row['car_id'] . "'"));
      $incomingOwn = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as totalPayment, toAccount as cardName, paydate FROM payments WHERE deletedby = 0 AND fromAccount = '" . $row['car_id'] . "' AND toAccount = 'Özü ödədi'"));

      if ($row['confirmed'] == 1) {
        $confirmed = '<i class="fa fa-check-circle-o" style="color: blue;" aria-hidden="true" title=' . lang('Təsdiqlənib') . '></i>';
      } else {
        $confirmed = "";
      }

      $debt = round(($row['agreePrice'] - $getPayments['totalPayment']), 2);

      if ($debt < 0) {
        $debt = 0;
      }

      $show = 1;

      if ($_POST['params'] == 'Borclular') {

        if ($debt <= 0) {
          $show = 0;
        }
      }

      if ($incomingCustomerFrom['paydate'] != '0000-00-00') {
        $paydate = date("d.m.Y", strtotime($incomingCustomerFrom['paydate']));
      } else {
        $paydate = '';
      }

      if ($paydate == '01.01.1970') {
        $paydate = '';
      }

      if (empty($incomingWeTo['cardName'])) {
        $incomingWeTo['cardName'] = 'ÖZÜ ÖDƏDİ';

        if ($incomingOwn['paydate'] != '0000-00-00') {
          $paydate = date("d.m.Y", strtotime($incomingOwn['paydate']));
        } else {
          $paydate = '';
        }

        if ($paydate == '01.01.1970') {
          $paydate = '';
        }
      }

      $incomingAll = $incomingCustomerFrom['totalPayment'] + $incomingCustomer['totalPayment'];

      if ($show == 1) {

        $output .= '
                <tr id="data-' . $row['id'] . '" class="forProgress" style="' . $bgcolor . '">
                  <td>İCBARİ</td>
                  <td>' . $getSupplier['title'] . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="identification">' . $row['identification'] . '</td>
                  <td><a href="/call/' . $row['car_id'] . '" target="_blank">' . $row['car_id'] . '</a></td>
                  <td>' . $getUserAgree['name'] . ' ' . $getUserAgree['surname'] . '</td>
                  <td class="text-end editable-' . $row['id'] . '" data-column="defaultPrice">
                    ' . number_format($row['defaultPrice'], 2, '.', ' ') . '
                  </td>
                  <td class="text-end editable-' . $row['id'] . '" data-column="agreePrice">
                  <strong>' . number_format($row['agreePrice'], 2, '.', ' ') . '</strong>
                  </td>

                  <td class="text-end editable-' . $row['id'] . '" data-column="price">
' . number_format($row['price'], 2, '.', ' ') . '
</td>
                  <td class="editable-' . $row['id'] . '" data-column="write_date">' . date("d.m.Y", strtotime($row['write_date'])) . '</td>
                  <td class="editable-' . $row['id'] . '" data-column="content">' . $row['content'] . '</td>
          ';
      }

      $output .= '</tr>';
    }
  }

?>

<?php

  //end dynamic
  $total_data = $total_data - $break;
  $all_total = $all_total - $break;

  if ($total_data == 0 && $_GET['type'] != 54) {
    $output .= '
  <style>.totalTable{display: none;}</style>
  <tr>
    <td colspan="100" align="center">' . lang('Məlumat Tapılmadı.') . '</td>
  </tr>
  ';
  }
  $output .= '
  </tbody>
  </table>
  <br />';
  if ($_POST['short'] != 1 && $_POST['showType'] != "report") {
    $end = $start + $limit;
    if ($limit == 9999999999999) {
      $output .= '<div class="row row-xs printno" style="margin: 0;"><div class="col-6"><label>' . lang('Toplam') . ': ' . $total_data . '</label></div>';
    } else {
      $showEnd = ($end > $total_data) ? $total_data : $end;
      $output .= '<div class="row row-xs printno" style="margin: 0;"><div class="col-6"><label>Göstərilir: <b>' . ($start + 1) . ' - ' . $showEnd . '</b> / ' . $total_data . '</label></div>';
    }
    $output .= '
    <div class="col-6 printno">
      <ul class="pagination" style="float: right;">
    ';
    $total_links = ceil($total_data / $limit);
    $previous_link = '';
    $next_link = '';
    $page_link = '';
    if ($total_links > 4) {
      if ($page < 5) {
        for ($count = 1; $count <= 5; $count++) {
          $page_array[] = $count;
        }
        $page_array[] = '...';
        $page_array[] = $total_links;
      } else {
        $end_limit = $total_links - 5;
        if ($page > $end_limit) {
          $page_array[] = 1;
          $page_array[] = '...';
          for ($count = $end_limit; $count <= $total_links; $count++) {
            $page_array[] = $count;
          }
        } else {
          $page_array[] = 1;
          $page_array[] = '...';
          for ($count = $page - 1; $count <= $page + 1; $count++) {
            $page_array[] = $count;
          }
          $page_array[] = '...';
          $page_array[] = $total_links;
        }
      }
    } else {
      for ($count = 1; $count <= $total_links; $count++) {
        $page_array[] = $count;
      }
    }
    if (empty($page_array)) {
      $page_array = [1];
    }
    for ($count = 0; $count < count($page_array); $count++) {
      if ($page == $page_array[$count]) {
        $page_link .= '
        <li class="page-item">
          <a class="page-link active btn btn-primary" href="#">' . $page_array[$count] . ' <span class="sr-only">(current)</span></a>
        </li>
        ';
        $previous_id = $page_array[$count] - 1;
        if ($previous_id > 0) {
          $previous_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="' . $previous_id . '">' . lang('Əvvəlki') . '</a></li>';
        } else {
          $previous_link = '
          <li class="page-item disabled">
            <a class="page-link" href="#">' . lang('Əvvəlki') . '</a>
          </li>
          ';
        }
        $next_id = $page_array[$count] + 1;
        if ($next_id > $total_links) {
          $next_link = '
          <li class="page-item disabled">
            <a class="page-link" href="#">' . lang('Sonrakı') . '</a>
          </li>
            ';
        } else {
          $next_link = '<li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="' . $next_id . '">' . lang('Sonrakı') . '</a></li>';
        }
      } else {
        if ($page_array[$count] == '...') {
          $page_link .= '
          <li class="page-item disabled">
              <a class="page-link" href="#">...</a>
          </li>
          ';
        } else {
          $page_link .= '
          <li class="page-item"><a class="page-link" href="javascript:void(0)" data-page_number="' . $page_array[$count] . '">' . $page_array[$count] . '</a></li>
          ';
        }
      }
    }

    $output .= $previous_link . $page_link . $next_link;
    $output .= '
      </ul>

    </div>
    </div>
    ';
  }
  if ($_GET['type'] == 1 || $_GET['type'] == 3 || $_GET['type'] == 6 || $_GET['type'] == 7) {
    echo $output_grid;
  } else {
    echo $output;
  }

  if ($user_id == 1) {
    echo $query . ' - ' . $fetchMethod;
  }
}

?>