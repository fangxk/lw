define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                showColumns: false,
                showExport: false,
                commonSearch: false,
                extend: {
                    index_url: 'collar/prize/index' + location.search,
                    add_url: 'collar/prize/add',
                    edit_url: 'collar/prize/edit',
                    del_url: 'collar/prize/del',
                    multi_url: 'collar/prize/multi',
                    import_url: 'collar/prize/import',
                    table: 'collar_prize_log',
                }
            });
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "用户昵称";};
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
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'name', title: __('Name')},
                        {field: 'prize', title: __('Prize')},
                        {field: 'identiy', title: __('Identiy'), searchList: {"1":__('Identiy 1'),"0":__('Identiy 0')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
           $("input[name='row[identiy]']").click(function () {
                var identiy = $("input[name='row[identiy]']:checked").val();
               $("#users").show();
                if(identiy == 1){
                    $("#users").removeClass("form-group hidden").addClass("form-group");
                    $("#nickname").removeClass("form-group").addClass("form-group hidden");
                }
                if(identiy == 0){
                    $("#users").removeClass("form-group").addClass("form-group hidden");
                    $("#nickname").removeClass("form-group hidden").addClass("form-group");
                }
           })
            Controller.api.bindevent();
        },
        edit: function () {
            $("input[name='row[identiy]']").click(function () {
                var identiy = $("input[name='row[identiy]']:checked").val();
                $("#users").show();
                if(identiy == 1){
                    $("#users").removeClass("form-group hidden").addClass("form-group");
                    $("#nickname").removeClass("form-group").addClass("form-group hidden");
                }
                if(identiy == 0){
                    $("#users").removeClass("form-group").addClass("form-group hidden");
                    $("#nickname").removeClass("form-group hidden").addClass("form-group");
                }
            })
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