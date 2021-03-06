"use strict";
define(["jquery", "bootstrap", "backend", "table", "form"],
    function(e, t, a, i, r) {
        var n = {
            index: function() {
                i.api.init({
                    extend: {
                        index_url: "wanlshop/withdraw/index" + location.search,
                        add_url: "",
                        edit_url: "",
                        del_url: "wanlshop/withdraw/del",
                        multi_url: "wanlshop/withdraw/multi",
                        import_url: "wanlshop/withdraw/import",
                        table: "withdraw"
                    }
                });
                var t = e("#table");
                t.bootstrapTable({
                    url: e.fn.bootstrapTable.defaults.extend.index_url,
                    pk: "id",
                    sortName: "id",
                    columns: [[{
                        checkbox: !0
                    },
                        {
                            field: "id",
                            title: __("Id")
                        },
                        {
                            field: "user_id",
                            title: __("User_id")
                        },
                        {
                            field: "money",
                            title: __("Money"),
                            operate: "BETWEEN"
                        },
                        {
                            field: "handingfee",
                            title: __("Handingfee"),
                            operate: "BETWEEN"
                        },
                        {
                            field: "taxes",
                            title: __("Taxes"),
                            operate: "BETWEEN"
                        },
                        {
                            field: "type",
                            title: __("Type")
                        },
                        {
                            field: "account",
                            title: __("Account")
                        },
                        {
                            field: "memo",
                            title: __("Memo")
                        },
                        {
                            field: "status",
                            title: __("Status"),
                            searchList: {
                                created: __("Status created"),
                                successed: __("Status successed"),
                                rejected: __("Status rejected")
                            },
                            formatter: i.api.formatter.status
                        },
                        {
                            field: "transfertime",
                            title: __("Transfertime"),
                            operate: "RANGE",
                            addclass: "datetimerange",
                            formatter: i.api.formatter.datetime
                        },
                        {
                            field: "createtime",
                            title: __("Createtime"),
                            operate: "RANGE",
                            addclass: "datetimerange",
                            formatter: i.api.formatter.datetime
                        },
                        {
                            field: "updatetime",
                            title: __("Updatetime"),
                            operate: "RANGE",
                            addclass: "datetimerange",
                            formatter: i.api.formatter.datetime
                        },
                        {
                            field: "operate",
                            title: __("Operate"),
                            table: t,
                            events: i.api.events.operate,
                            buttons: [{
                                name: "agree",
                                title: __("??????????????????"),
                                classname: "btn btn-xs btn-success btn-magic btn-ajax",
                                icon: "fa fa-check",
                                text: "??????",
                                confirm: "??????????????????????????????????????????",
                                url: "wanlshop/withdraw/agree",
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
                                    title: __("??????????????????"),
                                    classname: "btn btn-xs btn-danger btn-dialog",
                                    icon: "fa fa-times",
                                    text: "??????",
                                    url: "wanlshop/withdraw/refuse",
                                    visible: function(e) {
                                        if ("created" == e.status) return ! 0
                                    },
                                    extend: 'data-area=["500px","270px"]'
                                },
                                {
                                    name: "detail",
                                    title: __("??????"),
                                    classname: "btn btn-xs btn-info btn-dialog",
                                    icon: "fa fa-eye",
                                    url: "wanlshop/withdraw/detail"
                                }],
                            formatter: i.api.formatter.operate
                        }]]
                }),
                    i.api.bindevent(t)
            },
            refuse: function() {
                n.api.bindevent()
            },
            api: {
                bindevent: function() {
                    r.api.bindevent(e("form[role=form]"))
                }
            }
        };
        return n
    });