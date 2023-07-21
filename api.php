<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET["ip"])) {
    // 获取请求的IP地址
    $ip = $_GET["ip"];

    // 使用curl发起API请求
    $access_token = "d6a042beb20fc8"; // 替换为你的ipinfo.io API密钥
    $apiUrl = "http://ipinfo.io/{$ip}?token={$access_token}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $apiData = curl_exec($ch);
    curl_close($ch);

    // 将数据插入数据库
    $data = json_decode($apiData, true);
    insertDataIntoDatabase($data);

    // 返回JSON格式的查询结果
    echo $apiData;
}

// 将数据插入数据库
function insertDataIntoDatabase($data) {
    // 数据库配置
    $host = "localhost";
    $username = "ip6666";
    $password = "3cdNJdaTMpNjpa65";
    $database = "ip6666"; // 替换为你创建的数据库名称

    // 连接数据库
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        die("数据库连接失败：" . $conn->connect_error);
    }

    // 获取数据字段
    $ip = $data["ip"];
    $country = $data["country"];
    $region = $data["region"];
    $city = $data["city"];
    $org = $data["org"];

    // 将查询结果插入数据库
    $sql = "INSERT INTO ip_records (ip_address, country, region, city, org) VALUES ('$ip', '$country', '$region', '$city', '$org')";
    if ($conn->query($sql) === TRUE) {
        // 查询成功，不需要返回数据
    } else {
        // 查询失败，返回错误信息
        $errorData = array("error" => "查询失败：" . $conn->error);
        echo json_encode($errorData);
    }

    // 关闭数据库连接
    $conn->close();
}
