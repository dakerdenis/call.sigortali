<?php include('inc/header.php'); ?><div class="projects-section">
    <form id="search" action="javascript:void(0);">
        <div class="row g-2 align-items-end">
            <div class="col-lg-2"><label class="form-label mb-1">Limit</label><select id="limit" class="form-control">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
                    <option value="9999999999999"><?= lang('Hamısı'); ?></option>
                </select></div>
            <div class="col-lg-2"><label class="form-label mb-1">Bitmə tarixi - dən</label><input type="date" id="date_from" class="form-control"></div>
            <div class="col-lg-2"><label class="form-label mb-1">Bitmə tarixi - dək</label><input type="date" id="date_to" class="form-control"></div>
            <div class="col-lg-3"><label class="form-label mb-1">Search</label><input type="text" id="search_query" class="form-control" placeholder="DQN / Ad / Pin / Telefon"></div>
            <div class="col-lg-2 d-flex gap-2"><button type="button" id="btnFilter" class="btn btn-primary w-100"><i class="fa fa-filter"></i></button><button type="button" id="btnReset" class="btn btn-outline-secondary w-100"><i class="fa fa-refresh"></i></button></div>
            <div class="col-lg-1"><button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-success w-100"><i class="fa fa-file-excel-o"></i></button></div>
        </div>
    </form>
    <div id="dynamic_content" class="customersTable table-responsive mt-3">
        <center>
            <div class="spinner-grow" style="width:3rem;height:3rem"></div>
        </center>
    </div>
</div><? include('inc/footer.php'); ?><script>
    $(function() {
        function spinner() {
            return '<center><div class="spinner-grow" style="width:3rem;height:3rem"></div></center>';
        }

        function load_data(page, query, limit) {
            $("#dynamic_content").css({
                opacity: .5
            }).html(spinner());
            $.post("/index.php?action=fetch&type=22", {
                page: page,
                query: query,
                limit: limit,
                date_from: $("#date_from").val(),
                date_to: $("#date_to").val()
            }, function(data) {
                $("#dynamic_content").html(data).css({
                    opacity: 1
                });
            });
        }
        load_data(1, "", $("#limit").val());
        $("#btnFilter").on("click", function() {
            load_data(1, $("#search_query").val().trim(), $("#limit").val());
        });
        $("#search_query").on("keypress", function(e) {
            if (e.which == 13) {
                e.preventDefault();
                $("#btnFilter").click();
            }
        });
        $("#btnReset").on("click", function() {
            $("#search_query").val("");
            $("#date_from").val("");
            $("#date_to").val("");
            $("#limit").val("10");
            load_data(1, "", $("#limit").val());
        });
        $("#limit").on("change", function() {
            load_data(1, $("#search_query").val().trim(), $(this).val());
        });
        $(document).on("click", ".page-link", function() {
            var page = $(this).data("page_number");
            load_data(page, $("#search_query").val().trim(), $("#limit").val());
        });
    });
</script>