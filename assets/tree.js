var treeSelector = $('#jsTree_DocsTree');
treeSelector.on('move_node.jstree', function (node, parent) {
    console.log(node);
    console.log(parent);
    $.ajax({
        async: false,
        type: 'GET',
        url: '/admin/docs/default/move-node',
        data: {
            'id': parent.node.id.replace('node_', ''),
            'ref': parent.parent.replace('node_', ''),
            'position': parent.position
        },
        success: function (data) {
            if (data.status) {
                common.notify(data.message, 'success');
            } else {
                common.notify(data.message, 'error');
            }
        }
    });
});

treeSelector.on('rename_node.jstree', function (node, text) {
    if (text.old !== text.text) {
        $.ajax({
            async: false,
            type: 'GET',
            url: "/admin/docs/default/rename-node",
            dataType: 'json',
            data: {
                "id": text.node.id.replace('node_', ''),
                text: text.text
            },
            success: function (data) {
                common.notify(data.message, 'success');
            }
        });
    }
});
//Need dev.
treeSelector.on('create_node.jstree', function (node, parent, position) {
    $.ajax({
        async: false,
        type: 'GET',
        url: "/admin/docs/default/create-node",
        dataType: 'json',
        data: {
            text: parent.node.text,
            parent_id: parent.parent.replace('node_', '')
        },
        success: function (data) {
            common.notify(data.message, 'success');
        }
    });
});

treeSelector.on("delete_node.jstree", function (node, parent) {
    $.ajax({
        async: false,
        type: 'GET',
        url: "/admin/docs/default/delete",
        data: {
            "id": parent.node.id.replace('node_', '')
        }
    });
});

function switchNode(node) {
    $.ajax({
        async: false,
        type: 'GET',
        url: "/admin/docs/default/switch-node",
        dataType: 'json',
        data: {
            id: node.id.replace('node_', ''),
        },
        success: function (data) {
            var icon = (data.switch) ? 'flaticon-eye' : 'flaticon-eye-close';
            common.notify(data.message, 'success');
            treeSelector.jstree(true).set_icon(node, icon);
        }
    });
}




