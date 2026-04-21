<?php
session_start();
//if (substr_count($_SERVER[‘HTTP_ACCEPT_ENCODING’], ‘gzip’)) ob_start(“ob_gzhandler”); else ob_start();

header('Content-Type: text/html; charset=utf-8');

include_once("inc/config.php");
//include_once("libs/gclouds/requests.php");

setlocale(LC_MONETARY, 'tr_TR.UTF-8');

//ob_end_flush();

if (!empty($_COOKIE['login'])) {
  $_SESSION['login'] = $_COOKIE['login'];
}

if (!empty($_COOKIE['id'])) {
  $_SESSION['id'] = $_COOKIE['id'];
}

if (!isset($_SESSION['login']) || !isset($_SESSION['id']) || $_SESSION['id'] == 0) {

?>

  <html>

  <head>
    <link rel="stylesheet" href="/assets/login.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
  </head>

  <body>

    <body class="align">

      <div class="grid">

        <form id="login-form" class="form login">

          <center>
            <img src="/assets/logo.svg" alt="" width="200px">
          </center>

          <div class="form__field">
            <label for="login__username"><svg class="icon">
                <use xlink:href="#icon-user"></use>
              </svg><span class="hidden">İstifadəçi adı</span></label>
            <input autocomplete="username" id="login__username" type="text" name="login" class="form__input" placeholder="İstifadəçi adı" required>
          </div>

          <div class="form__field">
            <label for="login__password"><svg class="icon">
                <use xlink:href="#icon-lock"></use>
              </svg><span class="hidden">Şifrə</span></label>
            <input id="login__password" type="password" name="password" class="form__input" placeholder="Şifrə" required>
          </div>

          <div class="form__field">
            <input id="login" type="submit" value="Daxil ol">
          </div>

        </form>

      </div>

      <svg xmlns="http://www.w3.org/2000/svg" class="icons">
        <symbol id="icon-arrow-right" viewBox="0 0 1792 1792">
          <path d="M1600 960q0 54-37 91l-651 651q-39 37-91 37-51 0-90-37l-75-75q-38-38-38-91t38-91l293-293H245q-52 0-84.5-37.5T128 1024V896q0-53 32.5-90.5T245 768h704L656 474q-38-36-38-90t38-90l75-75q38-38 90-38 53 0 91 38l651 651q37 35 37 90z" />
        </symbol>
        <symbol id="icon-lock" viewBox="0 0 1792 1792">
          <path d="M640 768h512V576q0-106-75-181t-181-75-181 75-75 181v192zm832 96v576q0 40-28 68t-68 28H416q-40 0-68-28t-28-68V864q0-40 28-68t68-28h32V576q0-184 132-316t316-132 316 132 132 316v192h32q40 0 68 28t28 68z" />
        </symbol>
        <symbol id="icon-user" viewBox="0 0 1792 1792">
          <path d="M1600 1405q0 120-73 189.5t-194 69.5H459q-121 0-194-69.5T192 1405q0-53 3.5-103.5t14-109T236 1084t43-97.5 62-81 85.5-53.5T538 832q9 0 42 21.5t74.5 48 108 48T896 971t133.5-21.5 108-48 74.5-48 42-21.5q61 0 111.5 20t85.5 53.5 62 81 43 97.5 26.5 108.5 14 109 3.5 103.5zm-320-893q0 159-112.5 271.5T896 896 624.5 783.5 512 512t112.5-271.5T896 128t271.5 112.5T1280 512z" />
        </symbol>
      </svg>

    </body>

  </body>

  </html>

  <script>
    $('#login-form').on('submit', function(e) {
      e.preventDefault();
      var data = $("#login-form").serialize();
      $.ajax({
        type: 'POST',
        url: '/call/inc/login.php',
        data: data,
        beforeSend: function() {
          $("#error").fadeOut();
          $("#login").val('Yüklenilir..');
        },
        success: function(response) {
          if ($.trim(response) === "1") {
            $("#login").val('Doğru..');
            setTimeout(' window.location.href = "/call/"; ', 0);
          } else if ($.trim(response) === "2") {
            $("#seckey").prop("disabled", false);
            $('#authentnew').show();
            $('#authentold').show();
            $('#seckeys').focus();
            $("#login").val('Daxil Ol..');
          } else if ($.trim(response) === "3") {
            $('#authentnew').hide();
            $('#authentold').show();
            $('#seckeys').focus();
            $("#login").val('Daxil Ol..');
          } else {
            $("#error").fadeIn(1000, function() {
              $("#error").html(response).show();
            });
            $("#login").val('Daxil Ol..');
          }
          console.log(response);
        }
      });
      return false;

    });
  </script>

<?php
} else {

  #if(!empty($user_id)){
  if (true) {
    ob_start();

    switch ($_GET["action"]) {

      case "orderitems":
        include_once("orderitems.php");
        break;
      case "addorder":
        include_once("addorder.php");
        break;
      case "ordersconfirmed":
        include_once("ordersconfirmed.php");
        break;
      case "orders":
        include_once("orders.php");
        break;
      case "statuses":
        include_once("statuses.php");
        break;
      case "current":
        include_once("current.php");
        break;
      case "accounting":
        include_once("accounting.php");
        break;
      case "settings":
        include_once("settings.php");
        break;
      case "companies":
        include_once("companies.php");
        break;
      case "customer":
        include_once("customer.php");
        break;
      case "customers":
        include_once("customers.php");
        break;
      case "finance2":
        include_once("finance2.php");
        break;
      case "events":
        include_once("events.php");
        break;
      case "data":
        include_once("inc/data.php");
        break;
      case "add":
        include_once("inc/add.php");
        break;
      case "fetch":
        include_once("inc/fetch.php");
        break;
      case "whatsapp":
        include_once("whatsapp.php");
        break;
      default:
        include_once("home.php");
    }

    ob_end_flush();
  } else {
    die(header("location: /call/inc/logout.php"));
  }
}
?>