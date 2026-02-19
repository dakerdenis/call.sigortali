<?php

function lang($event){

    $settingMultiLang = $GLOBALS['settingMultiLang'];

    if($settingMultiLang['setting_value'] == 1){

        $db = $GLOBALS['db'];
        $lang = $GLOBALS['lang'];

        $langQuery = "SELECT *, $lang as lang FROM lang WHERE phrase = '$event' AND deletedby = 0";

        $lang_title = mysqli_fetch_array(mysqli_query($db, $langQuery));

        if(empty($lang_title['lang'])){

            if(empty($lang_title['phrase']) && $lang == "Azərbaycanca"){
                mysqli_query($db, 'INSERT INTO lang ( phrase ) VALUES ( "'.$event.'" )');
            }

            $lang_title['lang'] = $event;
        }

        return $lang_title['lang'];

    } else{
        return $event;
    }

}

function generateSeoURL($string, $wordLimit = 0){
    $separator = '-';
    
    if($wordLimit != 0){
        $wordArr = explode(' ', $string);
        $string = implode(' ', array_slice($wordArr, 0, $wordLimit));
    }

    $quoteSeparator = preg_quote($separator, '#');

    $trans = array(
        '&.+?;'                    => '',
        '[^\w\d _-]'            => '',
        '\s+'                    => $separator,
        '('.$quoteSeparator.')+'=> $separator
    );

    $string = strip_tags($string);
    foreach ($trans as $key => $val){
        $string = preg_replace('#'.$key.'#i'.(UTF8_ENABLED ? 'u' : ''), $val, $string);
    }

    $string = strtolower($string);

    return trim(trim($string, $separator));
}

function format_uri( $string, $separator = '-' ){

    $ts = array("/ş/", "/ə/", "/ö/", "/ü/", "/ğ/", "/ı/", "/ç/", "/Ş/", "/Ə/", "/Ö/", "/Ü/", "/Ğ/", "/I/", "/İ/", "/Ç/");
    $tn = array("s", "e", "o", "u", "g", "i", "c","s", "e", "o", "u", "g", "i", "i", "c");
    $string = preg_replace($ts, $tn, $string);

    $accents_regex = '~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|th|tilde|uml);~i';
    $special_cases = array( '&' => 'and', "'" => '');
    $string = mb_strtolower( trim( $string ), 'UTF-8' );
    $string = str_replace( array_keys($special_cases), array_values( $special_cases), $string );
    $string = preg_replace( $accents_regex, '$1', htmlentities( $string, ENT_QUOTES, 'UTF-8' ) );
    $string = preg_replace("/[^a-z0-9]/u", "$separator", $string);
    $string = preg_replace("/[$separator]+/u", "$separator", $string);
    return $string;
}

function convert($size){
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

function generateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateRandomNumber($length = 5) {
    $characterss = '0123456789';
    $charactersLengths = strlen($characterss);
    $randomStrings = '';
    for ($i = 0; $i < $length; $i++) {
        $randomStrings .= $characterss[rand(0, $charactersLengths - 1)];
    }
    return $randomStrings;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'il',
        'm' => 'ay',
        'w' => 'həftə',
        'd' => 'gün',
        'h' => 'saat',
        'i' => 'dəqiqə',
        's' => 'saniyə',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' əvvəl' : 'indi';
}

function convertToHoursMins($time, $format = '%02d:%02d') {
    if ($time < 1) {
        return;
    }
    $hours = floor($time / 60);
    $minutes = ($time % 60);
    return sprintf($format, $hours, $minutes);
}

function addlog($user_id, $module, $eventId, $content) {

    $settingaddLog = $GLOBALS['settingaddLog'];

    if($settingaddLog['setting_value'] == 1){

        $db = $GLOBALS['db'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip_addr = $_SERVER['HTTP_CLIENT_IP'];
        } else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip_addr = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip_addr = $_SERVER['REMOTE_ADDR'];
        }

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $log = addslashes('{"log":[ { "user":"'.$user_id.'", "clientIp":"'.$ip_addr.'", "clientUA":"'.$user_agent.'", "URL":"'.$actual_link.'", "module":"'.$module.'", "eventId":"'.$eventId.'", "content":"'.$content.'", "userContent":"'.$userContent.'", "date":"' . date('Y-m-d H:i:s') . '" } ]}');

        mysqli_query($db, 'INSERT INTO logs ( module, eventId, log, content, createdby ) VALUES ("'.$module.'", "'.$eventId.'", "'.$log.'", "'.$content.'", "'.$user_id.'")');

    }

}

function generate_webp_image($file, $compression_quality = 100, $fileBasename){

    $dir = "./uploads/";
  
    if (!file_exists($file)) {
        return false;
    }

    $output_file = $fileBasename.".webp";

    if($compression_quality == 1){
        $output_file = "thumb-".$output_file;
    }
    
    if (file_exists($dir.$output_file)) {
        return $output_file;
    }

    $file_type = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if (function_exists('imagewebp')) {

        switch ($file_type) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($file);
                break;

            case 'png':
                $image = imagecreatefrompng($file);
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;

            case 'gif':
                $image = imagecreatefromgif($file);
                break;
            default:
                return false;
        }

        // Save the image
        $result = imagewebp($image, $dir.$output_file, $compression_quality);
        if (false === $result) {
            return false;
        }

        // Free up memory
        imagedestroy($image);

        return $output_file;
    } elseif (class_exists('Imagick')) {
        $image = new Imagick();
        $image->readImage($file);

        if ($file_type === 'png') {
            $image->setImageFormat('webp');
            $image->setImageCompressionQuality($compression_quality);
            $image->setOption('webp:lossless', 'true');
        }

        $image->writeImage($output_file);
        return $output_file;
    }

    return false;
}

function cropimg($imgSrc){

    list($width, $height) = getimagesize($imgSrc);

    $file_type = strtolower(pathinfo($imgSrc, PATHINFO_EXTENSION));
    switch ($file_type) {
        case 'jpeg':
        case 'jpg':
            $myImage = imagecreatefromjpeg($imgSrc);
            break;

        case 'png':
            $myImage = imagecreatefrompng($imgSrc);
            imagepalettetotruecolor($myImage);
            imagealphablending($myImage, true);
            imagesavealpha($myImage, true);
            break;

        case 'gif':
            $myImage = imagecreatefromgif($imgSrc);
            break;
        default:
            return false;
    }

    if ($width > $height) {
        $y = 0;
        $x = ($width - $height) / 2;
        $smallestSide = $height;
        $thumbSize = $height;
    } else {
        $x = 0;
        $y = ($height - $width) / 2;
        $smallestSide = $width;
        $thumbSize = $width;
    }

    //$thumbSize = 1000;
    $thumb = imagecreatetruecolor($thumbSize, $thumbSize);
    imagecopyresampled($thumb, $myImage, 0, 0, $x, $y, $thumbSize, $thumbSize, $smallestSide, $smallestSide);

    switch ($file_type) {
        case 'jpeg':
        case 'jpg':
            imagejpeg($thumb, $imgSrc);
            break;
        case 'png':
            imagepng($thumb, $imgSrc);
            break;
        case 'gif':
            imagejpeg($thumb, $imgSrc);
            break;
        default:
            imagejpeg($thumb, $imgSrc);
    }

}

function uploadFiles($tmp_name, $name, $sort, $generate, $crop){

    $dir = "./uploads/";

    if($name == "blob"){
        $pathinfo = "png";
    } else{
        $pathinfo = strtolower(pathinfo($name,PATHINFO_EXTENSION));
    }

    $fileBasename = basename(md5($sort.date("Y")."".date("m")."".date("d").round(microtime(true))));
    $filenameOrg = basename($fileBasename.".".$pathinfo);

    if($generate == 1){

        if (move_uploaded_file($tmp_name, $dir.$filenameOrg)) {

            if($crop == 1){
                cropimg($dir.$filenameOrg);
            }

            if($pathinfo != "svg" && $pathinfo != "webp"){
                $filename = generate_webp_image($dir.$filenameOrg, 100, $fileBasename);
                generate_webp_image($dir.$filenameOrg, 1, $fileBasename);
                unlink($dir.$filenameOrg);
            } else{
                $filename = $filenameOrg;
            }
                
            return $filename;

        } else {
            return "Error";
        }

    } else{

        if (move_uploaded_file($tmp_name, $dir.$filenameOrg)) {
                
            return $filenameOrg;
    
        } else {
            return "Error";
        }

    }

}

?>