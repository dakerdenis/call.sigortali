<?php include('inc/header.php'); ?>



      <div class="projects-section">



        <div class="row">

                            <div class="col-lg-3 grid-margin stretch-card">

                                <select id="limit" class="full-width">

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

                                <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-primary"><i class="fa fa-file-excel-o"></i></button>

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

            load_data(1, query, limit);



        }));



    });



    function load_data(page, query = '', limit = '') {

        

        $("#dynamic_content").css({

            opacity: 0.5

        });

        $.ajax({

            url: "/index.php?action=fetch&type=2",

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

    

    $("#limit").on("change", function() {

        var limit = $(this).val();

        var query = $(".search_box_form").val();

        load_data(1, query, limit);

    });



</script>