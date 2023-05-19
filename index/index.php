<!DOCTYPE html>
<html>
<head>
    <title>堡垒机资产管理平台</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        } 
        
        #edit-form {
    position: fixed; /* 让表单固定在页面上，不随滚动条滚动 */
    top: 50%; /* 把表单放在页面垂直居中的位置 */
    left: 50%; /* 把表单放在页面水平居中的位置 */
    transform: translate(-50%, -50%); /* 使用 transform 来精确调整表单的位置，使其完全居中 */
    background-color: white; /* 为表单添加一个背景色，使其在其他内容上面更显眼 */
    padding: 20px; /* 为表单添加一些内边距，让内容不会贴着边缘 */
    border: 1px solid black; /* 为表单添加一个边框 */
    z-index: 1000; /* 使用 z-index 使表单在其他内容之上 */
}


    </style>
    <!---<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">  google打不开的---> 
   <!--- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  google打不开的---> 
   <!--- <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>  google打不开的---> 
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.min.css">
    
</head>
<body>
    <h2>堡垒机资产管理平台</h2>
    
    <!-- 搜索部分 -->
    <input type="text" id="search" placeholder="输入搜索关键词实时搜索无需回车..."></br>
    <!---新增资产部分--->
    <button id="add-asset-button">新增资产</button>

    <div id="add-asset-modal" style="display: none;">
    <form id="add-asset-form">
        <input type="text" id="add-asset-hostname" placeholder="主机名称">
        <input type="text" id="add-asset-ip" placeholder="IP地址">
        <select id="add-asset-nodes"></select>
        <button type="submit">创建资产</button>
    </form>
    </div>

<?php
require_once 'get_information.php';

// 从JSON文件中获取资产和节点列表
$assetsList = json_decode(file_get_contents('assets.json'), true);
$nodesMap = json_decode(file_get_contents('nodes.json'), true);

// 将 nodesMap 转换为 nodesMap
$newNodesMap = array();
foreach ($nodesMap as $node) {
    $newNodesMap[$node['id']] = $node['name'];
}
$nodesMap = $newNodesMap;
    
//var_dump($assetsList);
//var_dump($nodesMap);
?>

    <!-- 主体信息 -->
    <table>
        <tr>
            <th>主机名称</th>
            <th>IP地址</th>
            <th>资产连接版本</th>
            <th>协议</th>
            <th>节点</th>
            <th>操作</th> <!-- 新增一个列，用于放置编辑按钮 -->
        </tr>
        <?php
            // 在此处处理资产列表：
            foreach ($assetsList as $asset) {
                echo "<tr>";

                echo "<td>" . htmlspecialchars($asset['hostname']) . "</td>";
                echo "<td>" . htmlspecialchars($asset['ip']) . "</td>";
                echo "<td>" . htmlspecialchars($asset['admin_user_display']) . "</td>";
                echo "<td>" . htmlspecialchars(implode(", ", $asset['protocols'])) . "</td>";

                // 通过节点ID查找节点名称
                $nodeNames = array();
                foreach ($asset['nodes'] as $nodeId) {
                    if (isset($nodesMap[$nodeId])) {
                        $nodeNames[] = htmlspecialchars($nodesMap[$nodeId]);
                    }
                }
                echo "<td>" . implode(", ", $nodeNames) . "</td>";
                echo "<td><button class='edit-button' data-id='" . htmlspecialchars($asset['id']) . "'>编辑</button></td>";
                echo "</tr>";
            }
        ?>
    </table>
    <!-- 编辑部分实现 -->
    <!-- 编辑表单，应该包含所有你想要让用户编辑的字段 -->
    <form id="edit-form" style="display: none;">
        <!-- 这是一个隐藏的输入框，用来存储当前正在编辑的资产ID -->
        <input type="hidden" id="asset-id">
        <!-- 以下是其他的输入框，应根据你的需要添加 -->
        <!-- 例如，如果你想让用户编辑资产的名称和IP地址，你可以添加以下两个输入框 -->
        <input type="text" id="asset-name" placeholder="资产名称">
        <input type="text" id="asset-ip" placeholder="资产IP地址">
        <!---新增一个平台字段--->
        
        <input type="hidden" id="asset-platform" value="Windows">
            <!-- 添加一个下拉菜单用于选择节点 -->
        <select id="asset-nodes"></select>
        <!-- 提交按钮 -->
        <button type="submit" id="edit-form-submit-button">提交</button>
            <!-- 取消编辑按钮 -->
        <button type="button" id="cancel-edit-button">取消</button>
    </form>

<script>
//搜索部分实现
$(document).ready(function() {
    $("#search").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("table tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

// 引入外部资源
var assetsList = <?php echo json_encode($assetsList); ?>;
var nodesMap = <?php echo json_encode($nodesMap); ?>;

// 日志文件函数
function logToFile(message) {
    $.ajax({
        url: 'log_to_file.php',
        method: 'POST',
        data: {
            message: message
        }
    });
}

//---新增资产部分---
$("#add-asset-button").click(function() {
    // 清空下拉菜单
    $("#add-asset-nodes").empty();

    // 将所有节点填充到下拉菜单中
    Object.keys(nodesMap).forEach(function(nodeId) {
        var nodeName = nodesMap[nodeId]; // 从nodesMap中获取节点名称
        $("#add-asset-nodes").append('<option value="' + nodeId + '">' + nodeName + '</option>');
    });

    // 显示模态窗口
    $("#add-asset-modal").show();
});

// 处理创建资产表单的提交事件
$("#add-asset-form").submit(function(e) {
    e.preventDefault();

    var hostname = $("#add-asset-hostname").val();
    console.log("Hostname: " + hostname);

    var newAssetData = {
        hostname: hostname,
        ip: $("#add-asset-ip").val(),
        nodes: [$("#add-asset-nodes").val()], 
        platform: 'Windows',   // 固定值不修改
        protocols: ['vnc/5900']  //网管可用默认协议

    };

    logToFile("New asset data: " + JSON.stringify(newAssetData));

    $.ajax({
        url: 'create_asset.php',
        method: 'POST',
        data: {
            data: newAssetData
        },
        success: function(response) {
            logToFile("Response: " + JSON.stringify(response));
            $("#add-asset-modal").hide();
            location.reload();
        }
    });
});

// 当页面加载完成时
$(".edit-button").click(function() {
    var assetId = $(this).data("id"); // 获取被点击按钮的资产ID
    var confirmEdit = confirm("你确定要编辑ID为 " + assetId + " 的资产吗？");
    if (confirmEdit) {
        // 设置隐藏的输入框的值为资产ID
        $("#asset-id").val(assetId);

        // 直接从assetsList中获取资产数据
        var asset = assetsList.find(function(asset) {
            return asset.id == assetId;
        });

        // 将资产数据填充到表单中
        $('#edit-form #asset-name').val(asset.hostname);
        $('#edit-form #asset-ip').val(asset.ip);
        $('#edit-form #asset-platform').val(asset.platform);

        // 将所有的节点填充到下拉菜单中
        var $assetNodesSelect = $('#edit-form #asset-nodes');
        $assetNodesSelect.empty(); // 清空下拉菜单
        Object.keys(nodesMap).forEach(function(nodeId) {
            var nodeName = nodesMap[nodeId]; // 从nodesMap中获取节点名称
            // 如果节点是当前资产的一部分，则选择它
            var isSelected = asset.nodes.includes(nodeId) ? ' selected' : '';
            $assetNodesSelect.append('<option value="' + nodeId + '"' + isSelected + '>' + nodeName + '</option>');
        });

        // 显示编辑表单
        $("#edit-form").show();
    }
});

$("#edit-form").submit(function(e) {
   e.preventDefault();

    var assetId = $("#asset-id").val();
    var newAssetData = {
        name: $("#asset-name").val(),
        ip: $("#asset-ip").val(),
        nodes: $("#asset-nodes").val(),
        platform: $("#asset-platform").val(), // 新增的platform字段
        protocols: ['vnc/5900'] 
    };

    logToFile("Asset ID: " + assetId);
    logToFile("New asset data: " + JSON.stringify(newAssetData));

    $.ajax({
        url: 'update_asset.php',
        method: 'POST',
        data: {
            id: assetId,
            data: newAssetData
        },
        success: function(response) {
            logToFile("Response: " + JSON.stringify(response));
            $("#edit-form").hide();
            location.reload();
        }
    });
});

// 取消编辑按钮的点击事件处理器
$("#cancel-edit-button").click(function() {
    $("#edit-form").hide(); // 隐藏编辑表单
});
</script>
</body>
</html>
