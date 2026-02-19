<?php include('inc/header.php'); ?>

<?

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

?>

<style>
  a{
    text-decoration: none;
  }
</style>

      <div class="projects-section">

        <div id="dynamic_content">
          <center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>
        </div>

      </div>

      
  
  <div class="messages-section" style="overflow: hidden;">

    <div id="messages" class="messages" style="max-height: 550px; overflow-y: auto;">
    
          <? if($userGroup == 1 || $userGroup == 3){ ?>

                                                    <?

                                                        $today = 0;
                                                        $query = "SELECT * FROM call_status WHERE car_id IN (SELECT car_id FROM call_status WHERE createdby = '$user_id') AND type IN (SELECT id FROM paramitems WHERE code = 'paywait' AND status = 1 AND deletedby = 0) AND id IN (SELECT MAX(id) FROM call_status GROUP BY car_id) AND created > now() - INTERVAL 60 day ORDER by fast DESC";
                                                        $sql = mysqli_query($db, $query);
                                                        while($row = mysqli_fetch_array($sql)){

                                                            $today++;

                                                            $getParamitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = ".$row['type']));
                                                            $getCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT pin FROM customers WHERE id = '".$row['car_id']."'"));

                                                            if($row['fast'] == 1){
                                                                $addClass = "alert-danger";
                                                            } else{
                                                                $addClass = "alert-success";
                                                            }

                                                            $getStatusDate = $row['created'];
                                                            $thisTime = date("Y-m-d H:i:s");

                                                            $hourdiff = round((strtotime($thisTime) - strtotime($getStatusDate))/3600, 1)." S";

                                                            if ( is_numeric($getCustomer['pin']) ) {
                                                              $hourLimit = 360;
                                                            } else{
                                                              $hourLimit = 48;
                                                            }

                                                            if((round((strtotime($thisTime) - strtotime($getStatusDate))/3600, 1)) > $hourLimit){
                                                              $addClass = "alert-danger";
                                                            }

                                                            echo '

                                                                <a href="/call/'.$row['car_id'].'">
                                                                    <div class="alert '.$addClass.' '.$getParamitem['code'].'" role="alert" '.((!empty($row['content']) || !empty($row['companyId']) || !empty($row['paycode']) || !empty($row['price']) || !empty($row['agreePrice']) || !empty($row['next_date']))?'  data-toggle="tooltip" data-original-title="'.(($row['next_date'] != '0000-00-00 00:00:00')?' Xatırlatma Tarixi: '.$row['next_date'].' ':"").' '.((!empty($row['content']))?' Qeyd: '.$row['content'].' ':"").' '.((!empty($row['companyId']))?' Şirkət: '.$selectedCompany['title'].' ':"").' '.(($row['price'] != '0.00')?' Qiymət: '.$row['price'].' ':"").' '.(($row['agreePrice'] != '0.00')?' Razılaşdığı Qiymət: '.$row['agreePrice'].' ':"").' '.((!empty($row['paycode']))?' Ödəniş Kodu: '.$row['paycode'].' ':"").' Status Tarixi: '.$row['created'].'"':"").'>
                                                                        <strong>'.$row['car_id'].': </strong>'.$getParamitem['title'].' '.$hourdiff.'
                                                                    </div>
                                                                </a>

                                                            ';

                                                        }

                                                        if($today == 0){
                                                            echo '<p>Məlumat tapılmadı.</p>';
                                                        }

                                                    ?>
        
                                                    <?

                                                        $today = 0;
                                                        $query = "SELECT * FROM call_status WHERE car_id IN (SELECT car_id FROM call_status WHERE car_id IN (SELECT car_id FROM call_status WHERE createdby = '$user_id') AND type IN (SELECT id FROM paramitems WHERE code = 'backto' AND status = 1 AND deletedby = 0) AND id IN (SELECT MAX(id) FROM call_status GROUP BY car_id)) AND created > now() - INTERVAL 60 day GROUP by car_id ORDER by fast DESC";
                                                        $sql = mysqli_query($db, $query);
                                                        while($row = mysqli_fetch_array($sql)){

                                                            //now

                                                                $checkCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT end_date FROM customers WHERE car_id = '".$row['car_id']."'"));

                                                                //if($checkCustomer['end_date'] > date("Y-m-d")){

                                                            //now

                                                            $checkForward = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM call_status WHERE car_id = '".$row['car_id']."' AND forwardTo != '$user_id' AND forwardTo != 0 ORDER by id DESC LIMIT 1"));
                                                            if(empty($checkForward['id'])){

                                                                $today++;

                                                                $getLastStatus = mysqli_fetch_array(mysqli_query($db, "SELECT type FROM call_status WHERE car_id = '".$row['car_id']."' ORDER by id DESC LIMIT 1"));
                                                                $getParamitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = ".$getLastStatus['type']));

                                                                if($row['type'] == 14){

                                                                    $getStatusDate = $row['created'];
                                                                    $thisTime = date("Y-m-d H:i:s");

                                                                    $hourdiff = round((strtotime($thisTime) - strtotime($getStatusDate))/3600, 1)." S";

                                                                }

                                                                if($row['fast'] == 1){
                                                                    $addClass = "alert-danger";
                                                                } else{
                                                                    $addClass = "alert-success";
                                                                }

                                                                echo '

                                                                    <a href="/call/'.$row['car_id'].'">
                                                                        <div class="alert '.$addClass.' '.$getParamitem['code'].' '.$addClass.'" role="alert" '.((!empty($row['content']) || !empty($row['companyId']) || !empty($row['paycode']) || !empty($row['price']) || !empty($row['agreePrice']) || !empty($row['next_date']))?'  data-toggle="tooltip" data-original-title="'.(($row['next_date'] != '0000-00-00 00:00:00')?' Xatırlatma Tarixi: '.$row['next_date'].' ':"").' '.((!empty($row['content']))?' Qeyd: '.$row['content'].' ':"").' '.((!empty($row['companyId']))?' Şirkət: '.$selectedCompany['title'].' ':"").' '.(($row['price'] != '0.00')?' Qiymət: '.$row['price'].' ':"").' '.(($row['agreePrice'] != '0.00')?' Razılaşdığı Qiymət: '.$row['agreePrice'].' ':"").' '.((!empty($row['paycode']))?' Ödəniş Kodu: '.$row['paycode'].' ':"").' Status Tarixi: '.$row['created'].'"':"").'>
                                                                            <strong>'.$row['car_id'].': </strong>'.$getParamitem['title'].'
                                                                        </div>
                                                                    </a>

                                                                ';

                                                            }

                                                            // now

                                                            //}

                                                            // now

                                                        }

                                                        if($today == 0){
                                                            echo '<p>Məlumat tapılmadı.</p>';
                                                        } else{
                                                            echo '<script>$(".badgeBackto").text("'.$today.'");</script>';
                                                        }

                                                    ?>
                                        
                                                    <?

                                                        $today = 0;
                                                        $query = "SELECT * FROM call_status WHERE car_id IN (SELECT car_id FROM call_status WHERE forwardTo = '$user_id') AND type IN (SELECT id FROM paramitems WHERE code = 'forward' AND status = 1 AND deletedby = 0) AND id IN (SELECT MAX(id) FROM call_status GROUP BY car_id) AND created > now() - INTERVAL 60 day ORDER by fast DESC";
                                                        $sql = mysqli_query($db, $query);
                                                        while($row = mysqli_fetch_array($sql)){

                                                            $today++;

                                                            $getParamitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = ".$row['type']));

                                                            if($row['type'] == 14){

                                                                $getStatusDate = $row['created'];
                                                                $thisTime = date("Y-m-d H:i:s");

                                                                $hourdiff = round((strtotime($thisTime) - strtotime($getStatusDate))/3600, 1)." S";

                                                            }

                                                            if($row['fast'] == 1){
                                                                $addClass = "alert-danger";
                                                            } else{
                                                                $addClass = "alert-success";
                                                            }

                                                            echo '

                                                                <a href="/call/'.$row['car_id'].'">
                                                                    <div class="alert '.$addClass.' '.$getParamitem['code'].' '.$addClass.'" role="alert" '.((!empty($row['content']) || !empty($row['companyId']) || !empty($row['paycode']) || !empty($row['price']) || !empty($row['agreePrice']) || !empty($row['next_date']))?'  data-toggle="tooltip" data-original-title="'.(($row['next_date'] != '0000-00-00 00:00:00')?' Xatırlatma Tarixi: '.$row['next_date'].' ':"").' '.((!empty($row['content']))?' Qeyd: '.$row['content'].' ':"").' '.((!empty($row['companyId']))?' Şirkət: '.$selectedCompany['title'].' ':"").' '.(($row['price'] != '0.00')?' Qiymət: '.$row['price'].' ':"").' '.(($row['agreePrice'] != '0.00')?' Razılaşdığı Qiymət: '.$row['agreePrice'].' ':"").' '.((!empty($row['paycode']))?' Ödəniş Kodu: '.$row['paycode'].' ':"").' Status Tarixi: '.$row['created'].'"':"").'>
                                                                        <strong>'.$row['car_id'].': </strong>'.$getParamitem['title'].'
                                                                    </div>
                                                                </a>

                                                            ';

                                                        }

                                                        if($today == 0){
                                                            echo '<p>Məlumat tapılmadı.</p>';
                                                        }

                                                    ?>
                                        
                                        
                                                    <?

                                                        $today = 0;
                                                        $query = "SELECT * FROM call_status WHERE car_id IN (SELECT car_id FROM customers WHERE DATE(end_date) >= CURDATE()) AND createdby = '$user_id' AND DATE(next_date) <= CURDATE() AND type IN (SELECT id FROM paramitems WHERE code = 'waiting' AND status = 1 AND deletedby = 0) AND id IN (SELECT MAX(id) FROM call_status GROUP BY car_id) AND created > now() - INTERVAL 60 day ORDER by fast DESC";
                                                        $sql = mysqli_query($db, $query);
                                                        while($row = mysqli_fetch_array($sql)){

                                                            $today++;

                                                            $getParamitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = ".$row['type']));

                                                            if($row['fast'] == 1){
                                                                $addClass = "alert-danger";
                                                            } else{
                                                                $addClass = "alert-success";
                                                            }

                                                            if($today == 1){

                                                                echo '

                                                                    <a href="/call/'.$row['car_id'].'">
                                                                        <div class="alert '.$addClass.' '.$getParamitem['code'].'" role="alert" '.((!empty($row['content']) || !empty($row['companyId']) || !empty($row['paycode']) || !empty($row['price']) || !empty($row['agreePrice']) || !empty($row['next_date']))?'  data-toggle="tooltip" data-original-title="'.(($row['next_date'] != '0000-00-00 00:00:00')?' Xatırlatma Tarixi: '.$row['next_date'].' ':"").' '.((!empty($row['content']))?' Qeyd: '.$row['content'].' ':"").' '.((!empty($row['companyId']))?' Şirkət: '.$selectedCompany['title'].' ':"").' '.(($row['price'] != '0.00')?' Qiymət: '.$row['price'].' ':"").' '.(($row['agreePrice'] != '0.00')?' Razılaşdığı Qiymət: '.$row['agreePrice'].' ':"").' '.((!empty($row['paycode']))?' Ödəniş Kodu: '.$row['paycode'].' ':"").' Status Tarixi: '.$row['created'].'"':"").'>
                                                                            <strong>'.$row['car_id'].': </strong>'.$getParamitem['title'].'
                                                                        </div>
                                                                    </a>

                                                                ';

                                                            }

                                                        }

                                                        if($today == 0){
                                                            echo '<p>Məlumat tapılmadı.</p>';
                                                        }

                                                    ?>

          <? } if($userGroup == 1 || $userGroup == 2){ ?>
                                        
                                                    <?

                                                        $today = 0;
                                                        $query = "SELECT * FROM call_status WHERE type IN (SELECT id FROM paramitems WHERE (code = 'confirm' OR code = 'payed') AND status = 1 AND deletedby = 0) AND id IN (SELECT MAX(id) FROM call_status GROUP BY car_id) AND created > now() - INTERVAL 60 day ORDER by fast DESC";
                                                        $sql = mysqli_query($db, $query);
                                                        while($row = mysqli_fetch_array($sql)){

                                                            $today++;

                                                            $getParamitem = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM paramitems WHERE id = ".$row['type']));

                                                            if($row['fast'] == 1){
                                                                $addClass = "alert-danger";
                                                            } else{
                                                                $addClass = "alert-success";
                                                            }

                                                            echo '

                                                                <a href="/call/'.$row['car_id'].'">
                                                                    <div class="alert '.$addClass.' '.$getParamitem['code'].'" role="alert" '.((!empty($row['content']) || !empty($row['companyId']) || !empty($row['paycode']) || !empty($row['price']) || !empty($row['agreePrice']) || !empty($row['next_date']))?'  data-toggle="tooltip" data-original-title="'.(($row['next_date'] != '0000-00-00 00:00:00')?' Xatırlatma Tarixi: '.$row['next_date'].' ':"").' '.((!empty($row['content']))?' Qeyd: '.$row['content'].' ':"").' '.((!empty($row['companyId']))?' Şirkət: '.$selectedCompany['title'].' ':"").' '.(($row['price'] != '0.00')?' Qiymət: '.$row['price'].' ':"").' '.(($row['agreePrice'] != '0.00')?' Razılaşdığı Qiymət: '.$row['agreePrice'].' ':"").' '.((!empty($row['paycode']))?' Ödəniş Kodu: '.$row['paycode'].' ':"").' Status Tarixi: '.$row['created'].'"':"").'>
                                                                        <button aria-label="" class="close" data-bs-dismiss="alert"></button>
                                                                        <strong>'.$row['car_id'].': </strong>'.$getParamitem['title'].'
                                                                    </div>
                                                                </a>

                                                            ';

                                                        }

                                                        if($today == 0){
                                                            echo '<p>Məlumat tapılmadı.</p>';
                                                        }

                                                    ?>

          <? } ?>

    </div>

  </div>

</body>

</html>

<? include('inc/footer.php'); ?>

<div class="modal fade addCustomer" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <form id="addCustomer">
      
        <div class="modal-body">

          <div class="invoice-body">

            <div class="panel panel-default">
              <table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <th class="text-center colfix"><?= lang('Xidmət'); ?></th>
                    <th class="text-center colfix"><?= lang('Növ'); ?></th>
                    <th class="text-center colfix"><?= lang('İstifadəçi'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <select name="productType" class="full-width select2">
                        <option value="1"><?= lang('İcbari avto sığorta'); ?></option>
                      </select>
                    </td>
                    <td class="text-right">
                      <select name="customerType" class="full-width select2">
                        <option value="1"><?= lang('Fiziki şəxs'); ?></option>
                      </select>
                    </td>
                    <td class="text-right">
                      <select name="createdOperator" class="full-width select2" required>
                        <option value="<?= $user_data['id']; ?>"><?= $user_data['name']; ?> <?= $user_data['surname']; ?></option>
                        <?

                          $sql = mysqli_query($db, "SELECT id, name, surname FROM users WHERE deletedby = 0 AND id != '$user_id' AND id != 1");
                          while($row = mysqli_fetch_array($sql)){
                            echo '<option value="'.$row['id'].'">'.$row['name'].' '.$row['surname'].'</option>';
                          }
                        
                        ?>
                      </select>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="panel panel-default">
              <table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <th><?= lang('Ad, Soyad'); ?></th>
                    <th class="text-center colfix"><?= lang('Telefon'); ?></th>
                    <th class="text-center colfix"><?= lang('Pin kod'); ?></th>
                    <th class="text-center colfix"><?= lang('Ş/Seriya'); ?></th>
                    <th class="text-center colfix"><?= lang('Seriya'); ?></th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td class="text-right">
                      <input type="text" class="exlike nameInput" name="namesurname" placeholder="<?= lang('Ad, Soyad'); ?>">
                    </td>
                    <td class="text-right">
                      <input type="text" class="exlike phoneInput" name="phone" placeholder="<?= lang('Telefon'); ?>">
                    </td>
                    <td class="text-right">
                      <input type="text" class="exlike pinInput" name="pin" placeholder="<?= lang('Pin kod'); ?>">
                    </td>
                    <td class="text-right">
                      <input type="text" class="exlike pin_serialInput" name="pin_serial" placeholder="<?= lang('Ş/Seriya'); ?>">
                    </td>
                    <td class="text-right">
                      <input type="text" class="exlike serialInput" name="serial" placeholder="<?= lang('Seriya'); ?>">
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="panel panel-default">
              <table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <td class="text-center col-xs-1"><?= lang('DQN'); ?></td>
                    <td class="text-center col-xs-1"></td>
                    <td class="text-center col-xs-1"><?= lang('Seriya'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('Marka'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('Model'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('Növ'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('Həcm'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('İl'); ?></td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike car_idInput" name="car_id" placeholder="<?= lang('DQN'); ?>">
                    </th>
                    <th>
                      <button class="my-btn searchCustomerBut active btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i></button>
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike car_pinInput" name="car_pin" placeholder="<?= lang('Seriya'); ?>">
                    </th>
                    <th class="text-center rowtotal mono">
                      <select name="make" class="full-width select2">
                        <option value=""><?= lang('Marka Seçin'); ?></option>
                        <?

                          $sql = mysqli_query($db, "SELECT id, title FROM make");
                          while($row = mysqli_fetch_array($sql)){
                            echo '<option value="'.$row['title'].'">'.$row['title'].'</option>';
                          }
                        
                        ?>
                      </select>
                    </th>
                    <th class="text-center rowtotal mono">
                      <select name="model" class="full-width select2">
                        <option value=""><?= lang('Model Seçin'); ?></option>
                        <?

                          $sql = mysqli_query($db, "SELECT id, title FROM model");
                          while($row = mysqli_fetch_array($sql)){
                            echo '<option value="'.$row['title'].'">'.$row['title'].'</option>';
                          }
                        
                        ?>
                      </select>
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike" name="type" placeholder="<?= lang('Növ'); ?>" value="Sedan">
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike" name="engineType" placeholder="<?= lang('Həcm'); ?>">
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike" name="caryear" placeholder="<?= lang('İl'); ?>">
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="panel panel-default">
              <table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <td class="text-center col-xs-1"><?= lang('Sığorta Şirkəti'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('Şəhadətnamə'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('Sığorta Haqqı'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('İSB Qiyməti'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('B/M Əmsalı'); ?></td>
                    <td class="text-center col-xs-1"><?= lang('Bitmə Tarixi'); ?></td>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th class="text-center rowtotal mono">
                      <select name="company" class="full-width select2">
                        <option value=""><?= lang('Şirkət Seçin'); ?></option>
                        <?

                          $sql = mysqli_query($db, "SELECT id, title FROM companies");
                          while($row = mysqli_fetch_array($sql)){
                            echo '<option value="'.$row['id'].'">'.$row['title'].'</option>';
                          }
                        
                        ?>
                      </select>
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike identificationInput" name="identification" placeholder="<?= lang('Şəhadətnamə'); ?>">
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike premiumInput" name="premium" placeholder="<?= lang('Sığorta Haqqı'); ?>">
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike valuespInput" name="valuesp" placeholder="<?= lang('İSB Qiyməti'); ?>">
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="text" class="exlike bmInput" name="bm" placeholder="<?= lang('B/M Əmsalı'); ?>">
                    </th>
                    <th class="text-center rowtotal mono">
                      <input type="date" class="exlike end_dateInput" name="end_date" value="<?= date("Y-m-d"); ?>" placeholder="<?= lang('Bitmə Tarixi'); ?>">
                    </th>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="row">
              <div class="col-12">
                <div class="panel panel-default">
                  <div class="panel-body">
                    <i><?= lang('Qeydlər'); ?></i>
                    <hr style="margin:3px 0 5px" />
                    <textarea name="note" class="form-control noteInput" cols="10" rows="2"></textarea>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>

        <div class="modal-footer">
          <button id="addRow" type="submit" class="btn btn-primary"><?= lang('Yadda saxla'); ?></button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Bağla'); ?></button>
        </div>

      </form>

    </div>
  </div>
</div>

<div class="modal fade modalAddStatus" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="statusForm_add" enctype="multipart/form-data">

                <div class="modal-body">

                            <input type="hidden" value="5" name="type">
                            <input id="car_id_input" type="hidden" name="car_id" value="">

                            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                                <div class="col-xl-12">
                                    <select id="status_type" class="full-width select2S" name="status_type" required>
                                        <option value="">Status Seçin</option>
                                            <?

                                                if($userGroup == 1){
                                                    $parentId = "parentId IN (SELECT id FROM params WHERE (code = 'operator' OR code = 'manager') AND status = 1 AND deletedby = 0)";
                                                } else if($userGroup == 2){
                                                    $parentId = "parentId IN (SELECT id FROM params WHERE (code = 'operator' OR code = 'manager') AND status = 1 AND deletedby = 0)";
                                                } else if($userGroup == 3){
                                                    $parentId = "parentId IN (SELECT id FROM params WHERE code = 'operator' AND status = 1 AND deletedby = 0)";
                                                }

                                                $sql = mysqli_query($db, "SELECT * FROM paramitems WHERE status = 1 AND deletedby = 0 AND $parentId");
                                                while($row = mysqli_fetch_array($sql)){
                                                    echo '<option value="'.$row['id'].'" data-type="'.$row['code'].'">'.$row['title'].'</option>';
                                                }

                                            ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                                <div class="col-xl-12">
                                    <textarea class="form-control" name="note" style="height: 50px;" placeholder="Qeyd"></textarea>
                                </div>
                            </div>

                            <div class="waitingData form-group row mt-3 mr-0 mb-3 ml-0" style="display:none;">
                                <div class="col-xl-12">

                                    <p>Xatırlatma Tarixi:</p>

                                    <input id="next_date" class="form-control" type="datetime-local" name="next_date">

                                </div>

                            </div>

                            <div class="forwardData form-group row mt-3 mr-0 mb-3 ml-0" style="display:none;">
                                <div class="col-xl-12">

                                    <p><?= lang("İstifadəçi Seçin"); ?>:</p>

                                    <select name="forwardTo" class="full-width select2S" id="forwardTo">
                                        <option value=""><?= lang("İstifadəçi Seçin"); ?></option>
                                        <? $sql = mysqli_query($db, "SELECT * FROM users WHERE deletedby = 0 AND status = 1 ORDER by name ASC");
                                        while($row = mysqli_fetch_array($sql)){
                                        echo '<option value="'.$row['id'].'">'.$row['name'].' '.$row['surname'].' '.$row['lastname'].'</option>';
                                        } ?>
                                    </select>

                                </div>

                            </div>

                            <div class="confirmData form-group row mt-3 mr-0 mb-3 ml-0" style="display:none;">
                                <div class="col-xl-12">

                                    <p><?= lang("Sığorta Şirkəti Seçin"); ?>:</p>

                                    <select name="selectCompany" class="full-width select2S" id="selectCompany">
                                        <option value=""><?= lang("Sığorta Şirkəti Seçin"); ?></option>
                                        <? $sql = mysqli_query($db, "SELECT id, title FROM companies WHERE deletedby = 0 AND status = 1 ORDER by title ASC");
                                        while($row = mysqli_fetch_array($sql)){
                                        echo '<option value="'.$row['id'].'">'.$row['title'].'</option>';
                                        } ?>
                                    </select>

                                    <br><br>

                                    <p><?= lang("Məhsul Seçin"); ?>:</p>

                                    <select name="selectProduct" class="full-width select2S" id="selectProduct">
                                      <option value="1"><?= lang('İcbari avto sığorta'); ?></option>
                                    </select>

                                    <br><br>

                                    <p><?= lang("Razılaşdığı Qiymət"); ?>:</p>

                                    <input class="form-control" id="agreePrice" type="number" step="0.01" name="agreePrice">

                                </div>

                            </div>

                            <div class="paywaitData form-group row mt-3 mr-0 mb-3 ml-0" style="display:none;">
                                <div class="col-xl-12">

                                    <p><?= lang("Sığorta Haqqı"); ?>:</p>

                                    <input class="form-control" id="price" type="number" step="0.01" name="price">

                                    <br>

                                    <p><?= lang("Ödəniş Kodu"); ?>:</p>

                                    <input class="form-control" id="paycode" type="text" name="paycode">

                                </div>

                            </div>

                            <div class="successData form-group row mt-3 mr-0 mb-3 ml-0" style="display:none;">
                                <div class="col-xl-12">

                                    <select name="changeUser" class="full-width selectSu">
                                      <option value="" id="thisChangeUser"><?= lang('İstifadəçini dəyiş'); ?></option>
                                      <?

                                        $sql = mysqli_query($db, "SELECT id, name, surname FROM users WHERE deletedby = 0 AND id != '$user_id'");
                                        while($row = mysqli_fetch_array($sql)){
                                          echo '<option value="'.$row['id'].'">'.$row['name'].' '.$row['surname'].'</option>';
                                        }
                                      
                                      ?>
                                    </select>

                                    <br>
                                    <br>

                                    <p><?= lang("Müqavilə nömrəsi"); ?>:</p>

                                    <input class="form-control" id="identification" type="text" name="identification">

                                    <br>

                                    <p><?= lang("Yazılma Tarixi"); ?>:</p>

                                    <input class="form-control" id="write_date" type="date" name="write_date" value="<?= date("Y-m-d"); ?>">

                                    <br>

                                    <p><?= lang("Bitmə Tarixi"); ?>:</p>

                                    <? $newEndDate = date('Y-m-d', strtotime('+1 year', strtotime(date("Y-m-d"))) ); ?>

                                    <input class="form-control" id="end_date" type="date" name="end_date" value="<?= $newEndDate; ?>">

                                    <br>

                                    <hr>

                                    <div class="row">

                                      <div class="form-group col-6">
                                          <input type="number" value="" step="0.01" name="allprice[]" id="allprice" placeholder="<?= lang('Biz ödədik'); ?>" class="calcprice form-control" autofocus>
                                      </div>

                                      <div class="form-group col-6">
                                          <select name="paymentMethod[]" class="full-width selectSu" id="">
                                            <option value="">Kart Seçin</option>
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

                                    </div>

                                    <hr>

                                    <p><?= lang("Bonus"); ?>:</p>

                                    <input class="form-control" id="bonus" type="number" step="0.01" name="bonus">

                                    <br><br>

                                    <label for="disableEarnAll">
                                        <input id="disableEarnAll" type="checkbox" value="1" name="disableEarnAll"> Komissiyasız!
                                    </label>

                                    <br><br>

                                    <label for="disableEarn">
                                        <input id="disableEarn" type="checkbox" value="1" name="disableEarn"> Operatora Komissiyasız!
                                    </label>

                                </div>

                            </div>

                            <div class="paymentData form-group row mt-3 mr-0 mb-3 ml-0" style="display:none;">
                                <div class="col-xl-12">

                                    <div class="row">

                                      <input type="hidden" name="paymentMethod[]" value="Özü ödədi">

                                      <div class="form-group col-12">
                                          <input type="number" value="" step="0.01" name="allprice[]" id="allprice" placeholder="<?= lang('Özü ödədi'); ?>" class="calcprice form-control" autofocus>
                                      </div>

                                      <hr>

                                      <div class="form-group col-6">
                                          <input type="number" value="" step="0.01" name="allprice[]" id="allprice" placeholder="<?= lang('Bizə ödədi'); ?>" class="calcprice form-control" autofocus>
                                      </div>

                                      <div class="form-group col-6">
                                          <select name="paymentMethod[]" class="full-width selectPa" id="">
                                            <option value="">Kart Seçin</option>
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

                                    </div>

                                </div>

                            </div>

                            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                                <div class="col-xl-12">
                                    <label for="fast">
                                        <input id="fast" type="checkbox" value="1" name="fast"> Təcili!
                                    </label>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary addStatusButton">Yadda saxla</button>
                            </div>

                </div>

            </form>

        </div>
    </div>
</div>

<div class="modal fade modalAddPayment" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?= lang('Ödəniş Et'); ?></h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

                <div class="modal-body" style="overflow: hidden;">

                    <form id="form_add_payment">

                        <input id="addFromJS" type="hidden" name="from" value="">
                        <input type="hidden" name="category" value="<?= lang('Sığorta ödənişləri'); ?>">

                        <div class="form-group">
                            <select name="to" class="full-width select2P to" id="">
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

                        <div class="form-group">
                            <label for="identification" class="col-form-label"><?= lang('Şəhadətnamə nömrəsi') ?>:</label>
                            <input type="text" name="identification" class="form-control" placeholder="<?= lang('Şəhadətnamə nömrəsi') ?>" id="identification">
                        </div>
                    
                        <div class="form-group">
                            <label for="amount" class="col-form-label"><?= lang('Məbləğ') ?>:</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="<?= lang('Məbləğ') ?>" id="amount">
                        </div>

                        <div class="form-group">
                            <label for="note" class="col-form-label"><?= lang('Qeyd') ?>:</label>
                            <textarea name="note" class="form-control" cols="20" rows="3" placeholder="<?= lang('Qeyd') ?>"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="date" class="col-form-label"><?= lang('Tarix'); ?>:</label>
                            <input type="date" name="paydate" class="form-control" placeholder="Tarix" value="<?= date("Y-m-d"); ?>" id="date">
                        </div>
                    
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Bağla') ?></button>
                            <button id="addRow" type="submit" class="btn btn-primary"><?= lang('Yadda Saxla') ?></button>
                        </div>
                    </form>
        </div>
    </div>
</div>

<script>

    $('#status_type').on('change', function() {
        var statusId = $(this).find(':selected').data('type');

        $("#next_date").prop('required',false);
        $("#selectCompany").prop('required',false);
        $("#selectProduct").prop('required',false);
        $("#price").prop('required',false);

        if(statusId == 'waiting'){
            $("#next_date").prop('required',true);
            $('.waitingData').show();
            $('.confirmData').hide();
            $('.successData').hide();
            $('.paymentData').hide();
            $('.paywaitData').hide();
        } else if(statusId == 'confirm'){
            //$(".openEdit").click();
            $("#selectCompany").prop('required',true);
            $("#selectProduct").prop('required',true);
            $('.confirmData').show();
            $('.waitingData').hide();
            $('.successData').hide();
            $('.paymentData').hide();
            $('.paywaitData').hide();
        } else if(statusId == 'success'){
            $("#identification").prop('required',true);
            $('.successData').show();
            $('.paymentData').hide();
            $('.waitingData').hide();
            $('.confirmData').hide();
            $('.paywaitData').hide();
        } else if(statusId == 'paywait'){
            $("#price").prop('required',true);
            $('.paywaitData').show();
            $('.successData').hide();
            $('.paymentData').hide();
            $('.waitingData').hide();
            $('.confirmData').hide();
        } else if(statusId == 'forward'){
            $('.forwardData').show();
            $('.paywaitData').hide();
            $('.successData').hide();
            $('.paymentData').hide();
            $('.waitingData').hide();
            $('.confirmData').hide();
        } else if(statusId == 'payed'){
            $('.paymentData').show();
            $('.paywaitData').hide();
            
            $('.forwardData').hide();
            $('.waitingData').hide();
            <? if($userGroup == 1 || $user_id == 14 || $user_id == 3){ ?>
              $('.confirmData').show();
            <? } else{ ?>
              $('.confirmData').hide();
            <? } ?>
        } else{
            $('.successData').hide();
            $('.paymentData').hide();
            $('.waitingData').hide();
            $('.confirmData').hide();
            $('.paywaitData').hide();
        }

    });

    $(document).ready(function() {

      $('#statusForm_add').on('submit', (function(e) {

          $('.addStatusButton').prop('disabled', true);

          e.preventDefault();

          $.ajax({
              type: 'POST',
              url: "/index.php?action=add",
              data: new FormData(this),
              contentType: false,
              cache: false,
              processData: false,
              beforeSend: function() {
                  $('.addStatusButton').prop('disabled', true);
                  $(".addStatusButton").html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
              },
              success: function(response) {
                  //console.log(response);
                  //location.reload();
                  $(location).attr('href', '/');
              }
          });
          return false;

      }));

    });

</script>


<script>

    $(document).ready(function() {

      load_data(1, '<?= $_GET['customer']; ?>', 1);

      $('#search').on('submit', (function(e) {

          e.preventDefault();

          var query = $(".search-input").val();
          load_data(1, query, 1);

      }));

    });

    $('#addCustomer').on('submit', (function(e) {

        e.preventDefault();

      $.ajax({
          type: 'POST',
          url: "/index.php?action=add&type=3",
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
              $('form#addCustomer').trigger("reset");
              load_data(1, response, 1);
              $("#addRow").html('Yadda Saxla');
              $('#addRow').prop('disabled', false);
          }
      });
      return false;

    }));

    $('#form_add_payment').on('submit', (function(e) {

        e.preventDefault();

        $.ajax({
            type: 'POST',
            url: "/index.php?action=add&type=8",
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
                //console.log(response);
                $('form#form_add').trigger("reset");
                //loadAll();
                $("#addRow").html('Yadda Saxla');
                $('#addRow').prop('disabled', false);
                $('.modalAddIncome').modal('toggle');
            }
        });
        return false;

    }));

    function load_data(page, query = '', limit = '') {
        $("#dynamic_content").css({
            opacity: 0.5
        });
        $.ajax({
            url: "/index.php?action=fetch&type=1",
            method: "POST",
            data: {
                page: page,
                query: query,
                limit: limit
            },
            beforeSend: function() {
              $("#dynamic_content").html('<center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>');
            },
            success: function(data) {
                $('#dynamic_content').html(data);
                $("#dynamic_content").css({
                    opacity: 1
                });
            }
        });
    }

    $(document).on('click', '.searchCustomerBut', function() {

        var car_id = $(".car_idInput").val();
        var car_pin = $(".car_pinInput").val();

        $.ajax({
            type: 'POST',
            url: '/index.php?action=data&type=1',
            data: {
              car_id: car_id,
              car_pin: car_pin
            },
            beforeSend: function() {},
            success: function(response) {

              var data = $.parseJSON(response);

              $('.nameInput').val(data.name);
              $('.phoneInput').val(data.phone);
              $('.pinInput').val(data.pin);
              $('.pin_serialInput').val(data.pin_serial);
              $('.serialInput').val(data.serial);
              $('.end_dateInput').val(data.end_date);
              $('.premiumInput').val(data.premium);
              $('.bmInput').val(data.bm);
              $('.valuespInput').val(data.valuesp);

              $('.noteInput').text(data.note);

            }
        });
        return false;
        
        
            
    });

</script>
