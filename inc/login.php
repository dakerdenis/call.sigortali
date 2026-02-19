<?php



if ( !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' ){
        if(isset($_POST['login']) && isset($_POST['password'])){
            include("config.php");
            $login = mysqli_real_escape_string($db, $_POST['login']);
            $password = mysqli_real_escape_string($db, $_POST['password']);
            $res=mysqli_query($db, "SELECT * FROM users WHERE email = '$login' AND deletedby = 0 AND status = 1 AND groupId != 0 ");
            $data=mysqli_fetch_array($res);
            if(empty($data['email'])){
                die(header('Location: /'));
            } else {
                $isPasswordCorrect = password_verify($password, $data['password']);
                if ($isPasswordCorrect != 1) {
                    die(header('Location: /'));
                } else {
                    ini_set('session.gc_maxlifetime', 18000);
                    session_set_cookie_params(18000);
                    session_start();
                    $_SESSION['login'] = $data['email'];
                    $_SESSION['id'] = $data['id'];
                    echo 1;
                }
            }
        }
} else{
    die( header("location: /"));
}
?>