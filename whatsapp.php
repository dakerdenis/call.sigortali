<?php include('inc/header.php'); ?>

<?php
// Только админ и менеджер
if ($userGroup != 1 && $userGroup != 2) {
    echo '<div class="alert alert-danger m-4">İcazə yoxdur.</div>';
    include('inc/footer.php');
    exit;
}

$jsonFile = __DIR__ . '/data/whatsapp_templates.json';

// Сохранение
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_templates'])) {
    $templates = [];
    if (!empty($_POST['tpl_id'])) {
        foreach ($_POST['tpl_id'] as $i => $id) {
            $templates[] = [
                'id' => (int)$id,
                'title' => trim($_POST['tpl_title'][$i] ?? ''),
                'message' => trim($_POST['tpl_message'][$i] ?? ''),
            ];
        }
    }
    if (!is_dir(__DIR__ . '/data')) mkdir(__DIR__ . '/data', 0755, true);
    file_put_contents($jsonFile, json_encode($templates, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    $saved = true;
}

// Загрузка
$templates = [];
if (file_exists($jsonFile)) {
    $templates = json_decode(file_get_contents($jsonFile), true) ?: [];
}
?>

<style>
    .tpl-card { background:#fff; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,0.06); padding:20px; margin-bottom:16px; }
    .tpl-card .tpl-header { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .tpl-card .tpl-num { width:36px; height:36px; border-radius:50%; background:#25D366; color:#fff; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px; flex-shrink:0; }
    .tpl-card textarea { min-height:120px; }
    .tpl-vars { font-size:12px; color:#6c757d; margin-top:6px; }
    .tpl-vars code { background:#f0f0f0; padding:2px 6px; border-radius:4px; }
</style>

<div class="projects-section">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold mb-0"><i class="fa fa-whatsapp" style="color:#25D366;"></i> WhatsApp Şablonları</h4>
    </div>

    <?php if (!empty($saved)): ?>
        <div class="alert alert-success"><i class="fa fa-check"></i> Şablonlar uğurla yadda saxlanıldı!</div>
    <?php endif; ?>

    <div class="alert alert-info" style="font-size:13px;">
        <i class="fa fa-info-circle"></i>
        Dəyişənlər: <code>{car_id}</code> — DQN nömrəsi, <code>{phone}</code> — telefon nömrəsi.
        Bu dəyişənlər göndərilərkən avtomatik əvəz olunacaq.
    </div>

    <form method="POST">
        <input type="hidden" name="save_templates" value="1">

        <div id="templates-list">
            <?php foreach ($templates as $i => $tpl): ?>
                <div class="tpl-card" data-index="<?= $i ?>">
                    <div class="tpl-header">
                        <div class="tpl-num"><?= $i + 1 ?></div>
                        <input type="hidden" name="tpl_id[]" value="<?= (int)$tpl['id'] ?>">
                        <input type="text" name="tpl_title[]" class="form-control" placeholder="Şablon adı" value="<?= htmlspecialchars($tpl['title']) ?>">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-tpl" title="Sil"><i class="fa fa-trash"></i></button>
                    </div>
                    <textarea name="tpl_message[]" class="form-control" placeholder="Mesaj mətni..."><?= htmlspecialchars($tpl['message']) ?></textarea>
                    <div class="tpl-vars">Dəyişənlər: <code>{car_id}</code> <code>{phone}</code></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="d-flex gap-2 mt-3">
            <button type="button" id="add-tpl" class="btn btn-outline-success">
                <i class="fa fa-plus"></i> Yeni şablon əlavə et
            </button>
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-floppy-o"></i> Yadda saxla
            </button>
        </div>
    </form>

</div>

</body></html>
<?php include('inc/footer.php'); ?>

<script>
var tplIndex = <?= count($templates) ?>;

$('#add-tpl').on('click', function() {
    tplIndex++;
    var html = `
    <div class="tpl-card">
        <div class="tpl-header">
            <div class="tpl-num">${tplIndex}</div>
            <input type="hidden" name="tpl_id[]" value="${tplIndex}">
            <input type="text" name="tpl_title[]" class="form-control" placeholder="Şablon adı">
            <button type="button" class="btn btn-outline-danger btn-sm remove-tpl" title="Sil"><i class="fa fa-trash"></i></button>
        </div>
        <textarea name="tpl_message[]" class="form-control" placeholder="Mesaj mətni..." style="min-height:120px;"></textarea>
        <div class="tpl-vars">Dəyişənlər: <code>{car_id}</code> <code>{phone}</code></div>
    </div>`;
    $('#templates-list').append(html);
});

$(document).on('click', '.remove-tpl', function() {
    if (confirm('Bu şablonu silmək istəyirsiniz?')) {
        $(this).closest('.tpl-card').remove();
        // Renumber
        $('#templates-list .tpl-num').each(function(i) { $(this).text(i + 1); });
    }
});
</script>