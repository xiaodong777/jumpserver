<?php
error_log("update_asset.php has been called.\n", 3, 'update_asset.log');
error_log("POST data: " . var_export($_POST, true), 3, "update_asset.log");
require_once 'functions.php';


// 检查是否有POST数据
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tintinArmSystem = new tintinArmSystem();

    // 获取表单提交的数据
$assetId = $_POST['id'];
$assetData = $_POST['data'];

$assetName = $assetData['name'];
$assetIp = $assetData['ip'];
$assetNodes = $assetData['nodes'];
$platform = $assetData['platform']; // 从 $assetData 获取 'platform'
$protocols = $assetData['protocols']; //写死协议
    // 更新资产信息

$result = $tintinArmSystem->update_asset($assetId, $assetName, $assetIp, $assetNodes, $platform,$protocols);


    // 记录更新结果到日志文件
    error_log("Debug: asset update result: " . var_export($result, true), 3, "update_asset.log");


    // 根据更新结果进行处理
    if ($result) {
        echo '资产信息更新成功';
    } else {
        echo '资产信息更新失败';
    }
} else {
    // 如果不是POST请求，那么重定向到主页面
    header('Location: index.php');
}
?>
