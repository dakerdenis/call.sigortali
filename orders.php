<?php include('inc/header.php'); ?>

    <div class="projects-section">


        <div class="row">
                    <div class="col-lg-12 grid-margin">
                        <div class="card">
                        <div class="card-body">
                            <div id="dynamic_content_grid_finaccounts" class="row icons-list">

                                <?php

                                    $sql = mysqli_query($db, "SELECT * FROM finaccounts WHERE status = 1 AND deletedby = 0");

                                    while($row = mysqli_fetch_array($sql)){

                                        $getIncomes = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as amount FROM payments WHERE toAccount = '".$row['title']."' AND deletedby = 0"));
                                        $getOutgoing = mysqli_fetch_array(mysqli_query($db, "SELECT SUM(amount) as amount FROM payments WHERE fromAccount = '".$row['title']."' AND deletedby = 0"));
                                        $balance = round($getIncomes['amount'] - $getOutgoing['amount'], 2);

                                        if($row['title'] == 'Özü ödədi'){
                                        $balance = '';
                                        } else{
                                        $balance = '('.$balance.')';
                                        }

                                        echo '<a href="/addorder/'.$row['id'].'" style="width: auto; margin: 5px;" class="btn btn-primary">'.$row['title'].' '.$balance.'</a>';
                                    }

                                ?>

                            </div>
                        </div>
                        </div>
                    </div>
                </div>

        <hr>

        <div class="row">
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
            <div class="col-lg-3 grid-margin stretch-card">
                <? if($userGroup == 1 || $userGroup == 2){ ?>
                    <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-primary"><i class="fa fa-file-excel-o"></i></button>
                <? } ?>
                <a href="/ordersconfirmed" class="btn btn-primary"><?= lang('Təsdiqlənənlər'); ?></a>
            </div>
        </div>

        <div id="dynamic_content" class="customersTable table-responsive">
          <center><div class="spinner-grow" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only"></span></div></center>
        </div>

    </div>

</body>

</html>

<? include('inc/footer.php'); ?>

<script>

    $(document).ready(function() {

        load_data(1, '', 10);

        $('#search').on('submit', (function(e) {

            e.preventDefault();

            var limit = $('#limit').val();
            var query = $(".search-input").val();
            load_data(1, query, limit, 'not');

        }));

    });

    function load_data(page, query = '', limit = '', confirmed = 'not', params = '') {
        
        $("#dynamic_content").css({
            opacity: 0.5
        });
        $.ajax({
            url: "/index.php?action=fetch&type=10",
            method: "POST",
            data: {
                page: page,
                query: query,
                limit: limit,
                confirmed: confirmed,
                params: params
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
        load_data(page, query, limit, 'not');
    });

    $(document).on('click', '.search_account', function() {
        var id = $(this).data('id');
        load_data(1, '', 1000, 'not', id);
    });

    $("#limit").on("change", function() {
        var limit = $(this).val();
        var query = $(".search_box_form").val();
        load_data(1, query, limit, 'not');
    });

</script>