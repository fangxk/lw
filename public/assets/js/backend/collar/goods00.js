define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {
    Fast.config.openArea = ['90%','90%'];
    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                showColumns: false,
                showExport: false,
                commonSearch: false,
                extend: {
                    index_url: 'collar/goods/index' + location.search,
                    add_url: 'collar/goods/add',
                    edit_url: 'collar/goods/edit',
                    del_url: 'collar/goods/del',
                    multi_url: 'collar/goods/multi',
                    import_url: 'collar/goods/import',
                    table: 'collar_goods',
                }
            });
            $.fn.bootstrapTable.locales[Table.defaults.locale]['formatSearch'] = function(){return "商品名称";};
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
                        {field: 'collargoodscategory.name', title: __('Collargoodscategory.name'), operate: 'LIKE'},
                       /* {field: 'weigh', title: __('Weigh'), operate: false},*/
                        {field: 'name', title: __('Name')},
                        {field: 'goods_status', title: __('Goods_status'), searchList: {"1":__('Goods_status 1'),"0":__('Goods_status 0'),"2":__('Goods_status 2'),"3":__('Goods_status 3')}, formatter: Table.api.formatter.status},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'total', title: __('Total')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        /*{field: 'collargoodscategory.id', title: __('Collargoodscategory.id')},*/
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        recyclebin: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    'dragsort_url': ''
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: 'collar/goods/recyclebin' + location.search,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), align: 'left'},
                        {
                            field: 'deletetime',
                            title: __('Deletetime'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            formatter: Table.api.formatter.datetime
                        },
                        {
                            field: 'operate',
                            width: '130px',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'Restore',
                                    text: __('Restore'),
                                    classname: 'btn btn-xs btn-info btn-ajax btn-restoreit',
                                    icon: 'fa fa-rotate-left',
                                    url: 'collar/goods/restore',
                                    refresh: true
                                },
                                {
                                    name: 'Destroy',
                                    text: __('Destroy'),
                                    classname: 'btn btn-xs btn-danger btn-ajax btn-destroyit',
                                    icon: 'fa fa-times',
                                    url: 'collar/goods/destroy',
                                    refresh: true
                                }
                            ],
                            formatter: Table.api.formatter.operate
                        }
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