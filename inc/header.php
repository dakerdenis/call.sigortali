<?php if(empty($_GET['customer'])){ $_GET['customer'] = ''; } ?>

<html>

<head>

    <title>CallCenter Studio</title>
    <link rel="icon" type="image/x-icon" href="/assets/favicon.png" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="/assets/invoice.css">
    <script src="/assets/script.js"></script>

    <script src="https://use.fontawesome.com/9f3f4f1125.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/@fullcalendar/core@6.1.9/locales-all.global.min.js'></script> 

    <script src="/assets/jquery.table2excel.js" type="text/javascript"></script>

    <link href="/libs/noty/lib/noty.css" rel="stylesheet">
    <link href="/libs/noty/lib/themes/nest.css" rel="stylesheet">
    <script src="/libs/noty/lib/noty.js"></script>

    <link rel="stylesheet" href="/assets/custom.css">

</head>

<body>
  

  <div class="app-container">

    <div class="app-header">
      <div class="app-header-left">
        <span class="app-icon"></span>
        <p class="app-name"><a href="/"><img src="/assets/logo.svg" alt="" width="150px"></a></p>
        <form id="search">
          <div class="search-wrapper">
            <input class="search-input" type="text" placeholder="<?= lang('Axtar'); ?>" value="<?= $_GET['customer']; ?>">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-search" viewBox="0 0 24 24">
              <defs></defs>
              <circle cx="11" cy="11" r="8"></circle>
              <path d="M21 21l-4.35-4.35"></path>
            </svg>
          </div>
        </form>
      </div>
      <div class="app-header-right">
        <button class="mode-switch" title="<?= lang('Temanı dəyiş'); ?>">
          <svg class="moon" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" width="24" height="24" viewBox="0 0 24 24">
            <defs></defs>
            <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"></path>
          </svg>
        </button>
        <button class="add-btn" title="<?= lang('Yeni müştəri əlavə et'); ?>" data-bs-toggle="modal" data-bs-target=".addCustomer">
          <svg class="btn-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="feather feather-plus">
            <line x1="12" y1="5" x2="12" y2="19" />
            <line x1="5" y1="12" x2="19" y2="12" /></svg>
        </button>
        <button class="notification-btn" data-bs-toggle="modal" data-bs-target=".modalAddPayment">
          <i class="fa fa-money" aria-hidden="true"></i>
        </button>
        <button class="profile-btn">
          <img src="/assets/profile.png" />
          <span><?= $user_data['name']; ?> <?= $user_data['surname']; ?></span>
        </button>
      </div>
      <button class="messages-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-message-circle">
          <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" /></svg>
      </button>
    </div>

    <div class="app-content">

      <div class="app-sidebar">
        <a href="/" class="app-sidebar-link <? if($_GET['action'] == ''){ echo 'active'; } ?>">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-home">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
            <polyline points="9 22 9 12 15 12 15 22" /></svg>
        </a>
        <a href="/current" class="app-sidebar-link <? if($_GET['action'] == 'current'){ echo 'active'; } ?>">
          <i class="fa fa-handshake-o" aria-hidden="true"></i>
        </a>
        <a href="/customers" class="app-sidebar-link <? if($_GET['action'] == 'customers'){ echo 'active'; } ?>">
          <i class="fa fa-user-o" aria-hidden="true"></i>
        </a>
        <a href="/companies" class="app-sidebar-link <? if($_GET['action'] == 'companies'){ echo 'active'; } ?>">
          <i class="fa fa-building" aria-hidden="true"></i>
        </a>
        <a href="/statuses" class="app-sidebar-link <? if($_GET['action'] == 'statuses'){ echo 'active'; } ?>">
          <i class="fa fa-check" aria-hidden="true"></i>
        </a>
        <a href="/accounting" class="app-sidebar-link <? if($_GET['action'] == 'accounting'){ echo 'active'; } ?>">
          <svg class="link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-pie-chart" viewBox="0 0 24 24">
            <defs />
            <path d="M21.21 15.89A10 10 0 118 2.83M22 12A10 10 0 0012 2v10z" />
          </svg>
        </a>
        <? if($userGroup == 1 || $userGroup == 2 || $user_id == 14){ ?>
        <a href="/finance" class="app-sidebar-link <? if($_GET['action'] == 'finance'){ echo 'active'; } ?>">
          <i class="fa fa-money" aria-hidden="true"></i>
        </a>
                <a href="/finance2" class="app-sidebar-link <? if($_GET['action'] == 'finance2'){ echo 'active'; } ?>">
          <i class="fa fa-money" aria-hidden="true"></i>
        </a>
        <? } ?>
        <? if($userGroup == 1 || $userGroup == 2){ ?>
        <a href="/settings" class="app-sidebar-link <? if($_GET['action'] == 'settings'){ echo 'active'; } ?>">
          <svg class="link-icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="feather feather-settings" viewBox="0 0 24 24">
            <defs />
            <circle cx="12" cy="12" r="3" />
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z" />
          </svg>
        </a>
        <? } ?>
        <a href="/inc/logout.php" class="app-sidebar-link">
          <i class="fa fa-sign-out" aria-hidden="true"></i>
        </a>
      </div>
