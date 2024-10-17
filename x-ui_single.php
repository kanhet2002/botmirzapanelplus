<?php
require_once 'config.php';

function login($url, $username, $password) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url . '/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT_MS => 4000,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "username=$username&password=$password",
        CURLOPT_COOKIEJAR => 'cookie.txt',
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $token = [];
        $token['error'] = curl_error($curl);
        return $token;
    }
    curl_close($curl);
    return json_decode($response, true);
}

function get_Client($username, $namepanel) {
    global $connect;
    $panel_info = select("marzban_panel", "*", "name_panel", $namepanel, "select");
    login($panel_info['url_panel'], $panel_info['username_panel'], $panel_info['password_panel']);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $panel_info['url_panel'] . '/panel/api/inbounds/getClientTraffics/' . $username,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Accept: application/json'),
        CURLOPT_COOKIEFILE => 'cookie.txt',
    ));
    $response = json_decode(curl_exec($curl), true)['obj'];
    curl_close($curl);
    unlink('cookie.txt');
    return $response;
}

function get_clinets($username, $namepanel) {
    global $connect;
    $panel_info = select("marzban_panel", "*", "name_panel", $namepanel, "select");
    login($panel_info['url_panel'], $panel_info['username_panel'], $panel_info['password_panel']);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $panel_info['url_panel'] . '/panel/api/inbounds/list',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Accept: application/json'),
        CURLOPT_COOKIEFILE => 'cookie.txt',
    ));
    $response = json_decode(curl_exec($curl), true)['obj'];
    foreach ($response as $client) {
        $client = json_decode($client['settings'], true)['clients'];
        foreach ($client as $clinets) {
            if ($clinets['email'] == $username) {
                $output = $clinets;
                break;
            }
        }
    }
    curl_close($curl);
    unlink('cookie.txt');
    return $output;
}

function addClient($namepanel, $usernameac, $Expire, $Total, $Uuid, $Flow, $subid) {
    global $connect;
    $panel_info = select("marzban_panel", "*", "name_panel", $namepanel, "select");
    $Allowedusername = get_Client($usernameac, $namepanel);
    
    // ایجاد نام کاربری جدید در صورت تکراری
    if (isset($Allowedusername['email'])) {
        $random_number = rand(1000000, 9999999);
        $username_ac = $usernameac . $random_number;
    }

    login($panel_info['url_panel'], $panel_info['username_panel'], $panel_info['password_panel']);
    
    // پیکربندی برای اضافه کردن کاربر
    $config = array(
        "id" => intval($panel_info['inboundid']),
        'settings' => json_encode(array(
            'clients' => array(
                array(
                    "id" => $Uuid,
                    "flow" => $Flow,
                    "email" => $username_ac,
                    "totalGB" => $Total,
                    "expiryTime" => $Expire,
                    "enable" => true,
                    "tgId" => "",
                    "subId" => $subid,
                    "reset" => 0
                )
            ),
            'decryption' => 'none',
            'fallbacks' => array(),
        ))
    );

    $configpanel = json_encode($config, true);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $panel_info['url_panel'] . '/panel/api/inbounds/addClient',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $configpanel,
        CURLOPT_COOKIEFILE => 'cookie.txt',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    unlink('cookie.txt');
    return json_decode($response, true);
}

// تابع برای پنل علیرضا
function loginAlireza($url, $username, $password) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url . '/login',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT_MS => 4000,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => "username=$username&password=$password",
        CURLOPT_COOKIEJAR => 'cookie.txt',
    ));
    $response = curl_exec($curl);
    if (curl_error($curl)) {
        $token = [];
        $token['error'] = curl_error($curl);
        return $token;
    }
    curl_close($curl);
    return json_decode($response, true);
}

// سایر توابع مشابه برای پنل علیرضا
function get_Client_Alireza($username) {
    global $connect;
    $panel_info = select("alireza_panel", "*", "name_panel", "Alireza", "select");
    loginAlireza($panel_info['url_panel'], $panel_info['username_panel'], $panel_info['password_panel']);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $panel_info['url_panel'] . '/api/inbounds/getClientTraffics/' . $username,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => array('Accept: application/json'),
        CURLOPT_COOKIEFILE => 'cookie.txt',
    ));
    $response = json_decode(curl_exec($curl), true)['obj'];
    curl_close($curl);
    unlink('cookie.txt');
    return $response;
}

function addClient_Alireza($usernameac, $Expire, $Total, $Uuid, $Flow, $subid) {
    global $connect;
    $panel_info = select("alireza_panel", "*", "name_panel", "Alireza", "select");
    
    loginAlireza($panel_info['url_panel'], $panel_info['username_panel'], $panel_info['password_panel']);
    
    // پیکربندی برای اضافه کردن کاربر
    $config = array(
        'email' => $usernameac,
        'totalGB' => $Total,
        'expiryTime' => $Expire,
        'id' => $Uuid,
        'flow' => $Flow,
        'subId' => $subid,
        'enable' => true
    );

    $configpanel = json_encode($config, true);
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $panel_info['url_panel'] . '/api/inbounds/addClient',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $configpanel,
        CURLOPT_COOKIEFILE => 'cookie.txt',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/json',
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    unlink('cookie.txt');
    return json_decode($response, true);
}

// توابع دیگر برای مدیریت کاربران در پنل علیرضا می‌توانید بر اساس الگوی موجود اضافه کنید
?>
