<?php include('inc/header.php'); ?>
<div class="projects-section">
<form id="search" action="javascript:void(0);">
  <div class="row g-2 align-items-end">
    <div class="col-lg-2">
      <label class="form-label mb-1">Limit</label>
      <select id="limit" class="form-control select2A">
        <option value="10" selected>10</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="500">500</option><option value="1000">1000</option>
        <option value="9999999999999"><?= lang('Hamısı'); ?></option>
      </select>
    </div>

    <div class="col-lg-3">
      <label class="form-label mb-1">DQN</label>
      <input id="dqn" type="text" class="form-control" placeholder="77AA777">
    </div>

    <div class="col-lg-3">
      <label class="form-label mb-1">Status</label>
      <select id="status_filter" class="form-control select2A">
        <option value=""><?= lang('Bütün statuslar'); ?></option>
        <?php $sqlStatus=mysqli_query($db,"SELECT id,title FROM paramitems WHERE status=1 ORDER BY title ASC");
        while($st=mysqli_fetch_array($sqlStatus)) echo '<option value="'.$st['id'].'">'.$st['title'].'</option>'; ?>
      </select>
    </div>

    <div class="col-lg-2">
      <label class="form-label mb-1"><?= lang('Tarix'); ?> (from)</label>
      <input type="date" id="date_from" class="form-control">
    </div>

    <div class="col-lg-2">
      <label class="form-label mb-1"><?= lang('Tarix'); ?> (to)</label>
      <input type="date" id="date_to" class="form-control">
    </div>

    <div class="col-lg-2 d-flex gap-2">
      <button id="btnFilter" type="button" class="btn btn-primary w-100"><i class="fa fa-filter"></i></button>
      <button id="btnReset" type="button" class="btn btn-outline-secondary w-100"><i class="fa fa-refresh"></i></button>
    </div>

    <? if($userGroup==1 || $userGroup==2){ ?>
    <div class="col-lg-1">
      <label class="form-label mb-1">&nbsp;</label>
      <button type="button" id="exportToExcel" data-id=".customersTable" class="btn btn-success w-100"><i class="fa fa-file-excel-o"></i></button>
    </div>
    <? } ?>
  </div>
</form>

<div id="dynamic_content" class="customersTable table-responsive mt-3">
  <center><div class="spinner-grow" style="width:3rem;height:3rem" role="status"></div></center>
</div>
</div>

<? include('inc/footer.php'); ?>

<script>
$(function(){
  function spinner(){ return '<center><div class="spinner-grow" style="width:3rem;height:3rem" role="status"></div></center>'; }
  function getFilters(){
    return {status_filter:$("#status_filter").val(),date_from:$("#date_from").val(),date_to:$("#date_to").val()};
  }
  function load_data(page,query,limit,params){
    $("#dynamic_content").css({opacity:.5}).html(spinner());
    $.post("/call/index.php?action=fetch&type=9",$.extend({page:page,query:query,limit:limit,params:params||""},getFilters()),function(data){
      $("#dynamic_content").html(data).css({opacity:1});
    });
  }

  // initial
  load_data(1,"",$("#limit").val(),"");

  // Filter button
  $("#btnFilter").on("click",function(){
    load_data(1,$("#dqn").val().trim(),$("#limit").val(),"");
  });

  // Enter in DQN triggers filter
  $("#dqn").on("keypress",function(e){
    if(e.which===13){ e.preventDefault(); $("#btnFilter").click(); }
  });

  // Reset
  $("#btnReset").on("click",function(){
    $("#dqn").val(""); $("#status_filter").val("").trigger("change"); $("#date_from").val(""); $("#date_to").val("");
    $("#limit").val("10").trigger("change");
    load_data(1,"",$("#limit").val(),"");
  });

  // Auto-load on change (чтобы не было "поменял статус и ничего")
  $("#status_filter,#date_from,#date_to,#limit").on("change",function(){
    load_data(1,$("#dqn").val().trim(),$("#limit").val(),"");
  });

  // pagination (ВАЖНО: у тебя .page-link в output имеет data-page_number)
  $(document).on("click",".page-link",function(){
    var p=$(this).data("page_number"); if(!p) return;
    load_data(p,$("#dqn").val().trim(),$("#limit").val(),"");
  });

  // старый handler оставляем, но он у тебя специфический
  $(document).on("click",".search_account",function(){
    load_data(1,"",1000,$(this).data("id"));
  });

  // ВЫРУБАЕМ ЭТУ ХЕРНЮ:
  // setTimeout(function(){ window.location.reload(1); },20000);
});
</script>