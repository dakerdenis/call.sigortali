<?php include('inc/header.php'); ?>

<?php
    $finId = $_GET['id'];
    $getFinAccount = mysqli_fetch_array(mysqli_query($db, "SELECT title FROM finaccounts WHERE id = $finId"));
    $finTitle = $getFinAccount['title'];
?>

    <div class="projects-section">

        <div id="dynamic_content" class="customersTable table-responsive">

            <style>
                p,
                label {
                font:
                    1rem 'Fira Sans',
                    sans-serif;
                }

                input {
                margin: 0.4rem;
                }

            </style>

            <form id="form_add_order">

                <input type="hidden" name="from" value="<?= $finTitle; ?>">

                <div class="form-group">
                    <label for="title" class="col-form-label"><?= lang('Hesabat adı'); ?>:</label>
                    <input type="text" name="title" class="form-control" placeholder="Tarix" value="Gündəlik Hesabat" id="title" required>
                </div>

                <div class="form-group">
                    <label for="title" class="col-form-label"><?= lang('Hara ödənilib'); ?>:</label>
                    <select name="to" class="form-control" required>
                        <option value=""><?= lang('Hara') ?></option>
                                <?

                                    $queryPaymentMethods = "SELECT * FROM finaccounts WHERE id != 1 AND status = 1 AND deletedby = 0";

                                    if($userGroup == 3){
                                        //$queryPaymentMethods .= " AND userId = '$user_id'";
                                    }

                                    $sqlGetPaymentMethods = mysqli_query($db, $queryPaymentMethods);
                                    while($rowGetPaymentMethods = mysqli_fetch_array($sqlGetPaymentMethods)){

                                        echo '<option value="'.$rowGetPaymentMethods['title'].'">'.$rowGetPaymentMethods['title'].'</option>';

                                    }

                                ?>
                    </select>
                </div>

                <br>

                <p>Mədaxillər</p>

                <div  style="max-height: 400px; overflow: auto;">

                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">ID</th>
                        <th scope="col"><input type="checkbox" id="checkMedaxil" name="checkMedaxil" value="checkMedaxil" /> DQN</th>
                        <th scope="col">Şəhadətnamə nömrəsi</th>
                        <th scope="col">Kart</th>
                        <th scope="col">Məbləğ</th>
                        <th scope="col">Tarix</th>
                        <th scope="col">Kateqoriya</th>
                        <th scope="col">Qeyd</th>
                        <th scope="col">Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $dqn = 0;
                            $totalAmount = 0;

                            $query = "SELECT * FROM payments WHERE fromAccount != 'Özü ödədi' AND toAccount != 'Özü ödədi' AND toAccount = '$finTitle' AND teslimId = 0 AND deletedby = 0";

                            if(!empty($finId)){
                                $query .= " AND toAccount = '$finTitle'";
                            }

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
                                        <td>'.$row['id'].'</td>
                                        <td>
                                        <div>
                                            <input type="checkbox" id="car_ids_'.$row['id'].'" name="car_ids[]" value="'.$row['id'].'" class="medaxilCheckboxs" />
                                            <label for="car_ids_'.$row['id'].'">'.$row['fromAccount'].'</label>
                                        </div>
                                        </td>
                                        <td>'.$row['orderId'].'</td>
                                        <td>'.$row['toAccount'].'</td>
                                        <td>'.$row['amount'].' AZN</td>
                                        <td>'.date("d.m.Y H:i", strtotime($row['created'])).'</td>
                                        <td>'.$row['category'].'</td>
                                        <td>'.$row['title'].'</td>
                                        <td>'.$getCreated['name'].' '.$getCreated['surname'].'</td>
                                    </tr>

                                ';

                                $totalAmount += $row['amount'];

                            }

                        ?>

                        <tr>
                            <th scope="row">Toplam:</th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?= $totalAmount; ?> AZN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                    </tbody>
                </table>
                
                <script>
                    document.getElementById('checkMedaxil').addEventListener('change', function() {
                        // Get the state of the "checkMedaxil" checkbox
                        const isChecked = this.checked;
                        
                        // Get all checkboxes with the class 'medaxilCheckboxs'
                        const checkboxes = document.querySelectorAll('.medaxilCheckboxs');
                        
                        // Update the checked state of each checkbox
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = isChecked;
                        });
                    });
                </script>

                </div>

                <br>

                <p>Xərclər</p>

                <div  style="max-height: 400px; overflow: auto;">

                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">ID</th>
                        <th scope="col"><input type="checkbox" id="checkXerc" name="checkXerc" value="checkXerc" /> Kart</th>
                        <th scope="col">Məbləğ</th>
                        <th scope="col">Tarix</th>
                        <th scope="col">Qeyd</th>
                        <th scope="col">Kateqoriya</th>
                        <th scope="col">İstifadəçi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $cost = 0;
                            $totalCost = 0;

                            $query = "SELECT * FROM payments WHERE (category != 'Sığorta ödənişləri'  AND category != 'Transfer') AND teslimId = 0 AND deletedby = 0";

                            if(!empty($finId)){
                                $query .= " AND fromAccount = '$finTitle'";
                            }

                            $sql = mysqli_query($db, $query);
                            while($row = mysqli_fetch_array($sql)){

                                $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = ".$row['createdby']));

                                $cost++;

                                echo '

                                    <tr>
                                        <th scope="row">'.$cost.'</th>
                                        <td>'.$row['id'].'</td>
                                        <td>
                                        <div>
                                            <input type="checkbox" id="cost_ids_'.$row['id'].'" name="cost_ids[]" value="'.$row['id'].'" class="xercCheckboxs" />
                                            <label for="cost_ids_'.$row['id'].'">'.$row['fromAccount'].'</label>
                                        </div>
                                        </td>
                                        <td>'.$row['amount'].' AZN</td>
                                        <td>'.$row['created'].'</td>
                                        <td>'.$row['title'].'</td>
                                        <td>'.$row['category'].'</td>
                                        <td>'.$getCreated['name'].' '.$getCreated['surname'].'</td>
                                    </tr>

                                ';

                                $totalCost += $row['amount'];

                            }

                        ?>

                        <tr>
                            <th scope="row">Toplam:</th>
                            <td></td>
                            <td></td>
                            <td><?= $totalCost; ?> AZN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                    </tbody>
                </table>
                
                <script>
                    document.getElementById('checkXerc').addEventListener('change', function() {
                        // Get the state of the "checkMedaxil" checkbox
                        const isChecked = this.checked;
                        
                        // Get all checkboxes with the class 'medaxilCheckboxs'
                        const checkboxes = document.querySelectorAll('.xercCheckboxs');
                        
                        // Update the checked state of each checkbox
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = isChecked;
                        });
                    });
                </script>

                </div>

                <br>
                
                <p>Transferlər</p>

                <div  style="max-height: 400px; overflow: auto;">

                <table class="table table-striped">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">ID</th>
                        <th scope="col"><input type="checkbox" id="checkTransfer" name="checkTransfer" value="checkTransfer" /> Kart</th>
                        <th scope="col">Məbləğ</th>
                        <th scope="col">Tarix</th>
                        <th scope="col">Qeyd</th>
                        <th scope="col">Kateqoriya</th>
                        <th scope="col">İstifadəçi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                            $cost = 0;
                            $totalCost = 0;

                            $query = "SELECT * FROM payments WHERE (category != 'Sığorta ödənişləri' AND category = 'Transfer') AND teslimId = 0 AND deletedby = 0";

                            if(!empty($finId)){
                                $query .= " AND fromAccount = '$finTitle'";
                            }

                            $sql = mysqli_query($db, $query);
                            while($row = mysqli_fetch_array($sql)){

                                $getCreated = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = ".$row['createdby']));

                                $cost++;

                                echo '

                                    <tr>
                                        <th scope="row">'.$cost.'</th>
                                        <td>'.$row['id'].'</td>
                                        <td>
                                        <div>
                                            <input type="checkbox" id="cost_ids_'.$row['id'].'" name="cost_ids[]" value="'.$row['id'].'" class="transferCheckboxs" />
                                            <label for="cost_ids_'.$row['id'].'">'.$row['fromAccount'].'</label>
                                        </div>
                                        </td>
                                        <td>'.$row['amount'].' AZN</td>
                                        <td>'.$row['created'].'</td>
                                        <td>'.$row['title'].'</td>
                                        <td>'.$row['category'].'</td>
                                        <td>'.$getCreated['name'].' '.$getCreated['surname'].'</td>
                                    </tr>

                                ';

                                $totalCost += $row['amount'];

                            }

                        ?>

                        <tr>
                            <th scope="row">Toplam:</th>
                            <td></td>
                            <td></td>
                            <td><?= $totalCost; ?> AZN</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>

                    </tbody>
                </table>
                
                <script>
                    document.getElementById('checkTransfer').addEventListener('change', function() {
                        // Get the state of the "checkMedaxil" checkbox
                        const isChecked = this.checked;
                        
                        // Get all checkboxes with the class 'medaxilCheckboxs'
                        const checkboxes = document.querySelectorAll('.transferCheckboxs');
                        
                        // Update the checked state of each checkbox
                        checkboxes.forEach(checkbox => {
                            checkbox.checked = isChecked;
                        });
                    });
                </script>

                </div>

                <br>

                <div class="form-group">
                    <label for="date" class="col-form-label"><?= lang('Tarix'); ?>:</label>
                    <input type="date" name="paydate" class="form-control" placeholder="Tarix" value="<?= date("Y-m-d"); ?>" id="date">
                </div>

                <div class="form-group">
                    <label for="note" class="col-form-label"><?= lang('Qeyd') ?>:</label>
                    <textarea name="note" class="form-control" cols="20" rows="3" placeholder="<?= lang('Qeyd') ?>"></textarea>
                </div>

                <br>

                <div class="modal-footer">
                    <button id="addRow" type="submit" class="btn btn-primary"><?= lang('Yadda Saxla') ?></button>
                </div>
                
            </form>


        </div>

    </div>

</body>

</html>

<? include('inc/footer.php'); ?>

<script>

    $('#form_add_order').on('submit', (function(e) {

        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: "/index.php?action=add&type=10",
            data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
            beforeSend: function() {
                $("#dynamic_content").html('<center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>');
                $('#addRow').prop('disabled', true);
                $("#addRow").html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
            },
            success: function(response) {
                $(location).attr('href', '/orderitems/'+response);
            }
        });
        return false;

    }));

</script>