<?php include('inc/header.php'); ?>

<style>
    .filter-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        padding: 20px;
        margin-bottom: 20px;
    }
    .filter-card .form-label {
        font-size: 12px;
        font-weight: 600;
        color: #6c757d;
        text-transform: uppercase;
        margin-bottom: 4px;
    }
    .filter-card .form-control,
    .filter-card .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        font-size: 14px;
    }
    .filter-card .form-control:focus,
    .filter-card .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.15rem rgba(78,115,223,.15);
    }
</style>

<div class="projects-section">

    <div class="filter-card">
        <div class="row g-3 align-items-end">

            <div class="col-lg-3 col-md-6">
                <label class="form-label"><i class="fa fa-search"></i> <?= lang('Axtarış'); ?></label>
                <input type="text" id="filter_query" class="form-control" placeholder="DQN, ad, şəhadətnamə...">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label"><i class="fa fa-building"></i> Sığorta şirkəti</label>
                <select id="filter_company" class="form-select">
                    <option value="">Hamısı</option>
                    <?php
                    $sqlC = mysqli_query($db, "SELECT id, title FROM companies WHERE deletedby = 0 ORDER BY title ASC");
                    while($rowC = mysqli_fetch_array($sqlC)){
                        echo '<option value="'.$rowC['id'].'">'.$rowC['title'].'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label"><i class="fa fa-user"></i> Kim yazıb</label>
                <select id="filter_user" class="form-select">
                    <option value="">Hamısı</option>
                    <?php
                    $sqlU = mysqli_query($db, "SELECT id, name, surname FROM users WHERE deletedby = 0 AND status = 1 ORDER BY name ASC");
                    while($rowU = mysqli_fetch_array($sqlU)){
                        echo '<option value="'.$rowU['id'].'">'.htmlspecialchars($rowU['name'].' '.$rowU['surname']).'</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label"><i class="fa fa-calendar"></i> Yazılma tarixi</label>
                <div class="d-flex gap-1">
                    <input type="date" id="filter_date_from" class="form-control" placeholder="Dən">
                    <input type="date" id="filter_date_to" class="form-control" placeholder="Dək">
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label">Yazılma qiyməti</label>
                <div class="d-flex gap-1">
                    <input type="number" step="0.01" id="filter_agree_min" class="form-control" placeholder="Min">
                    <input type="number" step="0.01" id="filter_agree_max" class="form-control" placeholder="Max">
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label">Mühərrikə görə</label>
                <div class="d-flex gap-1">
                    <input type="number" step="0.01" id="filter_default_min" class="form-control" placeholder="Min">
                    <input type="number" step="0.01" id="filter_default_max" class="form-control" placeholder="Max">
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label">Sığorta haqqı</label>
                <div class="d-flex gap-1">
                    <input type="number" step="0.01" id="filter_price_min" class="form-control" placeholder="Min">
                    <input type="number" step="0.01" id="filter_price_max" class="form-control" placeholder="Max">
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label"><i class="fa fa-sort"></i> Tarix sıralaması</label>
                <select id="filter_sort" class="form-select">
                    <option value="desc">Yenidən köhnəyə</option>
                    <option value="asc">Köhnədən yeniyə</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label"><i class="fa fa-list"></i> Göstər</label>
                <select id="limit" class="form-select">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
                    <option value="9999999999999"><?= lang('Hamısı'); ?></option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4 d-flex" style="gap:6px;">
                <button type="button" id="applyFilters" class="btn btn-primary flex-fill">
                    <i class="fa fa-filter"></i> Filtr
                </button>
                <button type="button" id="resetFilters" class="btn btn-light" title="Sıfırla">
                    <i class="fa fa-times"></i>
                </button>
            </div>

            <?php if($userGroup == 1 || $userGroup == 2){ ?>
            <div class="col-12 text-end">
                <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-success btn-sm">
                    <i class="fa fa-file-excel-o"></i> Excel
                </button>
            </div>
            <?php } ?>

        </div>
    </div>

    <div id="dynamic_content" class="customersTable table-responsive">
        <center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>
    </div>

</div>

</body>
</html>

<?php include('inc/footer.php'); ?>

<script>

$(document).ready(function() {
    load_data(1);
});

function load_data(page) {
    $("#dynamic_content").css({ opacity: 0.5 });

    $.ajax({
        url: "/call/index.php?action=fetch&type=11",
        method: "POST",
        data: {
            page: page,
            query: $('#filter_query').val(),
            limit: $('#limit').val(),
            company: $('#filter_company').val(),
            sort: $('#filter_sort').val(),
            agreeUser: $('#filter_user').val(),
            date_from: $('#filter_date_from').val(),
            date_to: $('#filter_date_to').val(),
            agree_min: $('#filter_agree_min').val(),
            agree_max: $('#filter_agree_max').val(),
            default_min: $('#filter_default_min').val(),
            default_max: $('#filter_default_max').val(),
            price_min: $('#filter_price_min').val(),
            price_max: $('#filter_price_max').val()
        },

        beforeSend: function() {
            $("#dynamic_content").html('<center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>');
        },
        success: function(data) {
            $('#dynamic_content').html(data);
            $("#dynamic_content").css({ opacity: 1 });
        }
    });
}

$('#applyFilters').on('click', function() {
    load_data(1);
});

$('#resetFilters').on('click', function() {
    $('#filter_query').val('');
    $('#filter_company').val('');
    $('#filter_user').val('');
    $('#filter_sort').val('desc');
    $('#limit').val('10');
    $('#filter_date_from, #filter_date_to').val('');
    $('#filter_agree_min, #filter_agree_max').val('');
    $('#filter_default_min, #filter_default_max').val('');
    $('#filter_price_min, #filter_price_max').val('');
    load_data(1);
});

$('#filter_query').on('keypress', function(e) {
    if (e.which == 13) load_data(1);
});

$('#filter_company, #filter_sort, #limit, #filter_user').on('change', function() {
    load_data(1);
});

$(document).on('click', '.page-link', function() {
    var page = $(this).data('page_number');
    load_data(page);
});

</script>