<?php include('inc/header.php'); ?>
<div class="container-fluid py-3">
    <!-- HEADER -->
    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 mb-3">
        <div>
            <h3 class="mb-1 fw-bold">Orders <span class="text-muted">Reports</span> - Təsdiq gözləyənlər</h3>
            <div class="text-muted small">Ödəniş həsabatları və sifariş gəlirləri</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <? if ($userGroup == 1 || $userGroup == 2) { ?>
                <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-outline-success">
                    <i class="fa fa-file-excel-o me-1"></i> Export
                </button>
            <? } ?>

            <a href="/call/ordersconfirmed" class="btn btn-outline-primary">
                <i class="fa fa-check-circle me-1"></i> <?= lang('Təsdiqlənənlər'); ?>
            </a>
        </div>
    </div>
    <!-- ACCOUNTS -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white d-flex align-items-center justify-content-between">
            <div class="fw-semibold">Hesablar</div>
            <div class="text-muted small">Kartlar üzrə balans</div>
        </div>
        <div class="card-body">
            <div id="dynamic_content_grid_finaccounts" class="row icons-list g-2">
                <?php
                $sql = mysqli_query($db, "SELECT * FROM finaccounts WHERE status = 1 AND deletedby = 0");
                while ($row = mysqli_fetch_array($sql)) {
                    $getIncomes = mysqli_fetch_array(mysqli_query(
                        $db,
                        "SELECT SUM(amount) as amount FROM payments WHERE toAccount = '" . $row['title'] . "' AND deletedby = 0"
                    ));
                    $getOutgoing = mysqli_fetch_array(mysqli_query(
                        $db,
                        "SELECT SUM(amount) as amount FROM payments WHERE fromAccount = '" . $row['title'] . "' AND deletedby = 0"
                    ));
                    $balance = round($getIncomes['amount'] - $getOutgoing['amount'], 2);
                    if ($row['title'] == 'Özü ödədi') {
                        $balance = '';
                    } else {
                        $balance = '(' . $balance . ')';
                    }
                    echo '
                    <div class="col-auto">
                    <a href="/call/addorder/' . $row['id'] . '" class="btn btn-primary">
                    ' . $row['title'] . ' ' . $balance . '
                    </a>
                    </div>
                    ';
                }
                ?>
            </div>
        </div>
    </div>
    <!-- ORDERS + FILTERS -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3">
                <div class="fw-semibold">Orders</div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted small d-none d-md-inline">Göstər:</span>
                    <select id="limit" class="form-select form-select-sm select2A" style="min-width:140px">
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
            <!-- FILTERS -->
            <div class="p-3 border rounded-3 bg-light mb-3">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="fw-semibold">Filterlər</div>
                    <div class="text-muted small">Tarix / hesab / qazanc</div>
                </div>
                <form id="ordersFilters">
                    <div class="row g-2">
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small text-muted mb-1"><?= lang('Tarixdən'); ?></label>
                            <input type="date" id="date_from" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small text-muted mb-1"><?= lang('Tarixədək'); ?></label>
                            <input type="date" id="date_to" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small text-muted mb-1"><?= lang('Hesab'); ?></label>
                            <select id="accountId" class="form-select form-select-sm select2A">
                                <option value=""><?= lang('Hamısı'); ?></option>
                                <?php
                                    $sqlAcc = mysqli_query(
                                        $db,
                                        "SELECT title FROM finaccounts WHERE status=1 AND deletedby=0 ORDER BY title ASC"
                                    );
                                    while ($acc = mysqli_fetch_array($sqlAcc)) {
                                        echo '<option value="' . htmlspecialchars($acc['title']) . '">' . htmlspecialchars($acc['title']) . '</option>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small text-muted mb-1"><?= lang('Hara ödənilib'); ?></label>
                            <select id="toAccount" class="form-select form-select-sm select2A">
                                <option value=""><?= lang('Hamısı'); ?></option>
                                <?php
                                $sqlAcc2 = mysqli_query(
                                    $db,
                                    "SELECT title FROM finaccounts WHERE status=1 AND deletedby=0 ORDER BY title ASC"
                                );

                                while ($acc2 = mysqli_fetch_array($sqlAcc2)) {

                                    echo '<option value="' . htmlspecialchars($acc2['title']) . '">' . htmlspecialchars($acc2['title']) . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small text-muted mb-1"><?= lang('Status'); ?></label>
                            <select id="confirmed" class="form-select form-select-sm">
                                <option value=""><?= lang('Hamısı'); ?></option>
                                <option value="not"><?= lang('Təsdiq gözləyir'); ?></option>
                                <option value="yes"><?= lang('Təsdiqləndi'); ?></option>
                            </select>
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small text-muted mb-1"><?= lang('Qazanc min'); ?></label>
                            <input type="number" step="0.01" id="profit_min" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3 col-lg-2">
                            <label class="form-label small text-muted mb-1"><?= lang('Qazanc max'); ?></label>
                            <input type="number" step="0.01" id="profit_max" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6 col-lg-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fa fa-filter me-1"></i> Filter
                            </button>
                            <button type="button" id="filtersReset" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fa fa-refresh me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- TABLE -->
            <div id="dynamic_content" class="customersTable table-responsive rounded-3 border">
                <div class="py-5 text-center">
                    <div class="spinner-grow" style="width:3rem;height:3rem" role="status">
                        <span class="visually-hidden">Loading</span>
                    </div>
                    <div class="text-muted mt-2">Yüklənir...</div>
                </div>
            </div>
        </div>
    </div>
</div>
<? include('inc/footer.php'); ?>
<script>
    let ORDERS_FILTERS = {
        date_from: '',
        date_to: '',
        accountId: '',
        toAccount: '',
        confirmed: 'not', // по умолчанию как сейчас (неподтвержденные)
        profit_min: '',
        profit_max: ''
    };
    function getFiltersFromUI() {
        return {
            date_from: $('#date_from').val(),
            date_to: $('#date_to').val(),
            accountId: $('#accountId').val(),
            toAccount: $('#toAccount').val(),
            confirmed: $('#confirmed').val(),
            profit_min: $('#profit_min').val(),
            profit_max: $('#profit_max').val()
        };
    }
    function applyFiltersToUI(f) {
        $('#date_from').val(f.date_from || '');
        $('#date_to').val(f.date_to || '');
        $('#accountId').val(f.accountId || '').trigger('change');
        $('#toAccount').val(f.toAccount || '').trigger('change');
        $('#confirmed').val(f.confirmed || '');
        $('#profit_min').val(f.profit_min || '');
        $('#profit_max').val(f.profit_max || '');
    }
    $(document).ready(function() {
        // init default
        applyFiltersToUI(ORDERS_FILTERS);
        load_data(1, '', $('#limit').val(), ORDERS_FILTERS);
        $('#search').on('submit', function(e) {
            e.preventDefault();
            var limit = $('#limit').val();
            var query = $(".search-input").val();
            ORDERS_FILTERS = {
                ...ORDERS_FILTERS,
                ...getFiltersFromUI()
            };
            load_data(1, query, limit, ORDERS_FILTERS);
        });
        $('#ordersFilters').on('submit', function(e) {
            e.preventDefault();
            var limit = $('#limit').val();
            var query = $(".search-input").val();
            ORDERS_FILTERS = {
                ...ORDERS_FILTERS,
                ...getFiltersFromUI()
            };
            load_data(1, query, limit, ORDERS_FILTERS);
        });
        $('#filtersReset').on('click', function() {
            ORDERS_FILTERS = {
                date_from: '',
                date_to: '',
                accountId: '',
                toAccount: '',
                confirmed: 'not',
                profit_min: '',
                profit_max: ''
            };
            applyFiltersToUI(ORDERS_FILTERS);
            var limit = $('#limit').val();
            var query = $(".search-input").val();
            load_data(1, query, limit, ORDERS_FILTERS);
        });

        $(document).on('click', '.quickDate', function() {
            const range = $(this).data('range');

            const pad = (n) => (n < 10 ? '0' + n : n);
            const toISO = (d) => d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());

            let now = new Date();
            let from = '';
            let to = '';

            if (range === 'today') {
                from = toISO(now);
                to = toISO(now);
            } else if (range === 'this_month') {
                let first = new Date(now.getFullYear(), now.getMonth(), 1);
                let last = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                from = toISO(first);
                to = toISO(last);
            } else {
                let days = parseInt(range, 10);
                let start = new Date(now);
                start.setDate(now.getDate() - (days - 1));
                from = toISO(start);
                to = toISO(now);
            }

            $('#date_from').val(from);
            $('#date_to').val(to);

            ORDERS_FILTERS = {
                ...ORDERS_FILTERS,
                ...getFiltersFromUI()
            };
            var limit = $('#limit').val();
            var query = $(".search-input").val();
            load_data(1, query, limit, ORDERS_FILTERS);
        });

    });


function load_data(page, query = '', limit = '', filters = {}, params = '') {
    $("#dynamic_content").css({ opacity: 0.5 });

    $.ajax({
        url: "/call/index.php?action=fetch&type=10",
        type: "POST",
        cache: false,
        data: {
            page: page,
            query: query || '',
            limit: limit || 10,
            params: params || '',
            confirmed: (typeof filters.confirmed !== 'undefined' ? filters.confirmed : 'not'),
            date_from: filters.date_from || '',
            date_to: filters.date_to || '',
            accountId: filters.accountId || '',
            toAccount: filters.toAccount || '',
            profit_min: filters.profit_min || '',
            profit_max: filters.profit_max || ''
        },
        beforeSend: function() {
            $("#dynamic_content").html('<center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>');
        },
        success: function(data) {
            $("#dynamic_content").html(data).css({ opacity: 1 });
        },
        error: function(xhr) {
            $("#dynamic_content").html('<div class="p-3 text-danger">AJAX ERROR</div>').css({ opacity: 1 });
            console.log(xhr.responseText);
        }
    });
}



    $(document).on('click', '.page-link', function() {
        var page = $(this).data('page_number');
        var query = $(".search-input").val();
        var limit = $('#limit').val();
        load_data(page, query, limit, ORDERS_FILTERS);
    });
$("#limit").on("change", function() {
    var limit = $(this).val();
    var query = $(".search-input").val() || '';
    ORDERS_FILTERS = {
        ...ORDERS_FILTERS,
        ...getFiltersFromUI()
    };
    load_data(1, query, limit, ORDERS_FILTERS);
});
</script>