define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                showColumns: false,
                showExport: false,
                commonSearch: false,
                extend: {
                    index_url: 'collar/withdraw/index' + location.search,
                    add_url: 'collar/withdraw/add',
                    /*edit_url: 'collar/withdraw/edit',*/
                    del_url: 'collar/withdraw/del',
                    multi_url: 'collar/withdraw/multi',
                    import_url: 'collar/withdraw/import',
                    table: 'collar_withdraw',
                }
            });

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
                        {field: 'orderid', title: __('Orderid'), operate: 'LIKE'},
                        {field: 'transactionid', title: __('Transactionid'), operate: 'LIKE'},
                        {field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                       /* {field: 'user_id', title: __('User_id')},*/
                        {field: 'money', title: __('Money'), operate:'BETWEEN'},
                        {field: 'handingfee', title: __('Handingfee'), operate:'BETWEEN'},
                        {field: 'type', title: __('Type'), operate: 'LIKE'},
                        {field: 'taxes', title: __('Taxes'), operate:'BETWEEN'},
                        {field: 'account', title: __('Account'), operate: 'LIKE'},
                        {field: 'status', title: __('Status'), searchList: {"created":__('Status created'),"successed":__('Status successed'),"rejected":__('Status rejected')}, formatter: Table.api.formatter.status},
                        {field: 'memo', title: __('Memo'), operate: 'LIKE'},
                        {field: 'transfertime', title: __('Transfertime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        /*{field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},*/
                        /*{field: 'user.id', title: __('User.id')},*/
                        /*{field: 'user.username', title: __('User.username'), operate: 'LIKE'},
                        {field: 'user.nickname', title: __('User.nickname'), operate: 'LIKE'},*/

                        {field: 'operate', title: __('Operate'),
                            buttons: [
                                {
                                    name: "agree",
                                    title: __("同意提现申请"),
                                    classname: "btn btn-xs btn-success btn-magic btn-ajax",
                                    icon: "fa fa-check",
                                    text: "同意",
                                    confirm: "确认点击同意，通过提现申请？",
                                    url: "collar/withdraw/agree",
                                    visible: function(e) {
                                        if ("created" == e.status) return ! 0
                                    },
                                    success: function(e, a) {
                                        return t.bootstrapTable("refresh"),
                                            !1
                                    },
                                    error: function(e, t) {
                                        return console.log(e, t),
                                            Layer.alert(t.msg),
                                            !1
                                    }
                                },
                                {
                                    name: "refuse",
                                    title: __("拒绝提现申请"),
                                    classname: "btn btn-xs btn-danger btn-dialog",
                                    icon: "fa fa-times",
                                    text: "拒绝",
                                    url: "collar/withdraw/refuse",
                                    visible: function(e) {
                                        if ("created" == e.status) return ! 0
                                    },
                                    extend: 'data-area=["500px","270px"]'
                                },
                                {
                                    name: "detail",
                                    title: __("详情"),
                                    classname: "btn btn-xs btn-info btn-dialog",
                                    icon: "fa fa-eye",
                                    url: "collar/withdraw/detail"
                                }
                            ],
                            table: table, events: Table.api.events.operate,
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
        refuse: function() {
            Controller.api.bindevent()
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});