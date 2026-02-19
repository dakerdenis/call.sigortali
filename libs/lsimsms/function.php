<?

if($setting_lsimsms['setting_value'] == 1 && $orderType == 2){

    function sendsms($phone, $SMSmsg, $username, $password, $sender){

        $symbols = ["+", "(", ")", "-"];
        $empty   = ["", "", "", ""];

        $ts = array("/ş/", "/ə/", "/ö/", "/ü/", "/ğ/", "/ı/", "/ç/", "/Ş/", "/Ə/", "/Ö/", "/Ü/", "/Ğ/", "/I/", "/Ç/");
        $tn = array("s", "e", "o", "u", "g", "i", "c","S", "E", "O", "U", "G", "I", "C");
        $SMSmsg = preg_replace($ts, $tn, $SMSmsg);

        $phone = str_replace($symbols, $empty, $phone);
        $password = md5($password);
        $md5Key = md5($password.$username.$SMSmsg.$phone.$sender);
        $SMSmsg = urlencode($SMSmsg);

        $url = 'http://apps.lsim.az/quicksms/v1/send?login='.$username.'&msisdn='.$phone.'&text='.$SMSmsg.'&sender='.$sender.'&key='.$md5Key.'&unicode=true';
        $options = array(
            'http' => array(
                'header'  => "application/json;charset=utf-8;",
                'method'  => 'GET'
            )
        );
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        if ($result === FALSE) { /* log errors */ }

        $itm = json_decode($result, true);
        $sendId = $itm["obj"];

        //var_dump($result);

    }

}

?>