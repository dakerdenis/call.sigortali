<?php include('inc/header.php'); ?>
    
      <div class="projects-section">

        <div class="row">
                    <div class="col-lg-12 grid-margin">
                        <div class="card">
                        <div class="card-body">
                            <div id="dynamic_content_grid_finaccounts" class="row icons-list"></div>
                        </div>
                        </div>
                    </div>
                </div>

        <hr>
    
        <div class="row"><h1>Finance 2</h1>
                            <div class="col-lg-3 grid-margin stretch-card">
                                <select id="limit" class="full-width select2A">
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
                                <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-primary"><i class="fa fa-file-excel-o"></i></button>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target=".modalAddPayment"><?= lang('Ödəniş Et'); ?></button>
                                <a href="/orders" class="btn btn-primary"><?= lang('Ödəniş Hesabatları'); ?></a>
                            </div>
            </div>

        <div id="dynamic_content_payments" class="customersTable table-responsive">
            <center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>
        </div>

      </div>

</body>

</html>

<? include('inc/footer.php'); ?>

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

                        <div class="form-group">
                            <label for="category" class="col-form-label">Kateqoriya:</label>
                            <br>
                            <select name="category" class="full-width select2P" required>
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

                        <br>

                        <div class="form-group">
                            <select name="from" class="full-width select2P from" id="">
                                <option value=""><?= lang('Hardan') ?></option>
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
                                <?

                                    $queryPaymentMethods = "SELECT * FROM companies WHERE status = 1 AND deletedby = 0";

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
                                <?

                                    $queryPaymentMethods = "SELECT * FROM companies WHERE status = 1 AND deletedby = 0";

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
                            <label for="date" class="col-form-label"><?= lang('Tarix'); ?>:</label>
                            <input type="date" name="paydate" class="form-control" placeholder="Tarix" value="<?= date("Y-m-d"); ?>" id="date">
                        </div>

                        <div class="form-group">
                            <label for="note" class="col-form-label"><?= lang('Qeyd') ?>:</label>
                            <textarea name="note" class="form-control" cols="20" rows="3" placeholder="<?= lang('Qeyd') ?>"></textarea>
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

    $(document).ready(function() {

        $('#search').on('submit', (function(e) {

            e.preventDefault();

            var limit = $('#limit').val();
            var query = $(".search-input").val();
            load_data_payments(1, query, '', '', limit);

        }));

    });

    function loadAll(){
        load_data_grid_finaccounts(1, '', 1000, '');
        
        load_data_payments(1, '', '', '', 10);
    }

    loadAll();

    function load_data_payments(page, query = '', account = '' , category = '', limit = '') {
        $("#dynamic_content_payments").css({
            opacity: 0.5
        });
        $.ajax({
            url: "/index.php?action=fetch&type=8",
            method: "POST",
            data: {
                page: page,
                query: query,
                account: account,
                category: category,
                limit: limit
            },
            success: function(data) {
                $('#dynamic_content_payments').html(data);
                $("#dynamic_content_payments").css({
                    opacity: 1
                });
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
                if(query == ""){
                    $("#search_box_barcode").focus();
                }
                if(parent != ""){
                    $(".productspos").show();
                } else{
                    if(query == ""){
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
                $('form#form_add').trigger("reset");
                loadAll();
                $("#addRow").html('Yadda Saxla');
                $('#addRow').prop('disabled', false);
                $('.modalAddIncome').modal('toggle');
            }
        });
        return false;

    }));

    $(document).on('click', '.page-link', function() {
        var page = $(this).data('page_number');
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

</script>