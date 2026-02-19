<?php include('inc/header.php'); ?>

<?php

    $getSettings_startCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'startCustomer'"));
    $getSettings_limitCustomer = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'limitCustomer'"));
    $getSettings_minprice = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'minprice'"));
    $getSettings_maxprice = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM settings WHERE setting_name = 'maxprice'"));

    if(!empty($_POST['minprice'])){
        $minprice = $_POST['minprice'];
        mysqli_query($db, "UPDATE settings SET setting_value = '$minprice' WHERE setting_name = 'minprice'");
    }

    if(!empty($_POST['maxprice'])){
        $maxprice = $_POST['maxprice'];
        mysqli_query($db, "UPDATE settings SET setting_value = '$maxprice' WHERE setting_name = 'maxprice'");
    }

    if(!empty($_POST['startCustomer'])){
        $startCustomer = $_POST['startCustomer'];
        mysqli_query($db, "UPDATE settings SET setting_value = '$startCustomer' WHERE setting_name = 'startCustomer'");
    }

    if(!empty($_POST['limitCustomer'])){
        $limitCustomer = $_POST['limitCustomer'];
        mysqli_query($db, "UPDATE settings SET setting_value = '$limitCustomer' WHERE setting_name = 'limitCustomer'");
    }

?>

      <div class="projects-section">

        <div class="row">
                            <div class="col-lg-3 grid-margin stretch-card">
                                <select id="limit" class="full-width select2">
                                    <option value="10" selected>10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                    <option value="500">500</option>
                                    <option value="1000">1000</option>
                                    <option value="9999999999999"><?= lang('Hamısı'); ?></option>
                                </select>
                            </div>
                            <div class="col-lg-6 grid-margin stretch-card">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".addUser"><i class="fa fa-plus"></i></button>
                                <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-primary"><i class="fa fa-file-excel-o"></i></button>
                                <a href="/cron/load.php" target="_blank" class="btn btn-primary">CLEAR DATABASE</a>
                            </div>
            </div>

            <form class="row" method="POST" action="">


                <div class="form-group row mt-3 mr-0 mb-3 ml-0">

                    <div class="col-xl-3">

                        <p><?= lang("Min qiymət"); ?>:</p>

                        <input class="form-control" type="text" name="minprice" placeholder="<?= lang("Min qiymət"); ?>" value="<?= $getSettings_minprice['setting_value']; ?>" required>

                    </div>

                    <div class="col-xl-3">

                        <p><?= lang("Max qiymət"); ?>:</p>

                        <input class="form-control" type="text" name="maxprice" placeholder="<?= lang("Max qiymət"); ?>" value="<?= $getSettings_maxprice['setting_value']; ?>" required>

                    </div>

                    <div class="col-xl-2">

                        <p><?= lang("Min gün"); ?>:</p>

                        <input class="form-control" type="text" name="startCustomer" placeholder="<?= lang("Min gün"); ?>" value="<?= $getSettings_startCustomer['setting_value']; ?>" required>

                    </div>

                    <div class="col-xl-2">

                        <p><?= lang("Max gün"); ?>:</p>

                        <input class="form-control" type="text" name="limitCustomer" placeholder="<?= lang("Max gün"); ?>" value="<?= $getSettings_limitCustomer['setting_value']; ?>" required>

                    </div>

                    <div class="col-xl-2">

                        <p>&nbsp;</p>

                        <input class="form-control" type="submit" value="<?= lang("Yadda saxla"); ?>">

                    </div>

                </div>


            </form>
            
            <? if($user_id == 1 || $user_id == 2){ ?>
            
            <hr>
            
            <form id="transferdata" class="row transferdata">


                <div class="form-group row mt-3 mr-0 mb-3 ml-0">

                    <div class="col-xl-5">

                        <p><?= lang("Hardan"); ?>:</p>
                        
                        <select id="from" name="from" class="form-control" required>
                            <option value="">İstifadəçi seçin</option>
                            <?
                            
                                $sql = mysqli_query($db, "SELECT * FROM users WHERE deletedby = 0");
                                while($row = mysqli_fetch_array($sql)){
                                    echo '<option value="'.$row['id'].'">'.$row['name'].' '.$row['surname'].'</option>';
                                }
                            
                            ?>
                        </select>

                    </div>

                    <div class="col-xl-5">

                        <p><?= lang("Hara"); ?>:</p>
                        
                        <select id="to" name="to" class="form-control" required>
                            <option value="">İstifadəçi seçin</option>
                            <?
                            
                                $sql = mysqli_query($db, "SELECT * FROM users WHERE deletedby = 0");
                                while($row = mysqli_fetch_array($sql)){
                                    echo '<option value="'.$row['id'].'">'.$row['name'].' '.$row['surname'].'</option>';
                                }
                            
                            ?>
                        </select>

                    </div>

                    <div class="col-xl-2">

                        <p>&nbsp;</p>

                        <input class="form-control" type="submit" value="<?= lang("Yadda saxla"); ?>">

                    </div>

                </div>


            </form>
            
            <? } ?>
            
            <hr>

        <div id="dynamic_content" class="customersTable table-responsive">
          <center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>
        </div>

      </div>

</body>

</html>

<? include('inc/footer.php'); ?>

<div class="modal fade addUser" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">

      <form id="addUser">
      
        <div class="modal-body">
            
            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                <div class="col-xl-12">
                    <select id="groupId" class="full-width select2U" name="groupId" required>
                        <option value="">Rol Seçin</option>
                        <option value="1">Admin</option>
                        <option value="2">Menecer</option>
                        <option value="3">Operator</option>
                    </select>
                </div>
            </div>

            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                <div class="col-xl-12">

                    <p><?= lang("Ad"); ?>:</p>

                    <input class="form-control" type="text" name="name" placeholder="<?= lang("Ad"); ?>" required>

                </div>

            </div>

            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                <div class="col-xl-12">

                    <p><?= lang("Soyad"); ?>:</p>

                    <input class="form-control" type="text" name="surname" placeholder="<?= lang("Soyad"); ?>" required>

                </div>

            </div>

            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                <div class="col-xl-12">

                    <p><?= lang("İstifadəçi adı"); ?>:</p>

                    <input class="form-control" type="text" name="email" placeholder="<?= lang("İstifadəçi adı"); ?>" required>

                </div>

            </div>

            <div class="form-group row mt-3 mr-0 mb-3 ml-0">
                <div class="col-xl-12">

                    <p><?= lang("Şifrə"); ?>:</p>

                    <input class="form-control" type="password" name="password" placeholder="<?= lang("Şifrə"); ?>" required>

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

<script>

    $(document).ready(function() {

        load_data(1, '', 10);

        $('#search').on('submit', (function(e) {

            e.preventDefault();

            var limit = $('#limit').val();
            var query = $(".search-input").val();
            load_data(1, query, limit);

        }));

    });

    function load_data(page, query = '', limit = '') {
        
        $("#dynamic_content").css({
            opacity: 0.5
        });
        $.ajax({
            url: "/index.php?action=fetch&type=4",
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

    $(document).on('click', '.page-link', function() {
        var page = $(this).data('page_number');
        var query = $(".search-input").val();
        var limit = $('#limit').val();
        load_data(page, query, limit);
    });

    $('#addUser').on('submit', (function(e) {

        e.preventDefault();

      $.ajax({
          type: 'POST',
          url: "/index.php?action=add&type=6",
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
              $('form#addUser').trigger("reset");
              load_data(1, '', 10);
              $("#addRow").html('Yadda Saxla');
              $('#addRow').prop('disabled', false);
          }
      });
      return false;

    }));
    
    $('#transferdata').on('submit', (function(e) {

        e.preventDefault();

      $.ajax({
          type: 'POST',
          url: "/index.php?action=add&type=13",
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
              $('form#addUser').trigger("reset");
              load_data(1, '', 10);
              $("#addRow").html('Yadda Saxla');
              $('#addRow').prop('disabled', false);
          }
      });
      return false;

    }));

</script>