<?php include('inc/header.php'); ?>
<?php
$orderId = (int)$_GET['id'];
$getOrderOrg = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM orders WHERE id = '$orderId'"));
?>

<div class="projects-section">
    <style>
        .report-wrap {
            max-width: 1400px;
            margin: 0 auto;
        }

        .report-title {
            margin-bottom: 20px;
        }

        .report-title h3 {
            margin-bottom: 8px;
            font-size: 22px;
            font-weight: 700;
        }

        .finance-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .04);
            margin-bottom: 24px;
            background: #fff;
        }

        .finance-card .card-header {
            background: #f8f9fa;
            font-weight: 700;
            padding: 14px 18px;
            border-bottom: 1px solid #dee2e6;
        }

        .finance-table {
            width: 100%;
            margin-bottom: 0;
            table-layout: fixed;
        }

        .finance-table th,
        .finance-table td {
            vertical-align: middle;
            padding: 10px 12px;
            font-size: 14px;
            word-wrap: break-word;
        }

        .finance-table thead th {
            background: #f8f9fa;
            font-weight: 700;
            text-align: center;
        }

        .finance-table tbody th,
        .finance-table tbody td {
            text-align: center;
        }

        .finance-table tbody td.note-col,
        .finance-table tbody th.note-col {
            text-align: left;
        }

        .finance-total-row th,
        .finance-total-row td {
            background: #f8f9fa;
            font-weight: 700;
        }

        .summary-table th,
        .summary-table td {
            text-align: center;
            font-size: 15px;
            padding: 14px 12px;
        }

        .summary-value {
            font-weight: 700;
            font-size: 18px;
        }

        .sign-table th,
        .sign-table td {
            text-align: center;
            vertical-align: middle;
            padding: 16px;
        }

        .sign-box {
            height: 80px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            background: #fff;
        }

        @media print {

            .app-header,
            .app-sidebar,
            .messages-section,
            .noprint {
                display: none !important;
            }

            .projects-section {
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .finance-card {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>

    <div id="dynamic_content" class="customersTable">
        <div class="report-wrap">

            <div class="report-title">
                <h3>Hesabatın adı: <?= $getOrderOrg['accountId']; ?></h3>
                <h3>Hesabat tarixi: <?= date("d.m.Y", strtotime($getOrderOrg['created'])); ?></h3>
            </div>

            <?php
            $totalAmount = 0;
            $totalCost = 0;
            $totalTransfer = 0;
            ?>

            <div class="card finance-card">
                <div class="card-header">Mədaxillər</div>
                <div class="table-responsive">
                    <table class="table table-bordered finance-table">
                        <colgroup>
                            <col style="width:6%;">
                            <col style="width:8%;">
                            <col style="width:14%;">
                            <col style="width:18%;">
                            <col style="width:14%;">
                            <col style="width:14%;">
                            <col style="width:26%;">
                        </colgroup>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th class="noprint">ID</th>
                                <th>DQN</th>
                                <th>Şəhadətnamə nömrəsi</th>
                                <th>Məbləğ</th>
                                <th>Tarix</th>
                                <th>Qeyd</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $dqn = 0;

                            $query = "
    SELECT * 
    FROM payments 
    WHERE fromAccount != 'Özü ödədi'
      AND fromAccount NOT IN (SELECT title FROM finaccounts)
      AND category = 'Sığorta ödənişləri'
      AND toAccount != 'Özü ödədi'
      AND teslimId = '$orderId'
      AND deletedby = 0
";
                            $sql = mysqli_query($db, $query);

                            while ($row = mysqli_fetch_array($sql)) {
                                $dqn++;

                                if (empty($row['orderId'])) {
                                    $getOrder = mysqli_fetch_array(mysqli_query($db, "
                                        SELECT identification 
                                        FROM call_status 
                                        WHERE identification != '' 
                                          AND car_id = '" . $row['fromAccount'] . "'
                                        ORDER BY id DESC 
                                        LIMIT 1
                                    "));
                                    $row['orderId'] = $getOrder['identification'];
                                }

                                $totalAmount += (float)$row['amount'];
                            ?>
                                <tr>
                                    <th scope="row"><?= $dqn; ?></th>
                                    <td class="noprint"><?= $row['id']; ?></td>
                                    <td><?= $row['fromAccount']; ?></td>
                                    <td><?= $row['orderId']; ?></td>
                                    <td><?= number_format((float)$row['amount'], 2, '.', ''); ?> AZN</td>
                                    <td><?= date("d.m.Y", strtotime($row['paydate'] ?: $row['created'])); ?></td>
                                    <td class="note-col"><?= $row['title']; ?></td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr class="finance-total-row">
                                <th>Toplam:</th>
                                <td class="noprint"></td>
                                <td></td>
                                <td></td>
                                <td><?= number_format($totalAmount, 2, '.', ''); ?> AZN</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php
            $query = "
                SELECT * 
                FROM payments 
                WHERE category != 'Sığorta ödənişləri'
                  AND category != 'Transfer'
                  AND teslimId = '$orderId'
                  AND deletedby = 0
            ";
            $sql = mysqli_query($db, $query);
            $costRows = [];
            $cost = 0;

            while ($row = mysqli_fetch_array($sql)) {
                $cost++;
                $row['row_num'] = $cost;
                $costRows[] = $row;
                $totalCost += (float)$row['amount'];
            }

            if ($totalCost > 0) {
            ?>
                <div class="card finance-card">
                    <div class="card-header">Xərclər</div>
                    <div class="table-responsive">
                        <table class="table table-bordered finance-table">
                            <colgroup>
                                <col style="width:6%;">
                                <col style="width:8%;">
                                <col style="width:14%;">
                                <col style="width:18%;">
                                <col style="width:14%;">
                                <col style="width:14%;">
                                <col style="width:26%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="noprint">ID</th>
                                    <th>DQN</th>
                                    <th>Kateqoriya</th>
                                    <th>Məbləğ</th>
                                    <th>Tarix</th>
                                    <th>Qeyd</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($costRows as $row) { ?>
                                    <tr>
                                        <th scope="row"><?= $row['row_num']; ?></th>
                                        <td class="noprint"><?= $row['id']; ?></td>
                                        <td><?= $row['fromAccount']; ?></td>
                                        <td><?= $row['category']; ?></td>
                                        <td><?= number_format((float)$row['amount'], 2, '.', ''); ?> AZN</td>
                                        <td><?= date("d.m.Y", strtotime($row['paydate'] ?: $row['created'])); ?></td>
                                        <td class="note-col"><?= $row['title']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="finance-total-row">
                                    <th>Toplam:</th>
                                    <td class="noprint"></td>
                                    <td></td>
                                    <td></td>
                                    <td><?= number_format($totalCost, 2, '.', ''); ?> AZN</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>

            <?php
            $query = "
    SELECT * 
    FROM payments 
    WHERE category = 'Transfer'
      AND teslimId = '$orderId'
      AND deletedby = 0
";
            $sql = mysqli_query($db, $query);
            $transferRows = [];
            $transfer = 0;

            while ($row = mysqli_fetch_array($sql)) {
                $transfer++;
                $row['row_num'] = $transfer;
                $transferRows[] = $row;
                $totalTransfer += (float)$row['amount'];
            }

            if ($totalTransfer > 0) {
            ?>
                <div class="card finance-card">
                    <div class="card-header">Transferlər</div>
                    <div class="table-responsive">
                        <table class="table table-bordered finance-table">
                            <colgroup>
                                <col style="width:6%;">
                                <col style="width:8%;">
                                <col style="width:14%;">
                                <col style="width:18%;">
                                <col style="width:14%;">
                                <col style="width:14%;">
                                <col style="width:26%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th class="noprint">ID</th>
                                    <th>DQN</th>
                                    <th>Kateqoriya</th>
                                    <th>Məbləğ</th>
                                    <th>Tarix</th>
                                    <th>Qeyd</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transferRows as $row) { ?>
                                    <tr>
                                        <th scope="row"><?= $row['row_num']; ?></th>
                                        <td class="noprint"><?= $row['id']; ?></td>
                                        <td><?= $row['fromAccount']; ?></td>
                                        <td><?= $row['category']; ?></td>
                                        <td><?= number_format((float)$row['amount'], 2, '.', ''); ?> AZN</td>
                                        <td><?= date("d.m.Y", strtotime($row['paydate'] ?: $row['created'])); ?></td>
                                        <td class="note-col"><?= $row['title']; ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="finance-total-row">
                                    <th>Toplam:</th>
                                    <td class="noprint"></td>
                                    <td></td>
                                    <td></td>
                                    <td><?= number_format($totalTransfer, 2, '.', ''); ?> AZN</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php } ?>

            <?php
            $summaryIncome = 0;
            $summaryExpense = 0;

            $getSummaryIncome = mysqli_fetch_array(mysqli_query($db, "
    SELECT COALESCE(SUM(amount), 0) as total
    FROM payments
    WHERE fromAccount != 'Özü ödədi'
      AND fromAccount NOT IN (SELECT title FROM finaccounts)
      AND category = 'Sığorta ödənişləri'
      AND toAccount != 'Özü ödədi'
      AND teslimId = '$orderId'
      AND deletedby = 0
"));

            $getSummaryExpense = mysqli_fetch_array(mysqli_query($db, "
    SELECT COALESCE(SUM(amount), 0) as total
    FROM payments
    WHERE category != 'Sığorta ödənişləri'
      AND teslimId = '$orderId'
      AND deletedby = 0
"));

            $summaryIncome = (float)$getSummaryIncome['total'];
            $summaryExpense = (float)$getSummaryExpense['total'];
            $balance = $summaryIncome - $summaryExpense;
            ?>

            <div class="card finance-card">
                <div class="card-header">Hesabat yekunu</div>
                <div class="table-responsive">
                    <table class="table table-bordered finance-table summary-table">
                        <thead>
                            <tr>
                                <th>Toplam Gəlir</th>
                                <th>Toplam Xərc</th>
                                <th>Qalıq</th>
                                <th>Hara ödənilib</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="finance-total-row">
                                <td class="summary-value"><?= number_format($summaryIncome, 2, '.', ''); ?> AZN</td>
                                <td class="summary-value"><?= number_format($summaryExpense, 2, '.', ''); ?> AZN</td>
                                <td class="summary-value"><?= number_format($balance, 2, '.', ''); ?> AZN</td>
                                <td><?= $getOrderOrg['toAccount']; ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card finance-card">
                <div class="card-header">İmzalar</div>
                <div class="table-responsive">
                    <table class="table table-bordered sign-table mb-0">
                        <thead>
                            <tr>
                                <th>Təhvil verən</th>
                                <th>Təhvil alan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="sign-box"></div>
                                </td>
                                <td>
                                    <div class="sign-box"></div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <p><b>Qeyd:</b> Təhvil verən və alan bu sənədi fiziki olaraq ad, soyad daxil edib imzalamalıdır. 2 nüsxə saxlanılmalıdır.</p>
            <p><b>Çıxarış tarixi:</b> <?= date("d.m.Y H:i"); ?></p>

        </div>
    </div>
</div>

</body>

</html>
<? include('inc/footer.php'); ?>