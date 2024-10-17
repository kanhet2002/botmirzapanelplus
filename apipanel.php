<?php

class PanelAPI {
    private $marzbanUrl;
    private $alirezaUrl;

    public function __construct($marzbanUrl, $alirezaUrl) {
        $this->marzbanUrl = $marzbanUrl;
        $this->alirezaUrl = $alirezaUrl;
    }

    // متد ارسال درخواست به مارزبان
    private function sendMarzbanRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->marzbanUrl . '/' . $endpoint;
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => $method,
                'content' => $data ? json_encode($data) : null,
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result, true);
    }

    // متد ارسال درخواست به علیرضا
    private function sendAlirezaRequest($endpoint, $method = 'GET', $data = null) {
        $url = $this->alirezaUrl . '/' . $endpoint;
        $options = [
            'http' => [
                'header' => "Content-Type: application/json\r\n",
                'method' => $method,
                'content' => $data ? json_encode($data) : null,
            ],
        ];
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return json_decode($result, true);
    }

    // متد برای دریافت کاربران
    public function getUsers($panel) {
        if ($panel === 'marzban') {
            return $this->sendMarzbanRequest('users');
        } elseif ($panel === 'alireza') {
            return $this->sendAlirezaRequest('users');
        }
        return null;
    }

    // متد برای افزودن کاربر به مارزبان
    public function createMarzbanUser($username, $password) {
        $data = [
            'username' => $username,
            'password' => $password
        ];
        return $this->sendMarzbanRequest('users', 'POST', $data);
    }

    // متد برای افزودن کاربر به علیرضا
    public function createAlirezaUser($username, $password) {
        $data = [
            'username' => $username,
            'password' => $password
        ];
        return $this->sendAlirezaRequest('users', 'POST', $data);
    }

    // متد برای انتخاب پنل و افزودن کاربر
    public function createUser($panel, $username, $password) {
        if ($panel === 'marzban') {
            return $this->createMarzbanUser($username, $password);
        } elseif ($panel === 'alireza') {
            return $this->createAlirezaUser($username, $password);
        }
        return null;
    }
}

// مثال استفاده از کلاس
$marzbanApi = new PanelAPI('https://marzban.example.com/api', 'https://alireza.example.com/api');

// افزودن کاربر به مارزبان
$response = $marzbanApi->createUser('marzban', 'newUser', 'userPassword');
print_r($response);

// افزودن کاربر به علیرضا
$response = $marzbanApi->createUser('alireza', 'newUser', 'userPassword');
print_r($response);

?>
