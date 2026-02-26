<?php include('inc/header.php'); ?>
<?php
    $orderId = $_GET['id'];
    $getOrderOrg = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM orders WHERE id = '$orderId'"));

?>
    <div class="projects-section">

        <div id="dynamic_content" class="customersTable table-responsive">

            <h3><b>Hesabatın adı: <?= $getOrderOrg['accountId']; ?></b></h3>
            <h3><b>Hesabat tarixi: <?= date("d.m.Y", strtotime($getOrderOrg['created'])); ?></b></h3>
            <p>Mədaxillər</p>

            <table class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th class="noprint" scope="col">ID</th>
                    <th scope="col">DQN</th>
                    <th scope="col">Şəhadətnamə nömrəsi</th>
                    <th scope="col">Məbləğ</th>
                    <th scope="col">Tarix</th>
                    <th scope="col">Qeyd</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $dqn = 0;
                        $totalAmount = 0;
                        $query = "SELECT * FROM payments WHERE fromAccount != 'Özü ödədi' AND toAccount != 'Özü ödədi' AND toAccount = '".$getOrderOrg['accountId']."' AND teslimId = '$orderId' AND deletedby = 0";
                        $sql = mysqli_query($db, $query);
                        while($row = mysqli_fetch_array($sql)){
                            $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = ".$row['createdby']));
                            $dqn++;
                            if(empty($row['orderId'])){
                                $getOrder = mysqli_fetch_array(mysqli_query($db, "SELECT identification FROM call_status WHERE identification != '' AND car_id = '".$row['fromAccount']."' ORDER by id DESC LIMIT 1"));
                                $row['orderId'] = $getOrder['identification'];
                            }
                            echo '
                                <tr>
                                    <th scope="row">'.$dqn.'</th>
                                    <td class="noprint">'.$row['id'].'</td>
                                    <td>'.$row['fromAccount'].'</td>
                                    <td>'.$row['orderId'].'</td>
                                    <td>'.$row['amount'].' AZN</td>
                                    <td>'.date("d.m.Y", strtotime($row['created'])).'</td>
                                    <td>'.$row['title'].'</td>
                                </tr>
                            ';
                            $totalAmount += $row['amount'];
                        }
                    ?>
                    <tr>
                        <th scope="row">Toplam:</th>
                        <td class="noprint"></td>
                        <td></td>
                        <td></td>
                        <td><?= $totalAmount; ?> AZN</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <div class="costContainer">
            <p>Xərclər</p>
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th class="noprint" scope="col">ID</th>
                    <th class="noprint" scope="col">Kateqoriya</th>
                    <th scope="col">Məbləğ</th>
                    <th scope="col">Tarix</th>
                    <th scope="col">Qeyd</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $cost = 0;
                        $totalCost = 0;
                        $query = "SELECT * FROM payments WHERE (category != 'Sığorta ödənişləri' AND category != 'Transfer') AND teslimId = '$orderId' AND deletedby = 0";
                        $sql = mysqli_query($db, $query);
                        while($row = mysqli_fetch_array($sql)){
                            $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = ".$row['createdby']));
                            $cost++;
                            echo '
                                <tr>
                                    <th scope="row">'.$cost.'</th>
                                    <td class="noprint">'.$row['id'].'</td>
                                    <td class="noprint">'.$row['category'].'</td>
                                    <td>'.$row['amount'].' AZN</td>
                                    <td>'.date("d.m.Y", strtotime($row['created'])).'</td>
                                    <td>'.$row['title'].'</td>
                                </tr>
                            ';
                            $totalCost += $row['amount'];
                        }
                        if($totalCost == 0){
                            echo '<style>.costContainer{display:none;}</style>';
                        }
                    ?>
                    <tr>
                        <th scope="row">Toplam:</th>
                        <td class="noprint"></td>
                        <td class="noprint"></td>
                        <td><?= $totalCost; ?> AZN</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            </div>
            <hr>
            <div class="transferContainer">
            <p>Transferlər</p>
            <table class="table table-striped">
                <thead>
                    <tr>
                    <th scope="col">#</th>
                    <th class="noprint" scope="col">ID</th>
                    <th class="noprint" scope="col">Kateqoriya</th>
                    <th scope="col">Məbləğ</th>
                    <th scope="col">Tarix</th>
                    <th scope="col">Qeyd</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $transfer = 0;
                        $totalTransfer = 0;
                        $query = "SELECT * FROM payments WHERE (category != 'Sığorta ödənişləri' AND category = 'Transfer') AND fromAccount = '".$getOrderOrg['accountId']."' AND teslimId = '$orderId' AND deletedby = 0";
                        $sql = mysqli_query($db, $query);
                        while($row = mysqli_fetch_array($sql)){
                            $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = ".$row['createdby']));
                            $cost++;
                            echo '
                                <tr>
                                    <th scope="row">'.$transfer.'</th>
                                    <td class="noprint">'.$row['id'].'</td>
                                    <td class="noprint">'.$row['category'].'</td>
                                    <td>'.$row['amount'].' AZN</td>
                                    <td>'.date("d.m.Y", strtotime($row['created'])).'</td>
                                    <td>'.$row['title'].'</td>
                                </tr>
                            ';
                            $totalTransfer += $row['amount'];
                        }
                        if($totalTransfer == 0){
                            echo '<style>.transferContainer{display:none;}</style>';
                        }
                    ?>
                    <tr>
                        <th scope="row">Toplam:</th>
                        <td class="noprint"></td>
                        <td class="noprint"></td>
                        <td><?= $totalTransfer; ?> AZN</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            </div>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Toplam Gəlir</th>
                        <th scope="col">Toplam Xərc</th>
                        <th scope="col">Qalıq</th>
                        <th scope="col">Hara ödənilib</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th><?= $totalAmount; ?> AZN</th>
                        <td><?= ($totalCost + $totalTransfer); ?> AZN</td>
                        <td><?= ($totalAmount - $totalCost - $totalTransfer); ?> AZN</td>
                        <td><?= $getOrderOrg['toAccount']; ?></td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Təhvil verən</th>
                        <th scope="col">Təhvil alan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th style="width: 49%; border: 1px solid grey; height: 50px;"></th>
                        <td style="width: 49%; border: 1px solid grey; height: 50px;"></td>
                    </tr>
                </tbody>
            </table>
            <p><b>Qeyd:</b> Təhvil verən və alan bu sənədi fiziki olaraq ad, soyad daxil edib imzalamalıdır. 2 nüsxə saxlanılmalıdır.</p>
            <p><b>Çıxarış tarixi:</b> <?= date("d.m.Y H:i"); ?></p>
        </div>
    </div>
</body>
</html>
<? include('inc/footer.php'); ?>