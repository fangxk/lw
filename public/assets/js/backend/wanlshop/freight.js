"use strict";
define(["jquery", "bootstrap", "backend", "table", "form"], function (e, t, i, a, r) {
    var l = {
        index: function () {
            a.api.init({
                extend: {
                    index_url: "wanlshop/freight/index" + location.search,
                    add_url: "wanlshop/freight/add",
                    edit_url: "wanlshop/freight/edit",
                    del_url: "wanlshop/freight/del",
                    multi_url: "wanlshop/freight/multi",
                    table: "wanlshop_shop_freight"
                }
            });
            var t = e("#table");
            t.bootstrapTable({
                url: e.fn.bootstrapTable.defaults.extend.index_url,
                pk: "id",
                sortName: "id",
                columns: [[{checkbox: !0}, {field: "id", title: __("Id")}, {
                    field: "name",
                    title: __("Name")
                }, {
                    field: "shop.shopname",
                    title: __("shop.shopname"),
                    formatter: a.api.formatter.search
                }, {
                    field: "delivery",
                    title: __("Delivery"),
                    searchList: {
                        0: __("Delivery 0"),
                        1: __("Delivery 1"),
                        2: __("Delivery 2"),
                        3: __("Delivery 3"),
                        4: __("Delivery 4"),
                        5: __("Delivery 5"),
                        6: __("Delivery 6"),
                        7: __("Delivery 7"),
                        8: __("Delivery 8"),
                        9: __("Delivery 9"),
                        10: __("Delivery 10"),
                        11: __("Delivery 11"),
                        12: __("Delivery 12"),
                        13: __("Delivery 13"),
                        14: __("Delivery 14"),
                        15: __("Delivery 15"),
                        16: __("Delivery 16"),
                        17: __("Delivery 17"),
                        18: __("Delivery 18")
                    },
                    formatter: a.api.formatter.normal
                }, {
                    field: "isdelivery",
                    title: __("Isdelivery"),
                    searchList: {0: __("Isdelivery 0"), 1: __("Isdelivery 1")},
                    formatter: a.api.formatter.normal
                }, {
                    field: "valuation",
                    title: __("Valuation"),
                    searchList: {0: __("Valuation 0"), 1: __("Valuation 1"), 2: __("Valuation 2")},
                    formatter: a.api.formatter.normal
                }, {
                    field: "createtime",
                    title: __("Createtime"),
                    operate: "RANGE",
                    addclass: "datetimerange",
                    formatter: a.api.formatter.datetime
                }, {
                    field: "updatetime",
                    title: __("Updatetime"),
                    operate: "RANGE",
                    addclass: "datetimerange",
                    formatter: a.api.formatter.datetime
                }, {
                    field: "status",
                    title: __("Status"),
                    searchList: {normal: __("Normal"), hidden: __("Hidden")},
                    formatter: a.api.formatter.status
                }]]
            }), a.api.bindevent(t)
        }, recyclebin: function () {
            a.api.init({extend: {dragsort_url: ""}});
            var t = e("#table");
            t.bootstrapTable({
                url: "wanlshop/freight/recyclebin" + location.search,
                pk: "id",
                sortName: "id",
                columns: [[{checkbox: !0}, {field: "id", title: __("Id")}, {
                    field: "name",
                    title: __("Name"),
                    align: "left"
                }, {
                    field: "deletetime",
                    title: __("Deletetime"),
                    operate: "RANGE",
                    addclass: "datetimerange",
                    formatter: a.api.formatter.datetime
                }, {
                    field: "operate",
                    width: "130px",
                    title: __("Operate"),
                    table: t,
                    events: a.api.events.operate,
                    buttons: [{
                        name: "Restore",
                        text: __("Restore"),
                        classname: "btn btn-xs btn-info btn-ajax btn-restoreit",
                        icon: "fa fa-rotate-left",
                        url: "wanlshop/freight/restore",
                        refresh: !0
                    }, {
                        name: "Destroy",
                        text: __("Destroy"),
                        classname: "btn btn-xs btn-danger btn-ajax btn-destroyit",
                        icon: "fa fa-times",
                        url: "wanlshop/freight/destroy",
                        refresh: !0
                    }],
                    formatter: a.api.formatter.operate
                }]]
            }), a.api.bindevent(t)
        }, add: function () {
            l.api.bindevent()
        }, edit: function () {
            l.api.bindevent()
        }, api: {
            bindevent: function () {
                r.api.bindevent(e("form[role=form]"))
            }
        }
    };
    return l
});