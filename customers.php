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

            <div class="col-lg-4 col-md-6">
                <label class="form-label"><i class="fa fa-search"></i> <?= lang('Axtarış'); ?></label>
                <input type="text" id="filter_query" class="form-control" placeholder="Ad, DQN, FİN, telefon, şəhadətnamə...">
            </div>

            <div class="col-lg-3 col-md-6">
                <label class="form-label"><i class="fa fa-sort"></i> Bitmə tarixi</label>
                <select id="filter_sort" class="form-select">
                    <option value="asc">Yaxın bitənlər əvvəl</option>
                    <option value="desc">Uzaq bitənlər əvvəl</option>
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

            <div class="col-lg-3 col-md-8 d-flex" style="gap:6px;">
                <button type="button" id="applyFilters" class="btn btn-primary flex-fill">
                    <i class="fa fa-filter"></i> Filtr
                </button>
                <button type="button" id="resetFilters" class="btn btn-light" title="Sıfırla">
                    <i class="fa fa-times"></i>
                </button>
                <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-success" title="Excel">
                    <i class="fa fa-file-excel-o"></i>
                </button>
            </div>

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
        url: "/call/index.php?action=fetch&type=2",
        method: "POST",
        data: {
            page: page,
            query: $('#filter_query').val(),
            limit: $('#limit').val(),
            sort: $('#filter_sort').val()
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
    $('#filter_sort').val('asc');
    $('#limit').val('10');
    load_data(1);
});

$('#filter_query').on('keypress', function(e) {
    if (e.which == 13) load_data(1);
});

$('#filter_sort, #limit').on('change', function() {
    load_data(1);
});

$(document).on('click', '.page-link', function() {
    var page = $(this).data('page_number');
    load_data(page);
});

</script>