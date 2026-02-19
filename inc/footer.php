<script>
  $(document).ready(function() {
    $(".select2").select2();

    $('.addNewSelect2').select2({tags: true}).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });

  });

  $(document).on('select2:open', () => {
    document.querySelector('.select2-search__field').focus();
  });

  $(document).ready(function() {
    $(".select2").select2({
      tags: true,
      dropdownParent: $(".addCustomer"),
    }).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });
  });

  $(document).ready(function() {
    $(".select2P").select2({
      tags: true,
      dropdownParent: $(".modalAddPayment"),
    }).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });
  });

  $(document).ready(function() {
    $(".select2O").select2({
      tags: true,
      dropdownParent: $(".modalAddOrder"),
    }).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });
  });

  $(document).ready(function() {
    $(".selectSu").select2({
      tags: true,
      dropdownParent: $(".successData"),
    }).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });
  });

  $(document).ready(function() {
    $(".selectPa").select2({
      tags: true,
      dropdownParent: $(".paymentData"),
    }).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });
  });

  $(document).ready(function() {
    $(".select2S").select2({
      tags: true,
      dropdownParent: $(".modalAddStatus"),
    }).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });
  });

  $(document).ready(function() {
    $(".select2U").select2({
      tags: true,
      dropdownParent: $(".addUser"),
    }).on('select2:open', function(e){
      $('.select2-search__field').attr('placeholder', '<?= lang('Axtar və ya Əlavə et'); ?>');
    });
  });

  function noty(text, type){
    new Noty({
      timeout: 4000,
      theme: 'nest',
      type: type,
      layout: 'topRight',
      closeWith: ['click', 'button'],
      text: text
    }).show();
  }

    $(document).ready(function() {

      $(document).on('click', '.inlineEditButton', function() {

          var itemId = $(this).attr("id");

          $(".editable-"+itemId).each(function() {

              var content = $(this).text();
              var column = $(this).data("column");
              $(this).html("<input class='editableInput exlike' type='text' name='"+column+"' value='"+content+"'>");

          });

          $(".editableSelect-"+itemId).each(function() {

              var content = $(this).text();
              var column = $(this).data("column");
              $(this).html("<select class='form-control' name='"+column+"'>"
              +"<option value=''>İstifadəçi dəyiş</option>"
              <?
                $sql = mysqli_query($db, "SELECT * FROM users WHERE deletedby = 0 AND status = 1");
                while($row = mysqli_fetch_array($sql)){ ?>
                  +"<option value='<?= $row['id']; ?>'><?= $row['name']; ?> <?= $row['surname']; ?></option>"
                <? }
              ?>
              +"</select>");

          });

          $(".inlineEdit-"+itemId).hide();
          $(".inlineSave-"+itemId).show();
              
      });

      $(document).on('click', '.inlineSaveButton', function() {

          var itemId = $(this).attr("id");
          var module_name = $(this).attr("module");
          var obj = {};
          var params = [];

          obj['itemId'] = itemId;

          $(".editable-"+itemId).each(function() {

              column = $(this).data("column");
              value = $(this).children().val();

              obj[column] = value;

              // if($(this).children().prop("name") =="params"){
              //   params.push(value);
              // }

          });

          $(".editableSelect-"+itemId).each(function() {

              column = $(this).data("column");
              value = $(this).children().find(":selected").val();

              obj[column] = value;

              // if($(this).children().prop("name") =="params"){
              //   params.push(value);
              // }

          });

          // var arrayParams = params;
          // obj['params'] = arrayParams.join(',');

          JSON.stringify(obj);

          $.ajax({
              url: "/index.php?action=add&type=1&module_name="+module_name,
              method: "POST",
              data: obj,
              beforeSend: function() {
              },
              success: function(data) {

                  console.log(data);

                  $(".editable-"+itemId).each(function() {

                      var value = $(this).children().val();
                      $(this).text(value);

                  });
                  
                  $(".editableSelect-"+itemId).each(function() {

                      var value = $(this).children().find(":selected").text();
                      $(this).text(value);

                  });

                  $(".inlineEdit-"+itemId).show();
                  $(".inlineSave-"+itemId).hide();

              }
          });

      });
      
    $(document).on('click', '.confirm', function() {

        var itemId = $(this).attr("id");
        var module_name = $(this).attr("module");
        
        $.ajax({
            url: "/index.php?action=add&type=11",
            method: "POST",
            data: {
                itemId: itemId,
                module_name: module_name
            },
            beforeSend: function() {
                $("#data-" + itemId).css("background-color", "red");
                $('.confirm-' + itemId).html('<div class="spinner-grow" style="width: 1rem; height: 1rem;" role="status"><span class="sr-only"></span></div>');
            },
            success: function(data) {
              $('.confirm-' + itemId).remove();
              $('.delete-' + itemId).remove();
              $('.inlineEdit-' + itemId).remove();
            }
        });
    });
    
    $(document).on('click', '.status', function() {

            var itemId = $(this).attr("id");
            var status = $(this).data("status");
            var module_name = $(this).attr("module");

            if (status == 1) {
                var statusLabel = '<label class="badge badge-success"><?= lang('Aktiv'); ?></label>';
                var datastatusIcon = '<i class="fa fa-lock"></i>';
            } else if (status == 0) {
                var statusLabel = '<label class="badge badge-danger"><?= lang('Passiv'); ?></label>';
                var datastatusIcon = '<i class="fa fa-unlock-alt"></i>';
            }

            $.ajax({
                url: "/index.php?action=add&type=12",
                method: "POST",
                data: {
                    itemId: itemId,
                    status: status,
                    module_name: module_name
                },
                beforeSend: function() {
                    $("#data-" + itemId).css("background-color", "red");
                    $(".status-" + itemId).html(
                        '<div class="spinner-grow" style="width: 1rem; height: 1rem;" role="status"><span class="sr-only"></span></div>'
                    );
                },
                success: function(data) {
                    $("#data-" + itemId).css("background-color", "white");
                    $(".statusLabel-" + itemId).html(statusLabel);
                    $(".status-" + itemId).html(datastatusIcon);
                }
            });

    });
    

      $(document).on('click', '.delete', function() {

        var module_name = $(this).attr("module");
        var itemId = $(this).attr("id");

        if (confirm('<?= lang("Silmək istədiyinizdən əminsiniz ?"); ?>')) {
        
          $.ajax({
              url: "/index.php?action=add&type=2",
              method: "POST",
              data: {
                itemId: itemId,
                module_name: module_name
              },
              beforeSend: function() {
                  $("#data-" + itemId).css("background-color", "red");
                  $("#data-" + itemId).css("color", "white");
              },
              success: function(data) {
                  if (data == 1) {
                      $("#data-" + itemId).hide();
                  }
              }
          });


        }

      });

    });
  
    function copyToClipboard(element) {
      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(element).select();
      document.execCommand("copy");
      $temp.remove();
    }

    $(".copycolor").click(function(e){
      $(this).css('color', '#00A46C');
    });

    $("#exportToExcel").click(function(e){

					var tableName = $(this).data("id");
          var table = $(tableName);

          if(tableName == ""){
					  var table = $('.table');
          }

					if(table && table.length){
						var preserveColors = (table.hasClass('table-hover') ? true : false);
						$(table).table2excel({
							exclude: ".noExl",
							name: "NINJA Excel",
							filename: "NINJA (<? echo date('d.m.Y H:i'); ?>).xls",
							fileext: ".xls",
							exclude_img: true,
							exclude_links: true,
							exclude_inputs: true,
							preserveColors: preserveColors
						});
					}
	  });
</script>