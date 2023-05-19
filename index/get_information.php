<?php
require_once('functions.php');
$system = new tintinArmSystem();

$raw_assets = $system->get_raw_assets();
$raw_nodes = $system->get_raw_nodes();

// 重点：此处用于填写开放编辑和新增的资产节点部分允许哪些节点编辑就把节点ID放哪些其他节点则不读取自然就无法编辑。
$desired_node_ids = array(
    "b982d53d-c654-4b59-b774-3bbdd64c3bed"
);

// 过滤节点
$nodes = array_filter($raw_nodes, function($node) use ($desired_node_ids) {
    return in_array($node['id'], $desired_node_ids);
});

// 过滤资产
$assets = array_filter($raw_assets, function($asset) use ($desired_node_ids) {
    foreach ($asset['nodes'] as $node_id) {
        if (in_array($node_id, $desired_node_ids)) {
            return true;
        }
    }
    return false;
});



file_put_contents('assets.json', json_encode(array_values($assets)));
file_put_contents('nodes.json', json_encode(array_values($nodes)));

error_log("JSON result: " . $result, 3, 'get_information.log');  

echo $result;
?>
