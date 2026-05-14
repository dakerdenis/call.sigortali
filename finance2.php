<?php include('inc/header.php'); ?>

<div class="container-fluid py-3">

  <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 mb-3">
    <div>
      <h3 class="mb-1 fw-bold">Finance <span class="text-muted">3.0</span></h3>
      <div class="text-muted small">Yeni ödəniş sistemi</div>
    </div>
    <div class="d-flex flex-wrap gap-2">
      <button type="button" id="exportToExcel" data-id=".paymentsTable" class="btn btn-outline-success">
        <i class="fa fa-file-excel-o me-1"></i> Export
      </button>
      <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddPayment2">
        <i class="fa fa-plus me-1"></i> Ödəniş əlavə et
      </button>
    </div>
  </div>

  <!-- Summary cards -->
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-4 border-success">
        <div class="card-body">
          <div class="text-muted small">Gəlir (Mədaxil + Transfer gələn)</div>
          <div class="fs-4 fw-bold text-success" id="totalIncome">0.00 ₼</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-4 border-danger">
        <div class="card-body">
          <div class="text-muted small">Xərc (Sığorta + Xərc + Məxaric + Transfer gedən)</div>
          <div class="fs-4 fw-bold text-danger" id="totalExpense">0.00 ₼</div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card shadow-sm border-start border-4 border-primary">
        <div class="card-body">
          <div class="text-muted small">Balans</div>
          <div class="fs-4 fw-bold text-primary" id="totalBalance">0.00 ₼</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="row g-2 align-items-end">
        <div class="col-md-2">
          <label class="form-label small">Tarixdən</label>
          <input type="date" id="f_date_from" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Tarixə</label>
          <input type="date" id="f_date_to" class="form-control form-control-sm">
        </div>
        <div class="col-md-2">
          <label class="form-label small">Kateqoriya</label>
          <select id="f_category" class="form-select form-select-sm">
            <option value="">Hamısı</option>
            <option value="xercler">Xərclər</option>
            <option value="dovriyye">Dövriyyə</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Alt kateqoriya</label>
          <select id="f_subcategory" class="form-select form-select-sm">
            <option value="">Hamısı</option>
            <option value="sigorta">Sığorta ödənişləri</option>
            <option value="xerc">Xərc</option>
            <option value="medaxil">Mədaxil</option>
            <option value="mexaric">Məxaric</option>
            <option value="transfer">Transfer</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label small">Limit</label>
          <select id="f_limit" class="form-select form-select-sm">
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="500">500</option>
          </select>
        </div>
        <div class="col-md-2 d-flex gap-1">
          <button class="btn btn-primary btn-sm w-100" id="btnFilter"><i class="fa fa-search"></i> Axtar</button>
          <button class="btn btn-outline-secondary btn-sm" id="btnReset"><i class="fa fa-times"></i></button>
        </div>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div id="paymentsTableWrap" class="paymentsTable table-responsive">
        <div class="py-5 text-center">
          <div class="spinner-grow" role="status"></div>
          <div class="text-muted mt-2">Yüklənir...</div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include('inc/footer.php'); ?>

<!-- ====== MODAL ====== -->
<div class="modal fade" id="modalAddPayment2" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content shadow">

      <div class="modal-header">
        <h5 class="modal-title">Ödəniş əlavə et</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="formPayment2">
        <div class="modal-body">

          <!-- Step 1: Category -->
          <div class="row g-3 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Kateqoriya <span class="text-danger">*</span></label>
              <select id="p2_category" name="category" class="form-select" required>
                <option value="">Seçin</option>
                <option value="xercler">Xərclər</option>
                <option value="dovriyye">Dövriyyə</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Alt kateqoriya <span class="text-danger">*</span></label>
              <select id="p2_subcategory" name="subcategory" class="form-select" required disabled>
                <option value="">Əvvəlcə kateqoriya seçin</option>
              </select>
            </div>
          </div>

          <!-- Step 2: Dynamic subtype -->
          <div class="row g-3 mb-3" id="row_subtype" style="display:none;">
            <div class="col-md-6">
              <label class="form-label fw-bold" id="lbl_subtype">Növ</label>
              <select id="p2_subtype" name="subtype" class="form-select">
                <option value="">Seçin</option>
              </select>
            </div>
            <div class="col-md-6" id="row_payer" style="display:none;">
              <label class="form-label fw-bold">Kim ödəyir? <span class="text-danger">*</span></label>
              <select id="p2_payer" name="payer_type" class="form-select">
                <option value="">Seçin</option>
                <option value="sigortali_odedi">Sığortalı ödədi (xərc)</option>
                <option value="musteri_ozu">Müştəri özü ödədi (0)</option>
              </select>
            </div>
          </div>

          <!-- Step 3: Accounts -->
          <div class="row g-3 mb-3">
            <div class="col-md-6" id="row_from">
              <label class="form-label">Hardan <span class="text-danger">*</span></label>
              <select id="p2_from" name="from_account" class="form-select" required>
                <option value="">Seçin</option>
                <?php
                  $sqlFA = mysqli_query($db, "SELECT title FROM finaccounts WHERE id != 1 AND status = 1 AND deletedby = 0");
                  while ($r = mysqli_fetch_array($sqlFA)) {
                    echo '<option value="'.htmlspecialchars($r['title']).'">'.htmlspecialchars($r['title']).'</option>';
                  }
                ?>
              </select>
            </div>
            <div class="col-md-6" id="row_to">
              <label class="form-label">Hara <span class="text-danger">*</span></label>
              <input type="text" id="p2_to" name="to_account" class="form-control" placeholder="FİN / VOEN / kart">
            </div>
          </div>

          <!-- Step 4: Identification + DQN (only for sigorta) -->
          <div class="row g-3 mb-3" id="row_insurance_fields" style="display:none;">
            <div class="col-md-6">
              <label class="form-label">Şəhadətnamə nömrəsi</label>
              <input type="text" id="p2_identification" name="identification" class="form-control" placeholder="Axtarış..." autocomplete="off">
              <div id="p2_identDropdown" style="display:none;position:absolute;left:0;right:0;z-index:1050;background:#fff;border:1px solid #ddd;border-radius:0 0 8px 8px;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,0.1);"></div>
            </div>
            <div class="col-md-6" id="row_dqn" style="display:none;">
              <label class="form-label">DQN</label>
              <input type="text" id="p2_car_id" name="car_id" class="form-control" placeholder="DQN">
            </div>
          </div>

          <!-- Step 5: Amount + Date -->
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Məbləğ <span class="text-danger">*</span></label>
              <input type="number" step="0.01" min="0" id="p2_amount" name="amount" class="form-control" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Tarix <span class="text-danger">*</span></label>
              <input type="date" id="p2_date" name="paydate" class="form-control" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-4">
              <label class="form-label">Effekt</label>
              <div id="p2_effect_badge" class="mt-1">
                <span class="badge bg-secondary">Kateqoriya seçin</span>
              </div>
              <input type="hidden" id="p2_effect" name="effect" value="0">
            </div>
          </div>

          <!-- Note -->
          <div class="mb-3" id="row_note">
            <label class="form-label">Qeyd <span id="note_required_star" class="text-danger" style="display:none;">*</span></label>
            <textarea name="note" id="p2_note" class="form-control" rows="2" placeholder="Qeyd"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Bağla</button>
          <button type="submit" class="btn btn-primary" id="btnSavePayment2">
            <i class="fa fa-save me-1"></i> Yadda saxla
          </button>
        </div>
      </form>

    </div>
  </div>
</div>

<script>
// ======= CONFIG =======
var SUBCATS = {
  xercler: [
    {value:'sigorta', label:'Sığorta ödənişləri'},
    {value:'xerc', label:'Xərc'}
  ],
  dovriyye: [
    {value:'medaxil', label:'Mədaxil'},
    {value:'mexaric', label:'Məxaric'},
    {value:'transfer', label:'Transfer'}
  ]
};

var SUBTYPES = {
  sigorta: [
    {value:'icbari', label:'İcbari'},
    {value:'kasko', label:'Kasko'},
    {value:'tibbi', label:'Tibbi sığorta'},
    {value:'emlak', label:'Əmlak'},
    {value:'elave_mes', label:'Əlavə Məsuliyyət sığortası'},
    {value:'yasil_kart', label:'Yaşıl kart'},
    {value:'yuk', label:'Yük sığortası'}
  ],
  xerc: [
    {value:'tibbi', label:'Tibbi sığorta'},
    {value:'icare', label:'İcarə'},
    {value:'kommunal', label:'Kommunal'},
    {value:'diger', label:'Digər ofis xərcləri'},
    {value:'mobil', label:'Mobil nömrələr'},
    {value:'emek', label:'Əmək haqları'},
    {value:'dsfm', label:'DSFM'},
    {value:'vergi', label:'Vergi'}
  ],
  medaxil: [
    {value:'hesab_elave', label:'Şirkətin hesabına əlavə'},
    {value:'musteriden', label:'Müştəridən köçürmə'}
  ],
  mexaric: [
    {value:'vesait_negdl', label:'Vəsaitin nəğdlaşdırılması'},
    {value:'avans', label:'Avans'}
  ],
  transfer: [
    {value:'vesait_negdl', label:'Vəsaitin nəğdlaşdırılması'},
    {value:'avans', label:'Avans'}
  ]
};

// Subtypes where note is required
var NOTE_REQUIRED = ['diger','mobil'];

// Insurance subtypes that show DQN field
var DQN_TYPES = ['icbari','kasko'];

// Account options HTML
var CARD_OPTIONS = <?php
  $opts = '<option value="">Seçin</option>';
  $sqlFA2 = mysqli_query($db, "SELECT title FROM finaccounts WHERE id != 1 AND status = 1 AND deletedby = 0");
  while ($r2 = mysqli_fetch_array($sqlFA2)) {
    $opts .= '<option value="'.htmlspecialchars($r2['title']).'">'.htmlspecialchars($r2['title']).'</option>';
  }
  echo json_encode($opts);
?>;

var CARD_AND_OFFICE = <?php
  $opts2 = '<option value="">Seçin</option>';
  $sqlFA3 = mysqli_query($db, "SELECT title FROM finaccounts WHERE id != 1 AND status = 1 AND deletedby = 0");
  while ($r3 = mysqli_fetch_array($sqlFA3)) {
    $opts2 .= '<option value="'.htmlspecialchars($r3['title']).'">'.htmlspecialchars($r3['title']).'</option>';
  }
  echo json_encode($opts2);
?>;

// ======= DYNAMIC FORM LOGIC =======
$('#p2_category').on('change', function(){
  var cat = $(this).val();
  var $sub = $('#p2_subcategory');
  $sub.html('<option value="">Seçin</option>');
  if (cat && SUBCATS[cat]) {
    SUBCATS[cat].forEach(function(s){ $sub.append('<option value="'+s.value+'">'+s.label+'</option>'); });
    $sub.prop('disabled', false);
  } else {
    $sub.prop('disabled', true);
  }
  $sub.trigger('change');
});

$('#p2_subcategory').on('change', function(){
  var sub = $(this).val();
  var cat = $('#p2_category').val();
  
  // Reset
  $('#row_subtype, #row_payer, #row_insurance_fields, #row_dqn').hide();
  $('#p2_payer').val('');
  $('#note_required_star').hide();
  $('#p2_note').prop('required', false);
  
  // Subtypes
  if (sub && SUBTYPES[sub]) {
    $('#row_subtype').show();
    var $st = $('#p2_subtype');
    $st.html('<option value="">Seçin</option>');
    SUBTYPES[sub].forEach(function(t){ $st.append('<option value="'+t.value+'">'+t.label+'</option>'); });
    
    if (sub === 'sigorta') {
      $('#lbl_subtype').text('Sığorta növü');
      $('#row_payer').show();
      $('#row_insurance_fields').show();
    } else if (sub === 'xerc') {
      $('#lbl_subtype').text('Xərc növü');
    } else {
      $('#lbl_subtype').text('Növ');
    }
  } else {
    $('#row_subtype').hide();
  }
  
  // Effect badge
  updateEffect();
  
  // From/To fields
  updateAccountFields();
});

$('#p2_payer').on('change', function(){
  updateEffect();
  updateAccountFields();
});

$('#p2_subtype').on('change', function(){
  var st = $(this).val();
  var sub = $('#p2_subcategory').val();
  
  // DQN only for icbari & kasko
  if (sub === 'sigorta' && DQN_TYPES.indexOf(st) >= 0) {
    $('#row_dqn').show();
  } else {
    $('#row_dqn').hide();
    $('#p2_car_id').val('');
  }
  
  // Note required for diger, mobil
  if (NOTE_REQUIRED.indexOf(st) >= 0) {
    $('#note_required_star').show();
    $('#p2_note').prop('required', true);
  } else {
    $('#note_required_star').hide();
    $('#p2_note').prop('required', false);
  }
});

function updateEffect(){
  var sub = $('#p2_subcategory').val();
  var payer = $('#p2_payer').val();
  var $badge = $('#p2_effect_badge');
  var effect = 0;
  
  if (sub === 'sigorta') {
    if (payer === 'sigortali_odedi') { effect = -1; $badge.html('<span class="badge bg-danger fs-6">Xərc (-)</span>'); }
    else if (payer === 'musteri_ozu') { effect = 0; $badge.html('<span class="badge bg-secondary fs-6">Neytral (0)</span>'); }
    else { $badge.html('<span class="badge bg-warning text-dark">Kim ödəyir seçin</span>'); }
  } else if (sub === 'xerc') {
    effect = -1; $badge.html('<span class="badge bg-danger fs-6">Xərc (-)</span>');
  } else if (sub === 'medaxil') {
    effect = 1; $badge.html('<span class="badge bg-success fs-6">Gəlir (+)</span>');
  } else if (sub === 'mexaric') {
    effect = -1; $badge.html('<span class="badge bg-danger fs-6">Xərc (-)</span>');
  } else if (sub === 'transfer') {
    effect = -1; $badge.html('<span class="badge bg-info fs-6">Transfer (±)</span>');
  } else {
    $badge.html('<span class="badge bg-secondary">Kateqoriya seçin</span>');
  }
  
  $('#p2_effect').val(effect);
}

function updateAccountFields(){
  var sub = $('#p2_subcategory').val();
  var payer = $('#p2_payer').val();
  
  var $from = $('#p2_from');
  var $to = $('#p2_to');
  
  // Reset to defaults
  $from.closest('.col-md-6').show();
  $to.closest('.col-md-6').show();
  
  if (sub === 'sigorta') {
    if (payer === 'sigortali_odedi') {
      // From: card, To: FIN/VOEN (input)
      replaceWithSelect('p2_from', CARD_OPTIONS);
      replaceWithInput('p2_to', 'FİN / VOEN');
    } else if (payer === 'musteri_ozu') {
      // From: FIN/VOEN, no To
      replaceWithInput('p2_from', 'FİN / VOEN');
      $to.closest('.col-md-6').hide();
    }
  } else if (sub === 'xerc') {
    // From: card/office, no To
    replaceWithSelect('p2_from', CARD_AND_OFFICE);
    $to.closest('.col-md-6').hide();
  } else if (sub === 'medaxil') {
    // From: input (FIN/office), To: card
    replaceWithInput('p2_from', 'Hardan');
    replaceWithSelect('p2_to', CARD_OPTIONS);
  } else if (sub === 'mexaric') {
    // From: card/office, no To
    replaceWithSelect('p2_from', CARD_AND_OFFICE);
    $to.closest('.col-md-6').hide();
  } else if (sub === 'transfer') {
    // From: card, To: card
    replaceWithSelect('p2_from', CARD_OPTIONS);
    replaceWithSelect('p2_to', CARD_OPTIONS);
  }
}

function replaceWithSelect(id, optionsHtml){
  var $el = $('#'+id);
  if ($el.is('select')) { $el.html(optionsHtml); return; }
  var name = $el.attr('name');
  var $new = $('<select id="'+id+'" name="'+name+'" class="form-select">'+optionsHtml+'</select>');
  $el.replaceWith($new);
}

function replaceWithInput(id, placeholder){
  var $el = $('#'+id);
  if ($el.is('input')) { $el.attr('placeholder', placeholder).val(''); return; }
  var name = $el.attr('name');
  var $new = $('<input type="text" id="'+id+'" name="'+name+'" class="form-control" placeholder="'+placeholder+'">');
  $el.replaceWith($new);
}

// ======= IDENTIFICATION AUTOCOMPLETE =======
var identTimer2 = null;
$(document).on('input', '#p2_identification', function(){
  var val = $(this).val().trim();
  clearTimeout(identTimer2);
  if (val.length < 2) { $('#p2_identDropdown').hide().empty(); return; }
  identTimer2 = setTimeout(function(){
    $.post('/call/index.php?action=data&type=2', {q: val}, function(data){
      var dd = $('#p2_identDropdown').empty();
      if (data.length === 0) {
        dd.append('<div style="padding:10px 14px;color:#999;font-size:13px;">Tapılmadı</div>');
      } else {
        $.each(data, function(i, item){
          dd.append('<div class="p2-ident-opt" style="padding:8px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13px;" data-ident="'+item.identification+'" data-car="'+item.car_id+'"><strong>'+item.identification+'</strong> <span style="color:#6c757d;margin-left:8px;">'+item.car_id+'</span> <span style="color:#999;font-size:12px;margin-left:4px;">'+item.name+'</span></div>');
        });
      }
      dd.show();
    }, 'json');
  }, 300);
});

$(document).on('click', '.p2-ident-opt', function(){
  $('#p2_identification').val($(this).data('ident'));
  $('#p2_car_id').val($(this).data('car'));
  $('#p2_identDropdown').hide().empty();
});

$(document).on('click', function(e){
  if (!$(e.target).closest('#p2_identification, #p2_identDropdown').length) $('#p2_identDropdown').hide();
});

// ======= SUBMIT =======
$('#formPayment2').on('submit', function(e){
  e.preventDefault();
  var $btn = $('#btnSavePayment2');
  
  $.ajax({
    url: '/call/index.php?action=add&type=20',
    method: 'POST',
    data: new FormData(this),
    contentType: false,
    processData: false,
    cache: false,
    beforeSend: function(){
      $btn.prop('disabled', true).html('<span class="spinner-grow spinner-grow-sm"></span>');
    },
    success: function(resp){
      $btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Yadda saxla');
      if (resp == '1') {
        $('#formPayment2')[0].reset();
        $('#p2_subcategory').prop('disabled', true).html('<option value="">Əvvəlcə kateqoriya seçin</option>');
        $('#row_subtype, #row_payer, #row_insurance_fields, #row_dqn').hide();
        $('#p2_effect_badge').html('<span class="badge bg-secondary">Kateqoriya seçin</span>');
        $('#modalAddPayment2').modal('hide');
        loadPayments2();
        alert('Ödəniş uğurla əlavə edildi!');
      } else {
        alert('Xəta: ' + resp);
      }
    },
    error: function(){ $btn.prop('disabled', false).html('<i class="fa fa-save me-1"></i> Yadda saxla'); alert('Server xətası!'); }
  });
});

// ======= LOAD TABLE =======
function loadPayments2(page){
  page = page || 1;
  $('#paymentsTableWrap').css('opacity', 0.5);
  $.post('/call/index.php?action=fetch&type=20', {
    page: page,
    limit: $('#f_limit').val(),
    category: $('#f_category').val(),
    subcategory: $('#f_subcategory').val(),
    date_from: $('#f_date_from').val(),
    date_to: $('#f_date_to').val()
  }, function(data){
    $('#paymentsTableWrap').html(data).css('opacity', 1);
    
    // Update summary
    $.post('/call/index.php?action=data&type=20', {
      date_from: $('#f_date_from').val(),
      date_to: $('#f_date_to').val()
    }, function(s){
      var d = JSON.parse(s);
      $('#totalIncome').text(parseFloat(d.income).toFixed(2) + ' ₼');
      $('#totalExpense').text(parseFloat(d.expense).toFixed(2) + ' ₼');
      $('#totalBalance').text(parseFloat(d.balance).toFixed(2) + ' ₼');
    });
  });
}

$('#btnFilter').on('click', function(){ loadPayments2(1); });
$('#btnReset').on('click', function(){
  $('#f_date_from, #f_date_to').val('');
  $('#f_category, #f_subcategory').val('');
  loadPayments2(1);
});
$(document).on('click', '#paymentsTableWrap .page-link', function(){
  var p = $(this).data('page_number');
  if (p) loadPayments2(p);
});
// ======= DQN AUTOCOMPLETE =======
var dqnTimer = null;
$(document).on('input', '#p2_car_id', function(){
  var val = $(this).val().trim();
  clearTimeout(dqnTimer);
  var $wrap = $(this).closest('.col-md-6');
  var dd = $wrap.find('.ac-dropdown');
  if (!dd.length) {
    dd = $('<div class="ac-dropdown" style="display:none;position:absolute;left:0;right:0;z-index:1050;background:#fff;border:1px solid #ddd;border-radius:0 0 8px 8px;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,0.1);"></div>');
    $wrap.css('position','relative').append(dd);
  }
  if (val.length < 2) { dd.hide().empty(); return; }
  dqnTimer = setTimeout(function(){
    $.post('/call/index.php?action=data&type=21', {q: val}, function(data){
      dd.empty();
      if (!data.length) { dd.append('<div style="padding:10px 14px;color:#999;font-size:13px;">Tapılmadı</div>'); }
      else {
        $.each(data, function(i, item){
          dd.append('<div class="ac-opt" style="padding:8px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13px;" data-val="'+item.car_id+'"><strong>'+item.car_id+'</strong> <span style="color:#999;margin-left:8px;font-size:12px;">'+item.name+'</span></div>');
        });
      }
      dd.show();
    }, 'json');
  }, 300);
});

// ======= FIN/VOEN AUTOCOMPLETE =======
var finTimer = null;
$(document).on('input', '#p2_to, #p2_from', function(){
  if (!$(this).is('input')) return; // only for text inputs, not selects
  var val = $(this).val().trim();
  var $el = $(this);
  clearTimeout(finTimer);
  var $wrap = $el.closest('.col-md-6');
  var dd = $wrap.find('.ac-dropdown');
  if (!dd.length) {
    dd = $('<div class="ac-dropdown" style="display:none;position:absolute;left:0;right:0;z-index:1050;background:#fff;border:1px solid #ddd;border-radius:0 0 8px 8px;max-height:200px;overflow-y:auto;box-shadow:0 4px 12px rgba(0,0,0,0.1);"></div>');
    $wrap.css('position','relative').append(dd);
  }
  if (val.length < 2) { dd.hide().empty(); return; }
  finTimer = setTimeout(function(){
    $.post('/call/index.php?action=data&type=22', {q: val}, function(data){
      dd.empty();
      if (!data.length) { dd.append('<div style="padding:10px 14px;color:#999;font-size:13px;">Tapılmadı</div>'); }
      else {
        $.each(data, function(i, item){
          dd.append('<div class="ac-opt" style="padding:8px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13px;" data-val="'+item.pin+'"><strong>'+item.pin+'</strong> <span style="color:#6c757d;margin-left:8px;">'+item.car_id+'</span> <span style="color:#999;font-size:12px;margin-left:4px;">'+item.name+'</span></div>');
        });
      }
      dd.show();
    }, 'json');
  }, 300);
});

// ======= UNIVERSAL CLICK HANDLER FOR ALL AUTOCOMPLETES =======
$(document).on('click', '.ac-opt', function(){
  var val = $(this).data('val');
  $(this).closest('.col-md-6').find('input').val(val);
  $(this).closest('.ac-dropdown').hide().empty();
});

$(document).on('click', function(e){
  if (!$(e.target).closest('.ac-dropdown, #p2_car_id, #p2_to, #p2_from').length) {
    $('.ac-dropdown').hide();
  }
});

$(document).on('mouseenter', '.ac-opt', function(){ $(this).css('background','#f8f9fc'); });
$(document).on('mouseleave', '.ac-opt', function(){ $(this).css('background','#fff'); });
loadPayments2(1);
</script>