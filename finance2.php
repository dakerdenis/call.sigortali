<?php include('inc/header.php'); ?>

<div class="container-fluid py-3">

  <!-- Header -->
  <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 mb-3">
    <div>
      <h3 class="mb-1 fw-bold">Finance <span class="text-muted">2.1</span></h3>
      <div class="text-muted small">Ödənişlər və hesablar üzrə idarəetmə paneli</div>
    </div>

    <div class="d-flex flex-wrap gap-2">
      <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-outline-success">
        <i class="fa fa-file-excel-o me-1"></i> Export
      </button>

      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".modalAddPayment">
        <i class="fa fa-plus me-1"></i> <?= lang('Ödəniş Et'); ?>
      </button>

      <a href="/orders" class="btn btn-outline-primary">
        <i class="fa fa-list-alt me-1"></i> <?= lang('Ödəniş Hesabatları'); ?>
      </a>
    </div>
  </div>

  <!-- Accounts -->
  <div class="card shadow-sm mb-3">
    <div class="card-header bg-white d-flex align-items-center justify-content-between">
      <div class="fw-semibold">Hesablar</div>
      <div class="text-muted small">Kartlar / şirkətlər üzrə balans</div>
    </div>
    <div class="card-body">
      <div id="dynamic_content_grid_finaccounts" class="row icons-list g-2"></div>
    </div>
  </div>

  <!-- Payments + Filters -->
  <div class="card shadow-sm">
    <div class="card-header bg-white">
      <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
        <div class="fw-semibold">Ödənişlər</div>

        <div class="d-flex align-items-center gap-2">
          <span class="text-muted small d-none d-md-inline">Göstər:</span>
          <select id="limit" class="form-select form-select-sm select2A" style="min-width: 140px;">
            <option value="10" selected>10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
            <option value="1000">1000</option>
            <option value="9999999999999"><?= lang('Hamısı'); ?></option>
          </select>
        </div>
      </div>
    </div>

    <div class="card-body">

      <!-- Filters -->
      <div class="p-3 border rounded-3 bg-light mb-3">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold">Filterlər</div>
          <div class="text-muted small">Tarix / məbləğ / kateqoriya / hesabat</div>
        </div>

        <div class="row g-2">
          <div class="col-12 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Date from</label>
            <input type="date" id="date_from" class="form-control form-control-sm" placeholder="From">
          </div>

          <div class="col-12 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Date to</label>
            <input type="date" id="date_to" class="form-control form-control-sm" placeholder="To">
          </div>

          <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Min məbləğ</label>
            <input type="number" step="0.01" id="amount_min" class="form-control form-control-sm" placeholder="Min">
          </div>

          <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Max məbləğ</label>
            <input type="number" step="0.01" id="amount_max" class="form-control form-control-sm" placeholder="Max">
          </div>

          <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Hesabat ID</label>
            <input type="text" id="hesabat_id" class="form-control form-control-sm" placeholder="ID">
          </div>

          <div class="col-6 col-md-3 col-lg-2">
            <label class="form-label small text-muted mb-1">Şəhadətnamə nömrəsi</label>
            <input type="text" id="seh_no" class="form-control form-control-sm" placeholder="Nömrə">
          </div>

          <div class="col-12 col-md-6 col-lg-4">
            <label class="form-label small text-muted mb-1">Kateqoriya</label>
            <select id="category2" class="form-select form-select-sm">
              <option value="">Kateqoriya (hamısı)</option>
              <option value="Sığorta ödənişləri">Sığorta ödənişləri</option>
              <option value="Transfer">Transfer</option>
              <option value="Mobil nömrə">Mobil nömrə</option>
              <option value="Əmək haqları">Əmək haqları</option>
              <option value="Dsmf">Dsmf</option>
              <option value="Vergi">Vergi</option>
              <option value="Tibbi sığorta">Tibbi sığorta</option>
              <option value="İcarə">İcarə</option>
              <option value="Ofisin xərcləri">Ofisin xərcləri</option>
              <option value="Dəftərxana ləvazimatları">Dəftərxana ləvazimatları</option>
              <option value="Kommunal">Kommunal</option>
              <option value="İnternet">İnternet</option>
              <option value="Kompüter təmiri">Kompüter təmiri</option>
            </select>
          </div>

          <div class="col-12 col-md-6 col-lg-4 d-flex align-items-end gap-2">
            <button class="btn btn-primary btn-sm w-100" id="applyFilters">
              <i class="fa fa-filter me-1"></i> Filter
            </button>
            <button class="btn btn-outline-secondary btn-sm w-100" id="resetFilters">
              <i class="fa fa-refresh me-1"></i> Reset
            </button>
          </div>
        </div>
      </div>

      <!-- Table -->
      <div id="dynamic_content_payments" class="customersTable table-responsive rounded-3 border">
        <div class="py-5 text-center">
          <div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <div class="text-muted mt-2">Yüklənir...</div>
        </div>
      </div>

    </div>
  </div>

</div>

<? include('inc/footer.php'); ?>

<!-- Modal: Add Payment -->
<div class="modal fade modalAddPayment" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content shadow">

      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-0"><?= lang('Ödəniş Et'); ?></h5>
          <div class="text-muted small">Kartlardan ödənişləri qeyd etmək üçün</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <form id="form_add_payment">
        <div class="modal-body">

          <div class="mb-3">
            <label class="form-label"><?= lang('Kateqoriya'); ?>:</label>
            <select name="category" class="form-select select2P" required>
              <option value="<?= lang('Sığorta ödənişləri'); ?>"><?= lang('Sığorta ödənişləri'); ?></option>
              <option value="<?= lang('Transfer'); ?>"><?= lang('Transfer'); ?></option>
              <option value="<?= lang('Mobil nömrə'); ?>"><?= lang('Mobil nömrə'); ?></option>
              <option value="<?= lang('Əmək haqları'); ?>"><?= lang('Əmək haqları'); ?></option>
              <option value="<?= lang('Dsmf'); ?>"><?= lang('Dsmf'); ?></option>
              <option value="<?= lang('Vergi'); ?>"><?= lang('Vergi'); ?></option>
              <option value="<?= lang('Tibbi sığorta'); ?>"><?= lang('Tibbi sığorta'); ?></option>
              <option value="<?= lang('İcarə'); ?>"><?= lang('İcarə'); ?></option>
              <option value="<?= lang('Ofisin xərcləri'); ?>"><?= lang('Ofisin xərcləri'); ?></option>
              <option value="<?= lang('Dəftərxana ləvazimatları'); ?>"><?= lang('Dəftərxana ləvazimatları'); ?></option>
              <option value="<?= lang('Kommunal'); ?>"><?= lang('Kommunal'); ?></option>
              <option value="<?= lang('İnternet'); ?>"><?= lang('İnternet'); ?></option>
              <option value="<?= lang('Kompüter təmiri'); ?>"><?= lang('Kompüter təmiri'); ?></option>
            </select>
          </div>

          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label"><?= lang('Hardan'); ?></label>
              <select name="from" class="form-select select2P from">
                <option value=""><?= lang('Hardan') ?></option>
                <?php
                  $queryPaymentMethods = "SELECT * FROM finaccounts WHERE id != 1 AND status = 1 AND deletedby = 0";
                  $sqlGetPaymentMethods = mysqli_query($db, $queryPaymentMethods);
                  while ($rowGetPaymentMethods = mysqli_fetch_array($sqlGetPaymentMethods)) {
                    echo '<option value="' . $rowGetPaymentMethods['title'] . '">' . $rowGetPaymentMethods['title'] . '</option>';
                  }

                  $queryPaymentMethods = "SELECT * FROM companies WHERE status = 1 AND deletedby = 0";
                  $sqlGetPaymentMethods = mysqli_query($db, $queryPaymentMethods);
                  while ($rowGetPaymentMethods = mysqli_fetch_array($sqlGetPaymentMethods)) {
                    echo '<option value="' . $rowGetPaymentMethods['title'] . '">' . $rowGetPaymentMethods['title'] . '</option>';
                  }
                ?>
              </select>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label"><?= lang('Hara'); ?></label>
              <select name="to" class="form-select select2P to">
                <option value=""><?= lang('Hara') ?></option>
                <?php
                  $queryPaymentMethods = "SELECT * FROM finaccounts WHERE id != 1 AND status = 1 AND deletedby = 0";
                  $sqlGetPaymentMethods = mysqli_query($db, $queryPaymentMethods);
                  while ($rowGetPaymentMethods = mysqli_fetch_array($sqlGetPaymentMethods)) {
                    echo '<option value="' . $rowGetPaymentMethods['title'] . '">' . $rowGetPaymentMethods['title'] . '</option>';
                  }

                  $queryPaymentMethods = "SELECT * FROM companies WHERE status = 1 AND deletedby = 0";
                  $sqlGetPaymentMethods = mysqli_query($db, $queryPaymentMethods);
                  while ($rowGetPaymentMethods = mysqli_fetch_array($sqlGetPaymentMethods)) {
                    echo '<option value="' . $rowGetPaymentMethods['title'] . '">' . $rowGetPaymentMethods['title'] . '</option>';
                  }
                ?>
              </select>
            </div>
          </div>

          <div class="row g-2 mt-1">
            <div class="col-12 col-md-6">
              <label class="form-label"><?= lang('Şəhadətnamə nömrəsi'); ?>:</label>
              <input type="text" name="identification" class="form-control" placeholder="<?= lang('Şəhadətnamə nömrəsi') ?>" id="identification">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label"><?= lang('Məbləğ'); ?>:</label>
              <input type="number" step="0.01" name="amount" class="form-control" placeholder="<?= lang('Məbləğ') ?>" id="amount">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label"><?= lang('Tarix'); ?>:</label>
              <input type="date" name="paydate" class="form-control" value="<?= date("Y-m-d"); ?>" id="date">
            </div>

            <div class="col-12">
              <label class="form-label"><?= lang('Qeyd'); ?>:</label>
              <textarea name="note" class="form-control" rows="3" placeholder="<?= lang('Qeyd') ?>"></textarea>
            </div>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= lang('Bağla') ?></button>
          <button id="addRow" type="submit" class="btn btn-primary">
            <i class="fa fa-save me-1"></i> <?= lang('Yadda Saxla') ?>
          </button>
        </div>
      </form>

    </div>
  </div>
</div>
    <script>
        $(document).ready(function() {

            $('#search').on('submit', (function(e) {

                e.preventDefault();

                var limit = $('#limit').val();
                var query = $(".search-input").val();
                load_data_payments(1, query, '', '', limit);

            }));

        });

        function loadAll() {
            load_data_grid_finaccounts(1, '', 1000, '');

            load_data_payments(1, '', '', '', 10);
        }

        loadAll();

function load_data_payments(page, query = '', account = '' , category = '', limit = '') {
    $("#dynamic_content_payments").css({ opacity: 0.5 });

    $.ajax({
        url: "/index.php?action=fetch&type=8",
        method: "POST",
        data: {
            page: page,
            query: query,
            account: account,
            category: category,
            limit: limit,

            // NEW FILTERS
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            amount_min: $('#amount_min').val(),
            amount_max: $('#amount_max').val(),
            category2: $('#category2').val(),
            hesabat_id: $('#hesabat_id').val(),
            seh_no: $('#seh_no').val(),
        },
        success: function(data) {
            $('#dynamic_content_payments').html(data);
            $("#dynamic_content_payments").css({ opacity: 1 });
        }
    });
}

        function load_data_grid_finaccounts(page, query = '', limit = '', parent) {
            $("#dynamic_content_grid_finaccounts").css({
                opacity: 0.5
            });
            $.ajax({
                url: "/index.php?action=fetch&type=7",
                method: "POST",
                data: {
                    page: page,
                    query: query,
                    limit: limit,
                    parent: parent
                },
                beforeSend: function() {
                    $("#dynamic_content_grid_finaccounts").html('<center><div class="spinner-grow" style="width: 1rem; height: 1rem;" role="status"><span class="sr-only"></span></div></center>');
                },
                success: function(data) {
                    $('#dynamic_content_grid_finaccounts').html(data);
                    $("#dynamic_content_grid_finaccounts").css({
                        opacity: 1
                    });
                    if (query == "") {
                        $("#search_box_barcode").focus();
                    }
                    if (parent != "") {
                        $(".productspos").show();
                    } else {
                        if (query == "") {
                            $(".productspos").hide();
                        }
                    }

                }
            });
        }

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
  $('form#form_add_payment').trigger("reset");
  loadAll();
  $("#addRow").html('Yadda Saxla');
  $('#addRow').prop('disabled', false);
  $('.modalAddPayment').modal('hide'); 
}
            });
            return false;

        }));

        $(document).on('click', '.page-link', function() {
            var page = $(this).data('page_number');
            if (!page) return;
            var query = $(".search-input").val();
            var limit = $('#limit').val();
            load_data_payments(page, query, '', '', limit);
        });

        $(document).on('click', '.search_account', function() {
            var id = $(this).data('id');
            load_data_payments(1, '', id, '', 1000);
        });

        $("#limit").on("change", function() {
            var limit = $(this).val();
            var query = $(".search_box_form").val();
            load_data_payments(1, query, '', '', limit);
        });
        $('#applyFilters').on('click', function(){
  var limit = $('#limit').val();
  load_data_payments(1, '', '', '', limit);
});

$('#resetFilters').on('click', function(){
  $('#date_from, #date_to, #amount_min, #amount_max, #hesabat_id, #seh_no').val('');
  $('#category2').val('');
  var limit = $('#limit').val();
  load_data_payments(1, '', '', '', limit);
});
    </script>