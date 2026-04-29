<?php include('inc/header.php'); ?>

<?php
    $customerId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($customerId <= 0) {
        echo '<div class="alert alert-danger m-4">Müştəri ID göstərilməyib.</div>';
        include('inc/footer.php'); exit;
    }

    // 1) Найти "якорную" запись по id, чтобы вытащить PIN
    $anchor = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM customers WHERE id = $customerId"));
    if (empty($anchor)) {
        echo '<div class="alert alert-warning m-4">Müştəri tapılmadı (ID: ' . $customerId . ')</div>';
        include('inc/footer.php'); exit;
    }

    $pin = trim($anchor['pin']);
    $pinEsc = mysqli_real_escape_string($db, $pin);

    // 2) Все полисы этого клиента (по PIN). Если PIN пустой — показываем только эту запись
    if ($pin !== '') {
        $allPolicies = [];
        $sql = mysqli_query($db, "SELECT * FROM customers WHERE pin = '$pinEsc' ORDER BY end_date DESC, id DESC");
        while ($r = mysqli_fetch_array($sql)) { $allPolicies[] = $r; }
    } else {
        $allPolicies = [$anchor];
    }

    // 3) Уникальные машины (по car_id)
    $cars = [];
    foreach ($allPolicies as $p) {
        $cid = trim($p['car_id']);
        if ($cid === '') continue;
        if (!isset($cars[$cid])) {
            $cars[$cid] = [
                'car_id' => $cid, 'car_pin' => $p['car_pin'], 'make' => $p['make'],
                'model' => $p['model'], 'caryear' => $p['caryear'], 'type' => $p['type'],
                'engineType' => $p['engineType'], 'last_end_date' => $p['end_date'],
            ];
        }
    }

    // 4) Берём "лучший" профиль — самый свежий полис с непустым именем/телефоном
    $profile = $anchor;
    foreach ($allPolicies as $p) {
        if (!empty($p['name']) && !empty($p['phone'])) { $profile = $p; break; }
    }

    // 5) Helper: безопасный формат даты
    function safeDate($d, $fmt = "d.m.Y") {
        if (empty($d) || $d === '0000-00-00' || $d === '0000-00-00 00:00:00') return '—';
        $ts = strtotime($d);
        if ($ts === false || $ts <= 0) return '—';
        return date($fmt, $ts);
    }

    // 6) Активный полис — самый поздний end_date в будущем
    $hasActive = false;
    $latestEnd = null;
    foreach ($allPolicies as $p) {
        if (empty($p['end_date']) || $p['end_date'] === '0000-00-00') continue;
        $ts = strtotime($p['end_date']);
        if ($ts === false || $ts <= 0) continue;
        if ($latestEnd === null || $ts > $latestEnd) $latestEnd = $ts;
        if ($ts >= strtotime(date('Y-m-d'))) $hasActive = true;
    }

    // Аватар
    $nameParts = explode(' ', trim($profile['name']));
    $initials = strtoupper(mb_substr($nameParts[0] ?? '?', 0, 1) . mb_substr($nameParts[1] ?? '', 0, 1));
    $avatarColors = ['#4e73df','#1cc88a','#36b9cc','#f6c23e','#e74a3b','#858796','#6f42c1','#fd7e14'];
    $avatarBg = $avatarColors[$customerId % count($avatarColors)];

    // Создатель из якорной записи
    $getCreator = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM users WHERE id = " . (int)$anchor['createdby']));
?>

<style>
    .profile-card {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: #fff; border-radius: 12px; padding: 26px;
        margin-bottom: 18px; box-shadow: 0 4px 12px rgba(78,115,223,0.25);
    }
    .profile-avatar {
        width: 88px; height: 88px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 32px; font-weight: 700; color: #fff;
        border: 4px solid rgba(255,255,255,0.3); flex-shrink: 0;
    }
    .profile-name { font-size: 22px; font-weight: 700; margin-bottom: 4px; }
    .profile-meta { font-size: 13px; opacity: 0.9; }
    .profile-meta .mi { display: inline-block; margin-right: 14px; }
    .badge-pill { font-size: 11px; padding: 5px 10px; border-radius: 12px; font-weight: 600; }
    .badge-active  { background: #1cc88a; color: #fff; }
    .badge-expired { background: #e74a3b; color: #fff; }
    .badge-info-soft { background: rgba(255,255,255,0.2); color: #fff; }

    .action-bar { display: flex; gap: 8px; margin-bottom: 16px; flex-wrap: wrap; }

    .nav-tabs-modern {
        border: none; background: #fff; border-radius: 10px;
        padding: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 16px;
        display: inline-flex; gap: 4px;
    }
    .nav-tabs-modern .nav-link {
        border: none; color: #6c757d; font-weight: 600;
        font-size: 13px; padding: 8px 16px; border-radius: 7px;
    }
    .nav-tabs-modern .nav-link.active {
        background: #4e73df; color: #fff;
    }
    .nav-tabs-modern .nav-link .count {
        background: rgba(0,0,0,0.08); padding: 1px 7px; border-radius: 10px;
        font-size: 11px; margin-left: 5px;
    }
    .nav-tabs-modern .nav-link.active .count { background: rgba(255,255,255,0.25); }

    .info-card {
        background: #fff; border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06); padding: 20px;
        margin-bottom: 16px;
    }
    .info-card h6 {
        color: #4e73df; font-weight: 700; text-transform: uppercase;
        font-size: 11px; letter-spacing: 0.5px;
        margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid #eee;
    }
    .info-row {
        display: flex; justify-content: space-between;
        padding: 8px 0; border-bottom: 1px dashed #f0f0f0; font-size: 14px;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .label { color: #6c757d; font-weight: 500; }
    .info-row .value { color: #212529; font-weight: 600; text-align: right; }

    .car-tile, .policy-tile {
        background: #fff; border-radius: 10px; padding: 16px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05); margin-bottom: 12px;
        border-left: 4px solid #4e73df;
    }
    .car-tile.expired, .policy-tile.expired { border-left-color: #e74a3b; }
    .car-title { font-size: 18px; font-weight: 700; color: #212529; }
    .car-meta { font-size: 13px; color: #6c757d; margin-top: 4px; }

    .timeline-item {
        padding: 12px 14px; border-left: 3px solid #4e73df;
        background: #f8f9fc; margin-bottom: 8px; border-radius: 4px;
    }
    .timeline-item.empty { border-left-color: #e0e0e0; }
    .timeline-meta { font-size: 12px; color: #6c757d; margin-top: 4px; }
    .timeline-content { margin-top: 6px; font-size: 13px; color: #495057; }

    .empty-state {
        text-align: center; padding: 40px 20px; color: #adb5bd;
    }
    .empty-state i { font-size: 36px; margin-bottom: 10px; display: block; }
    .status-item { padding: 12px 16px; border-radius: 8px; margin-bottom: 10px; border-left: 4px solid #4e73df; background: #f8f9fc; }
    .status-item .status-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
    .status-item .status-user { font-weight: 700; font-size: 14px; color: #333; }
    .status-item .status-date { font-size: 12px; color: #999; }
    .status-item .status-badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; color: #fff; background: #4e73df; }
    .status-item .status-detail { font-size: 13px; color: #555; margin-top: 4px; }
    .status-item .status-detail strong { color: #333; }
    .status-item.success { border-left-color: #1cc88a; }
    .status-item.success .status-badge { background: #1cc88a; }
    .status-item.waiting { border-left-color: #f6c23e; }
    .status-item.waiting .status-badge { background: #f6c23e; color: #333; }
    .status-item.confirm { border-left-color: #36b9cc; }
    .status-item.confirm .status-badge { background: #36b9cc; }
    .status-item.paywait { border-left-color: #fd7e14; }
    .status-item.paywait .status-badge { background: #fd7e14; }
    .status-item.forward { border-left-color: #6f42c1; }
    .status-item.forward .status-badge { background: #6f42c1; }
    .status-item.payed { border-left-color: #e74a3b; }
    .status-item.payed .status-badge { background: #e74a3b; }
</style>

<div class="projects-section">

    <!-- Действия -->
    <div class="action-bar">
        <a href="/call/customers" class="btn btn-light"><i class="fa fa-arrow-left"></i> Geri qayıt</a>
        <?php if (!empty($profile['car_id'])): ?>
            <a href="/call/<?= htmlspecialchars($profile['car_id']) ?>" class="btn btn-primary"><i class="fa fa-phone"></i> Zəng et</a>
        <?php endif; ?>
        <?php if (!empty($profile['phone'])): ?>
            <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $profile['phone']) ?>" target="_blank" class="btn btn-success"><i class="fa fa-whatsapp"></i> WhatsApp</a>
        <?php endif; ?>
    </div>

    <!-- Шапка -->
    <div class="profile-card">
        <div class="d-flex align-items-center" style="gap:20px;flex-wrap:wrap;">
            <div class="profile-avatar" style="background:<?= $avatarBg ?>;"><?= $initials ?: '?' ?></div>
            <div style="flex:1;min-width:240px;">
                <div class="profile-name"><?= htmlspecialchars($profile['name']) ?: 'Naməlum müştəri' ?></div>
                <div class="profile-meta">
                    <span class="mi"><i class="fa fa-id-card"></i> PİN: <strong><?= htmlspecialchars($pin) ?: '—' ?></strong></span>
                    <?php if (!empty($profile['phone'])): ?>
                        <span class="mi"><i class="fa fa-phone"></i> <?= htmlspecialchars($profile['phone']) ?></span>
                    <?php endif; ?>
                </div>
                <div class="mt-2 d-flex" style="gap:6px;flex-wrap:wrap;">
                    <?php if ($hasActive): ?>
                        <span class="badge-pill badge-active"><i class="fa fa-check-circle"></i> Aktiv sığorta</span>
                    <?php else: ?>
                        <span class="badge-pill badge-expired"><i class="fa fa-exclamation-triangle"></i> Aktiv sığorta yoxdur</span>
                    <?php endif; ?>
                    <span class="badge-pill badge-info-soft"><i class="fa fa-car"></i> <?= count($cars) ?> avtomobil</span>
                    <span class="badge-pill badge-info-soft"><i class="fa fa-shield"></i> <?= count($allPolicies) ?> polis</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Табы -->
    <ul class="nav nav-tabs nav-tabs-modern" id="custTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-personal" type="button">
                <i class="fa fa-user"></i> Şəxsi məlumatlar
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-cars" type="button">
                <i class="fa fa-car"></i> Avtomobillər <span class="count"><?= count($cars) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-policies" type="button">
                <i class="fa fa-shield"></i> Sığortalar <span class="count"><?= count($allPolicies) ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-history" type="button">
                <i class="fa fa-history"></i> Statuslar tarixçəsi
            </button>
        </li>
    </ul>

    <div class="tab-content">

        <!-- ===== Şəxsi məlumatlar ===== -->
        <div class="tab-pane fade show active" id="tab-personal">
            <div class="row">
                <div class="col-md-6">
                    <div class="info-card">
                        <h6>Əsas məlumatlar</h6>
                        <div class="info-row"><span class="label">Ad Soyad</span><span class="value"><?= htmlspecialchars($profile['name']) ?: '—' ?></span></div>
                        <div class="info-row"><span class="label">PİN</span><span class="value"><?= htmlspecialchars($pin) ?: '—' ?></span></div>
                        <div class="info-row"><span class="label">Şəh. seriya</span><span class="value"><?= htmlspecialchars($profile['pin_serial']) ?: '—' ?></span></div>
                        <div class="info-row"><span class="label">Seriya</span><span class="value"><?= htmlspecialchars($profile['serial']) ?: '—' ?></span></div>
                        <div class="info-row"><span class="label">Telefon</span><span class="value"><?= htmlspecialchars($profile['phone']) ?: '—' ?></span></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-card">
                        <h6>Sistem məlumatları</h6>
                        <div class="info-row"><span class="label">Müştəri ID</span><span class="value">#<?= $anchor['id'] ?></span></div>
                        <div class="info-row"><span class="label">Yaradılıb</span><span class="value"><?= safeDate($anchor['created'], "d.m.Y H:i") ?></span></div>
                        <div class="info-row"><span class="label">Kim yazıb</span><span class="value"><?= htmlspecialchars(trim(($getCreator['name'] ?? '') . ' ' . ($getCreator['surname'] ?? ''))) ?: '—' ?></span></div>
                        <div class="info-row"><span class="label">Polislərin sayı</span><span class="value"><?= count($allPolicies) ?></span></div>
                        <div class="info-row"><span class="label">Avtomobil sayı</span><span class="value"><?= count($cars) ?></span></div>
                    </div>

                    <?php if (!empty($profile['note'])): ?>
                    <div class="info-card">
                        <h6>Qeyd</h6>
                        <div style="font-size:14px;color:#495057;white-space:pre-wrap;"><?= htmlspecialchars($profile['note']) ?></div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ===== Avtomobillər ===== -->
        <div class="tab-pane fade" id="tab-cars">
            <?php if (empty($cars)): ?>
                <div class="info-card empty-state">
                    <i class="fa fa-car"></i>
                    Avtomobil tapılmadı
                </div>
            <?php else: ?>
                <?php foreach ($cars as $c):
                    $expired = (safeDate($c['last_end_date']) === '—') ? false : (strtotime($c['last_end_date']) < strtotime(date('Y-m-d')));
                ?>
                    <div class="car-tile <?= $expired ? 'expired' : '' ?>">
                        <div class="d-flex justify-content-between align-items-start" style="gap:12px;flex-wrap:wrap;">
                            <div>
                                <div class="car-title">
                                    <i class="fa fa-car"></i> <?= htmlspecialchars($c['car_id']) ?>
                                </div>
                                <div class="car-meta">
                                    <?= htmlspecialchars(trim($c['make'] . ' ' . $c['model'])) ?: 'Marka göstərilməyib' ?>
                                    <?php if (!empty($c['caryear'])): ?> · <?= htmlspecialchars($c['caryear']) ?><?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <a href="/call/<?= htmlspecialchars($c['car_id']) ?>" class="btn btn-sm btn-primary"><i class="fa fa-eye"></i> Aç</a>
                            </div>
                        </div>
                        <div class="row mt-3" style="font-size:13px;">
                            <div class="col-md-3"><span class="text-muted">FİN:</span> <strong><?= htmlspecialchars($c['car_pin']) ?: '—' ?></strong></div>
                            <div class="col-md-3"><span class="text-muted">Növ:</span> <strong><?= htmlspecialchars($c['type']) ?: '—' ?></strong></div>
                            <div class="col-md-3"><span class="text-muted">Mühərrik:</span> <strong><?= htmlspecialchars($c['engineType']) ?: '—' ?></strong></div>
                            <div class="col-md-3"><span class="text-muted">Sonuncu bitmə:</span>
                                <strong style="<?= $expired ? 'color:#e74a3b;' : '' ?>"><?= safeDate($c['last_end_date']) ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- ===== Sığortalar ===== -->
        <div class="tab-pane fade" id="tab-policies">
            <?php if (empty($allPolicies)): ?>
                <div class="info-card empty-state">
                    <i class="fa fa-shield"></i>
                    Sığorta tapılmadı
                </div>
            <?php else: ?>
                <?php foreach ($allPolicies as $p):
                    $endStr = safeDate($p['end_date']);
                    $expired = ($endStr !== '—') && (strtotime($p['end_date']) < strtotime(date('Y-m-d')));
                    $companyRow = mysqli_fetch_array(mysqli_query($db, "SELECT * FROM companies WHERE id = " . (int)$p['company']));
                ?>
                    <div class="policy-tile <?= $expired ? 'expired' : '' ?>">
                        <div class="d-flex justify-content-between align-items-start" style="gap:12px;flex-wrap:wrap;">
                            <div>
                                <div class="car-title">
                                    <i class="fa fa-shield"></i> <?= htmlspecialchars($companyRow['title'] ?? 'Şirkət göstərilməyib') ?>
                                </div>
                                <div class="car-meta">
                                    Şəhadətnamə: <strong><?= htmlspecialchars($p['identification']) ?: '—' ?></strong>
                                    <?php if (!empty($p['car_id'])): ?> · DQN: <strong><?= htmlspecialchars($p['car_id']) ?></strong><?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <?php if ($expired): ?>
                                    <span class="badge-pill badge-expired">Bitib</span>
                                <?php elseif ($endStr !== '—'): ?>
                                    <span class="badge-pill badge-active">Aktiv</span>
                                <?php else: ?>
                                    <span class="badge-pill" style="background:#e0e0e0;color:#555;">Tarix yoxdur</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row mt-3" style="font-size:13px;">
                            <div class="col-md-3"><span class="text-muted">Sığorta haqqı:</span> <strong><?= htmlspecialchars($p['premium']) ?: '—' ?> ₼</strong></div>
                            <div class="col-md-3"><span class="text-muted">İSB qiyməti:</span> <strong><?= htmlspecialchars($p['valuesp']) ?: '—' ?> ₼</strong></div>
                            <div class="col-md-3"><span class="text-muted">B/M əmsalı:</span> <strong><?= htmlspecialchars($p['bm']) ?: '—' ?></strong></div>
                            <div class="col-md-3"><span class="text-muted">Bitmə tarixi:</span>
                                <strong style="<?= $expired ? 'color:#e74a3b;' : '' ?>"><?= $endStr ?></strong>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- ===== Statuslar tarixçəsi ===== -->
        <div class="tab-pane fade" id="tab-history">
            <div class="info-card">
                <?php
                    // Собираем все car_id этого клиента
                    $carIds = array_keys($cars);
                    if (empty($carIds)) {
                        echo '<div class="empty-state"><i class="fa fa-history"></i>Status tapılmadı</div>';
                    } else {
                        $carInList = "'" . implode("','", array_map(function($c) use($db){ return mysqli_real_escape_string($db, $c); }, $carIds)) . "'";




$sqlHist = mysqli_query($db,
    "SELECT cs.*, u.name AS uname, u.surname AS usurname, p.title AS ptitle, p.code AS pcode, c.title AS ctitle
     FROM call_status cs
     LEFT JOIN users u ON u.id = cs.createdby
     LEFT JOIN paramitems p ON p.id = cs.type
     LEFT JOIN companies c ON c.id = cs.companyId
     WHERE cs.car_id IN ($carInList) AND cs.deletedby = 0
     ORDER BY cs.id DESC LIMIT 50");
$hasHist = false;
while ($h = mysqli_fetch_array($sqlHist)) {
    $hasHist = true;
    $pcode = $h['pcode'] ?? '';
    echo '<div class="status-item ' . htmlspecialchars($pcode) . '">';
    echo '<div class="status-header">';
    echo '<span class="status-user">' . htmlspecialchars(trim(($h['uname'] ?? '') . ' ' . ($h['usurname'] ?? ''))) . '</span>';
    echo '<span class="status-date">' . safeDate($h['created'], "d.m.Y H:i") . '</span>';
    echo '</div>';
    echo '<span class="status-badge">' . htmlspecialchars($h['ptitle'] ?: '—') . '</span>';
    if (!empty($h['car_id'])) echo '<div class="status-detail"><strong>DQN:</strong> ' . htmlspecialchars($h['car_id']) . '</div>';
    if (!empty($h['next_date']) && $h['next_date'] != '0000-00-00 00:00:00') echo '<div class="status-detail"><strong>Xatırlatma:</strong> ' . safeDate($h['next_date'], "d.m.Y H:i") . '</div>';
    if (!empty($h['content'])) echo '<div class="status-detail"><strong>Qeyd:</strong> ' . nl2br(htmlspecialchars($h['content'])) . '</div>';
    if (!empty($h['ctitle'])) echo '<div class="status-detail"><strong>Şirkət:</strong> ' . htmlspecialchars($h['ctitle']) . '</div>';
    if (!empty($h['price']) && $h['price'] != '0.00') echo '<div class="status-detail"><strong>Qiymət:</strong> ' . $h['price'] . '</div>';
    if (!empty($h['agreePrice']) && $h['agreePrice'] != '0.00') echo '<div class="status-detail"><strong>Razılaşdığı:</strong> ' . $h['agreePrice'] . '</div>';
    if (!empty($h['paycode'])) echo '<div class="status-detail"><strong>Ödəniş kodu:</strong> ' . htmlspecialchars($h['paycode']) . '</div>';
    echo '</div>';
}



                        if (!$hasHist) {
                            echo '<div class="empty-state"><i class="fa fa-history"></i>Status tapılmadı</div>';
                        }
                    }
                ?>
            </div>
        </div>

    </div>
</div>

</body>
</html>

<?php include('inc/footer.php'); ?>