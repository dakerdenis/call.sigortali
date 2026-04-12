<?php include('inc/header.php'); ?>
<div class="projects-section">
    <div class="card mb-3 shadow-sm">
        <div class="card-body">

            <div class="row g-2">

                <div class="col-lg-2">
                    <input type="text" id="search_query" class="form-control" placeholder="DQN / Şəhadətnamə">
                </div>

                <div class="col-lg-2">
                    <select id="insurance_type" class="form-control select2A">
                        <option value="">Sığorta növü</option>
                        <option value="İCBARİ">İcbari</option>
                        <option value="KASKO">Kasko</option>
                        <option value="ƏMLAK">Əmlak</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <select id="company_filter" class="form-control select2A">
                        <option value="">Şirkət</option>

                        <?php
                        $q = mysqli_query($db, "SELECT id,title FROM companies WHERE status=1 AND deletedby=0 ORDER BY title");
                        while ($r = mysqli_fetch_array($q)) {
                            echo '<option value="' . $r['id'] . '">' . $r['title'] . '</option>';
                        }
                        ?>

                    </select>
                </div>

                <div class="col-lg-2">
                    <select id="payment_type" class="form-control select2A">
                        <option value="">Ödəniş tipi</option>
                        <option value="KART">Kart</option>
                        <option value="NAĞD">Nağd</option>
                        <option value="TRANSFER">Transfer</option>
                    </select>
                </div>

                <div class="col-lg-2">
                    <input type="date" id="date_from" class="form-control">
                </div>

                <div class="col-lg-2">
                    <input type="date" id="date_to" class="form-control">
                </div>

            </div>


            <div class="row g-2 mt-2">

                <div class="col-lg-2">
                    <input type="number" id="price_min" class="form-control" placeholder="Qiymət min">
                </div>

                <div class="col-lg-2">
                    <input type="number" id="price_max" class="form-control" placeholder="Qiymət max">
                </div>

                <div class="col-lg-2">

                    <select id="limit" class="form-control select2A">

                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="500">500</option>
                        <option value="1000">1000</option>

                    </select>

                </div>

                <div class="col-lg-2">
                    <button id="btnFilter" class="btn btn-primary w-100">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                </div>

                <div class="col-lg-2">
                    <button id="btnReset" class="btn btn-secondary w-100">
                        <i class="fa fa-refresh"></i> Reset
                    </button>
                </div>

                <div class="col-lg-2">

                    <button type="button" id="exportToExcel" class="btn btn-success w-100">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </button>

                </div>

            </div>

        </div>
    </div>


    <div id="dynamic_content" class="customersTable table-responsive">
        <center>
            <div class="spinner-grow" style="width:3rem;height:3rem"></div>
        </center>
    </div>
</div>
<? include('inc/footer.php'); ?>

<script>
    let ACCOUNTING_FILTERS = {
        query: '',
        date_from: '',
        date_to: '',
        price_min: '',
        price_max: '',
        insurance_type: '',
        company: '',
        payment_type: '',
    };

    function getFilters() {
        ACCOUNTING_FILTERS.query = $("#search_query").val();
        ACCOUNTING_FILTERS.date_from = $("#date_from").val();
        ACCOUNTING_FILTERS.date_to = $("#date_to").val();
        ACCOUNTING_FILTERS.price_min = $("#price_min").val();
        ACCOUNTING_FILTERS.price_max = $("#price_max").val();
        ACCOUNTING_FILTERS.insurance_type = $("#insurance_type").val();
        ACCOUNTING_FILTERS.company = $("#company_filter").val();
        ACCOUNTING_FILTERS.payment_type = $("#payment_type").val();
        return ACCOUNTING_FILTERS;
    }

    $(document).ready(function() {
        sessionStorage.removeItem('borclular_param');
        load_data(1, '', $("#limit").val(), {});

        $("#btnFilter").click(function() {
            var limit = $("#limit").val();
            load_data(1, '', limit, getFilters());
        });

        $("#btnReset").click(function() {
            $("#search_query").val('');
            $("#date_from").val('');
            $("#date_to").val('');
            $("#price_min").val('');
            $("#price_max").val('');
            ACCOUNTING_FILTERS = {};
            load_data(1, '', $("#limit").val(), {});
        });

        $("#limit").on("change", function() {
            load_data(1, '', $(this).val(), getFilters());
        });

        $(document).on('click', '.page-link', function() {
            var page = $(this).data('page_number');
            load_data(page, '', $("#limit").val(), getFilters());
        });

        $(document).on('click', '.search_account', function() {
            var id = $(this).data('id');
            sessionStorage.setItem('borclular_param', id);
            var limit = id == 'Borclular' ? 1000000 : 10;
            load_data(1, '', limit, getFilters(), id);
        });
    });

    function load_data(page, query = '', limit = '', filters = {}, params = '') {
        if (!params) {
            params = sessionStorage.getItem('borclular_param') || '';
        }
        $("#dynamic_content").css({
            opacity: 0.5
        });
        $.ajax({
            url: "/call/index.php?action=fetch&type=5",
            method: "POST",
            data: {
                page: page,
                query: filters.query || '',
                limit: limit,
                params: params,
                date_from: filters.date_from || '',
                date_to: filters.date_to || '',
                price_min: filters.price_min || '',
                price_max: filters.price_max || '',
                insurance_type: filters.insurance_type || '',
                company: filters.company || '',
                payment_type: filters.payment_type || '',
            },
            beforeSend: function() {
                $("#dynamic_content").html('<center><div class="spinner-grow" style="width:3rem;height:3rem"></div></center>');
            },
            success: function(data) {
                $("#dynamic_content").html(data);
                $("#dynamic_content").css({
                    opacity: 1
                });
            }
        });
    }

    $("#exportToExcel").click(function() {

        var table = document.querySelector(".customersTable table");
        if (!table) return;

        var html = table.outerHTML;

        var url = 'data:application/vnd.ms-excel,' + encodeURIComponent(html);
        var a = document.createElement('a');
        a.href = url;
        a.download = 'accounting-report.xls';
        a.click();

    });
</script>