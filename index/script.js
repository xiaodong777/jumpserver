$(document).ready(function() {
    $("#edit-dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons: {
            "保存": function() {
                // 在这里添加保存按钮的处理代码
                $(this).dialog("close");
            },
            "取消": function() {
                $(this).dialog("close");
            }
        }
    });

    $("#edit-button").click(function() {
        $("#edit-dialog").dialog("open");
    });
});
