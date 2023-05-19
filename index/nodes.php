<?php
//排错工具，用于查看节点ID字段信息
require_once 'functions.php';

$tintinArmSystem = new tintinArmSystem();
$assetsAndNodes = $tintinArmSystem->get_assets_and_nodes();
$nodesList = $assetsAndNodes['nodes'];

foreach ($nodesList as $node) {
    echo "Node ID: " . $node['id'] . "<br>";
    echo "Node Name: " . $node['name'] . "<br>";
    echo "<hr>";
}
?>
