define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                showColumns: false,
                showExport: false,
                commonSearch: false,
                extend: {
                    index_url: 'collar/feedback/index' + location.search,
                    add_url: 'collar/feedback/add',
                    edit_url: 'collar/feedback/edit',
                    del_url: 'collar/feedback/del',
                    multi_url: 'collar/feedback/multi',
                    import_url: 'collar/feedback/import',
                    table: 'collar_feedback',
                }
            });
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "用户昵称";};
            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        {field: 'uid', title: __('Uid'),visible:false},
                        {field: 'name', title: __('Name')},
                        {field: 'feedback', title: __('Feedback'), operate: 'LIKE'},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'user.id', title: __('User.id'),visible: false},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'statusswitch', title: __('Statusswitch'), table: table, formatter: Table.api.formatter.toggle},
                        /*{field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE',visible: false},*/
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});