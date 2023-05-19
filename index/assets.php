<?php
//排错工具，用于查看资产ID字段信息
require_once 'functions.php';

$tintinArmSystem = new tintinArmSystem();


$assetsAndNodes = $tintinArmSystem->get_assets_and_nodes();
$assetsList = $assetsAndNodes['assets'];


foreach ($assetsList as $asset) {
    echo "Asset ID: " . $asset['id'] . "<br>";
    echo "Asset Hostname: " . $asset['hostname'] . "<br>";
    echo "Asset IP: " . $asset['ip'] . "<br>";
    echo "Asset Platform: " . $asset['platform'] . "<br>";
    echo "Asset Admin User: " . $asset['admin_user'] . "<br>";
    echo "Protocols: " . implode(", ", $asset['protocols']) . "<br>";
    echo "Nodes: " . implode(", ", $asset['nodes']) . "<br>";
    echo "<hr>";
}

?>
