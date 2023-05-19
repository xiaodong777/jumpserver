<?php
error_log("functions.php has been loaded.\n", 3, 'functions.log');

class tintinArmSystem {
    private $jumpserver_url = "https://jumpserver.lol:443";
    private $username = "bot";
    private $password = "password";
    private $access_key = "key";
    private $secret_key = "key";
    private $token;

public function create_asset($hostname, $ip, $node, $platform, $protocols) {
    $headers = array(
        "Authorization: Bearer " . $this->token,
        "Content-Type: application/json"
    );

    // 创建新资产
    $create_url = $this->jumpserver_url . "/api/v1/assets/assets/";
    $create_data = array(
        "hostname" => $hostname,
        "ip" => $ip,
        // debug "nodes" => array($node),
        "nodes" => $node, 
        "platform" => $platform,  // 添加平台字段
        //新增protocols字段
        "protocols" => $protocols
    );

    return $this->send_request('POST', $create_url, $create_data, $headers);
}
    public function __construct() {
        $this->token = $this->authenticate();
    }

    // 更新资产部分功能实现
    public function update_asset($id, $hostname, $ip, $node, $platform,$protocols) {
        $headers = array(
            "Authorization: Bearer " . $this->token,
            "Content-Type: application/json"
        );

        // 更新资产
        $update_url = $this->jumpserver_url . "/api/v1/assets/assets/" . $id . "/";
        $update_data = array(
            "hostname" => $hostname,
            "ip" => $ip,
            "nodes" => array($node),
            "platform" => $platform,
            "protocols" => $protocols,// 添加平台字段
        );

        return $this->send_request('PUT', $update_url, $update_data, $headers);
    }

    private function authenticate() {
        $auth_url = $this->jumpserver_url . "/api/v1/authentication/auth/";
        $auth_data = array(
            "username" => $this->username,
            "password" => $this->password,
            "public_key" => $this->access_key,
            "private_key" => $this->secret_key
        );

        $result = $this->send_request('POST', $auth_url, $auth_data);
        return $result["token"];
    }

    public function get_assets_and_nodes() {
        return array(
            "assets" => $this->get_raw_assets(),
            "nodes" => $this->get_nodes(),
        );
    }

    public function get_nodes() {
        $nodes_url = $this->jumpserver_url . "/api/v1/assets/nodes/";
        $headers = array(
            "Authorization: Bearer " . $this->token,
            "Content-Type: application/json"
        );
        return $this->send_request('GET', $nodes_url, null, $headers);
    }

    public function get_raw_assets() {
        $assets_url = $this->jumpserver_url . "/api/v1/assets/assets/";
        $headers = array(
            "Authorization: Bearer " . $this->token,
            "Content-Type: application/json"
        );
        return $this->send_request('GET', $assets_url, null, $headers);
    }

public function get_raw_nodes() {
    $nodes_url = $this->jumpserver_url . "/api/v1/assets/nodes/";
    $headers = array(
        "Authorization: Bearer " . $this->token,
        "Content-Type: application/json"
    );
    return $this->send_request('GET', $nodes_url, null, $headers);
}

private function send_request($method, $url, $data = null, $headers = array("Content-Type: application/json")) {
    $ch = curl_init($url);

    switch ($method) {
        case 'GET':
            if (!empty($data)) {
                $url .= '?' . http_build_query($data);
            }
            break;
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, 1);
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            if (!empty($data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
            break;
        default:
            throw new Exception('Invalid HTTP method ' . $method);
            break;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);

    // 记录cURL的错误信息和HTTP状态码
    $curl_error = curl_error($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    error_log("cURL error: " . $curl_error . "\n", 3, 'functions.log');
    error_log("HTTP status code: " . $http_code . "\n", 3, 'functions.log');

    curl_close($ch);

    return json_decode($response, true);
}
}
?>
