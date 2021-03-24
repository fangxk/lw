define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                showColumns: false,
                showExport: false,
                commonSearch: false,
                extend: {
                    index_url: 'collar/advvideo/index' + location.search,
                    add_url: 'collar/advvideo/add',
                    edit_url: 'collar/advvideo/edit',
                    del_url: 'collar/advvideo/del',
                    multi_url: 'collar/advvideo/multi',
                    import_url: 'collar/advvideo/import',
                    table: 'collar_adv_video',
                }
            });
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "广告名称";};
            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        /* {field: 'weigh', title: __('Weigh'), operate: false},*/
                        {field: 'title', title: __('Title')},
                        {field: 'link', title: __('Link')},
                        {field: 'desc', title: __('Desc'), operate: 'LIKE'},
                        {field: 'video', title: __('Video'), operate: 'LIKE'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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