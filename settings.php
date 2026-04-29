<?php include('inc/header.php'); ?>
<style>
    /* Модалка должна быть поверх select2 */
    .modal {
        z-index: 9999 !important;
    }
    .modal-backdrop {
        z-index: 9998 !important;
    }
    .select2-container {
        z-index: 1 !important;
    }
    /* Когда модалка открыта — скрываем select2 на фоне */
    body.modal-open .select2-container {
        z-index: 1 !important;
    }
</style>
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

<div class="row align-items-center">
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
            <div class="col-lg-9 grid-margin d-flex justify-content-end align-items-center" style="gap:8px;">
                <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-success">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>
                <a href="/cron/load.php" target="_blank" class="btn btn-warning">
                    <i class="fa fa-database"></i> CLEAR DATABASE
                </a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".addUser">
                    <i class="fa fa-plus"></i> <?= lang("İstifadəçi əlavə et"); ?>
                </button>
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
            

           <!---- 
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
            -->
            <? } ?>
            


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
<div class="modal fade editUser" role="dialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="fa fa-user-circle"></i> <?= lang("İstifadəçini redaktə et"); ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="editUserForm">
        <input type="hidden" name="itemId" id="edit_itemId">
        <input type="hidden" name="module_name" value="users">

        <div class="modal-body p-4">

          <div class="text-center mb-4">
            <div id="edit_avatar" style="width:80px;height:80px;border-radius:50%;background:#4e73df;color:#fff;display:inline-flex;align-items:center;justify-content:center;font-weight:700;font-size:28px;">
              ?
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold"><i class="fa fa-shield text-muted"></i> <?= lang("Rol"); ?></label>
            <select class="form-select" name="groupId" id="edit_groupId" required>
              <option value="1">Admin</option>
              <option value="2">Menecer</option>
              <option value="3">Operator</option>
            </select>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold"><i class="fa fa-user text-muted"></i> <?= lang("Ad"); ?></label>
              <input type="text" class="form-control" name="name" id="edit_name" required>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label fw-semibold"><i class="fa fa-user text-muted"></i> <?= lang("Soyad"); ?></label>
              <input type="text" class="form-control" name="surname" id="edit_surname" required>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold"><i class="fa fa-envelope text-muted"></i> <?= lang("İstifadəçi adı"); ?></label>
            <input type="text" class="form-control" name="email" id="edit_email" required>
          </div>

          <div class="mb-1">
            <label class="form-label fw-semibold"><i class="fa fa-lock text-muted"></i> <?= lang("Yeni şifrə"); ?></label>
            <input type="password" class="form-control" name="password" id="edit_password" placeholder="<?= lang('Boş buraxsanız dəyişməyəcək'); ?>">
            <small class="text-muted"><?= lang('Şifrəni dəyişməmək üçün boş buraxın'); ?></small>
          </div>
<hr class="my-3">

          <div class="mb-1">
            <label class="form-label fw-semibold d-block"><i class="fa fa-power-off text-muted"></i> <?= lang("Hesabın statusu"); ?></label>
            <div class="form-check form-switch form-switch-lg" style="padding-left:3em;">
              <input class="form-check-input" type="checkbox" role="switch" id="edit_status" style="width:3em;height:1.5em;cursor:pointer;">
              <label class="form-check-label ms-2" for="edit_status" id="edit_status_label">
                <span class="badge bg-secondary">Deaktiv</span>
              </label>
            </div>
            <small class="text-muted"><?= lang('İstifadəçinin sistemə daxil olma icazəsi'); ?></small>
          </div>
        </div>

        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fa fa-times"></i> <?= lang('Bağla'); ?></button>
          <button type="submit" id="saveEditUser" class="btn btn-primary"><i class="fa fa-check"></i> <?= lang('Yadda saxla'); ?></button>
        </div>

      </form>
    </div>
  </div>
</div>

<div class="modal fade deleteUser" role="dialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content border-0 shadow-lg">

      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title"><i class="fa fa-exclamation-triangle"></i> <?= lang("Silmək"); ?></h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body text-center p-4">
        <div style="font-size:48px;color:#dc3545;margin-bottom:15px;">
          <i class="fa fa-trash"></i>
        </div>
        <h5 class="mb-2"><?= lang("Əminsiniz?"); ?></h5>
        <p class="text-muted mb-0">
          <?= lang("İstifadəçi"); ?> <strong id="delete_user_name"></strong> <?= lang("silinəcək. Bu əməliyyatı geri qaytarmaq olmaz."); ?>
        </p>
        <input type="hidden" id="delete_user_id">
      </div>

      <div class="modal-footer bg-light justify-content-center">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fa fa-times"></i> <?= lang('Ləğv et'); ?></button>
        <button type="button" id="confirmDeleteUser" class="btn btn-danger"><i class="fa fa-trash"></i> <?= lang('Bəli, sil'); ?></button>
      </div>

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
            url: "/call/index.php?action=fetch&type=4",
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
          url: "/call/index.php?action=add&type=6",
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
          url: "/call/index.php?action=add&type=13",
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
// ===== Edit user modal =====
$(document).on('click', '.openEditUser', function() {
    var $b = $(this);
    var id      = $b.data('id');
    var name    = $b.data('name') || '';
    var surname = $b.data('surname') || '';
    var email   = $b.data('email') || '';
    var group   = $b.data('group') || '';
    var active  = parseInt($b.data('active'));

    $('#edit_itemId').val(id);
    $('#edit_name').val(name);
    $('#edit_surname').val(surname);
    $('#edit_email').val(email);
    $('#edit_groupId').val(group);
    $('#edit_password').val('');

    // Status switch
    $('#edit_status').prop('checked', active === 1);
    updateStatusLabel(active === 1);

    // avatar with initials
    var initials = ((name.charAt(0) || '') + (surname.charAt(0) || '')).toUpperCase();
    var colors = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#6f42c1','#fd7e14'];
    var bg = colors[id % colors.length];
    $('#edit_avatar').text(initials || '?').css('background', bg);

    var modal = new bootstrap.Modal(document.querySelector('.editUser'));
    modal.show();
});
// ===== Delete user modal =====
$(document).on('click', '.openDeleteUser', function() {
    var id   = $(this).data('id');
    var name = $(this).data('name') || '';

    $('#delete_user_id').val(id);
    $('#delete_user_name').text(name);

    var modal = new bootstrap.Modal(document.querySelector('.deleteUser'));
    modal.show();
});

$(document).on('click', '#confirmDeleteUser', function() {
    var id = $('#delete_user_id').val();
    var $btn = $(this);
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    $.ajax({
        type: 'POST',
        url: '/call/index.php?action=add&type=2',
        data: {
            itemId: id,
            module_name: 'users'
        },
        success: function(response) {
            $btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Bəli, sil');
            bootstrap.Modal.getInstance(document.querySelector('.deleteUser')).hide();
            load_data(1, '', $('#limit').val());
        },
        error: function(xhr) {
            $btn.prop('disabled', false).html('<i class="fa fa-trash"></i> Bəli, sil');
            alert('Xəta: ' + (xhr.responseText || 'silinmədi'));
        }
    });
});
function updateStatusLabel(isActive) {
    if (isActive) {
        $('#edit_status_label').html('<span class="badge bg-success"><i class="fa fa-check-circle"></i> Aktiv</span>');
    } else {
        $('#edit_status_label').html('<span class="badge bg-secondary"><i class="fa fa-circle-o"></i> Deaktiv</span>');
    }
}

$(document).on('change', '#edit_status', function() {
    updateStatusLabel($(this).is(':checked'));
});

// ===== Submit edit user form =====
$(document).on('submit', '#editUserForm', function(e) {
    e.preventDefault();

    var $btn = $('#saveEditUser');
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');

    var formData = {
        itemId:  $('#edit_itemId').val(),
        name:    $('#edit_name').val(),
        surname: $('#edit_surname').val(),
        email:   $('#edit_email').val(),
        groupId: $('#edit_groupId').val(),
        status:  $('#edit_status').is(':checked') ? 1 : 0
    };

    var pwd = $('#edit_password').val();
    if (pwd && pwd.length > 0) {
        formData.password = pwd;
    }

    console.log('Sending:', formData);

    $.ajax({
        type: 'POST',
        url: '/call/index.php?action=add&type=1&module_name=users',
        data: formData,
        success: function(response) {
            console.log('Response:', response);
            $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Yadda saxla');
            bootstrap.Modal.getInstance(document.querySelector('.editUser')).hide();
            load_data(1, '', $('#limit').val());
        },
        error: function(xhr, status, err) {
            console.log('Error:', xhr.responseText, status, err);
            $btn.prop('disabled', false).html('<i class="fa fa-check"></i> Yadda saxla');
            alert('Xəta baş verdi: ' + (xhr.responseText || err));
        }
    });
});
</script>