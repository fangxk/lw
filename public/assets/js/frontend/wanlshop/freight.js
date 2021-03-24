define(["jquery", "bootstrap", "backend", "table", "form", "vue"], function (e, t, i, r, n, c) {
    var s = {
        recyclebin: function () {
            r.api.init({extend: {dragsort_url: ""}});
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
                    formatter: r.api.formatter.datetime
                }, {
                    field: "operate",
                    width: "130px",
                    title: __("Operate"),
                    table: t,
                    events: r.api.events.operate,
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
                    formatter: r.api.formatter.operate
                }]]
            }), r.api.bindevent(t)
        }, add: function () {
            new c({
                el: "#app", data: function () {
                    return {
                        freightData: [],
                        valuation: 0,
                        isdelivery: 0,
                        regions: Config.area,
                        cityCount: 407,
                        checkAll: !1,
                        checked: {province: [], citys: []},
                        disable: {province: [], citys: [], treeData: {}},
                        forms: []
                    }
                }, mounted: function () {
                    this.initializeForms()
                }, methods: {
                    initializeForms: function () {
                        var e = this;
                        if (!this.freightData.length) return !1;
                        this.freightData.forEach(function (t) {
                            for (var i in t.citys) t.citys.hasOwnProperty(i) && (t.citys[i] = parseInt(t.citys[i]));
                            t.treeData = e.getTreeData({province: t.province, citys: t.citys}), e.forms.push(t)
                        })
                    }, onAddRegionEvent: function () {
                        var e = 0;
                        if (this.forms.forEach(function (t) {
                            e += t.citys.length
                        }), console.log(e), e >= this.cityCount) return layer.msg("已经选择了所有区域~"), !1;
                        var t = this;
                        this.onShowCheckBox({
                            complete: function (e) {
                                t.forms.push({province: e.province, citys: e.citys, treeData: t.getTreeData(e)})
                            }
                        })
                    }, onCheckAll: function (e) {
                        this.checkAll = e;

                        for (var t in this.regions) if (this.regions.hasOwnProperty(t)) {
                            var i = this.regions[t];
                            if (!this.isPropertyExist(i.id, this.disable.treeData) || !this.disable.treeData[i.id].isAllCitys) {
                                var r = parseInt(i.id);
                                this.checkedProvince(r, this.checkAll)
                            }
                        }
                    }, onDisableRegion: function (e) {
                        var t = {province: [], citys: []};
                        for (var i in this.forms) if (this.forms.hasOwnProperty(i)) {
                            if (e > -1 && e === parseInt(i)) continue;
                            var r = this.forms[i];
                            t.province = this.arrayMerge(t.province, r.province), t.citys = this.arrayMerge(t.citys, r.citys)
                        }
                        this.disable = {province: t.province, citys: t.citys, treeData: this.getTreeData(t)}
                    }, getTreeData: function (e) {
                        var t = this;
                        console.log(e);
                        var i = {};
                        return e.province.forEach(function (r) {
                            var n = t.regions[r], c = [], s = 0;
                            for (var o in n.city) if (n.city.hasOwnProperty(o)) {
                                var a = n.city[o];
                                t.inArray(a.id, e.citys) && c.push({id: a.id, name: a.name}), s++
                            }
                            i[n.id] = {id: n.id, name: n.name, citys: c, isAllCitys: c.length === s}
                        }), i
                    }, onEditerForm: function (e, t) {
                        var i = this;
                        this.onShowCheckBox({
                            editerFormIndex: e,
                            checkedData: {province: t.province, citys: t.citys},
                            complete: function (e) {
                                t.province = e.province, t.citys = e.citys, t.treeData = i.getTreeData(e)
                            }
                        })
                    }, onDeleteForm: function (e) {
                        var t = this;
                        layer.confirm("确定要删除吗？", {title: "友情提示"}, function (i) {
                            t.forms.splice(e, 1), layer.close(i)
                        })
                    }, onShowCheckBox: function (t) {
                        var i = e.extend(!0, {editerFormIndex: -1, checkedData: null, complete: e.noop()}, t);
                        this.checked = i.checkedData ? i.checkedData : {
                            province: [],
                            citys: []
                        }, this.onDisableRegion(i.editerFormIndex), this.checkAll = !1;
                        var r = this;
                        layer.open({
                            type: 1,
                            shade: !1,
                            moveOut: !0,
                            title: "选择可配送区域",
                            btn: ["确定", "取消"],
                            area: ["820px", "520px"],
                            content: e(this.$refs.choice),
                            yes: function (e) {
                                if (r.checked.citys.length <= 0) return layer.msg("请选择区域~"), !1;
                                i.complete(r.checked), layer.close(e)
                            }
                        })
                    }, onCheckedProvince: function (e) {
                        var t = parseInt(e.target.value);
                        this.checkedProvince(t, e.target.checked)
                    }, checkedProvince: function (e, t) {
                        var i = this.checked.province.indexOf(e);
                        t ? -1 === i && this.checked.province.push(e) : i > -1 && this.checked.province.splice(i, 1);
                        var r = this.regions[e].city;
                        for (var n in r) if (r.hasOwnProperty(n)) {
                            var c = parseInt(n), s = this.checked.citys.indexOf(c);
                            t ? -1 === s && this.checked.citys.push(c) : s > -1 && this.checked.citys.splice(s, 1)
                        }
                    }, onCheckedCity: function (e, t) {
                        var i = parseInt(e.target.value);
                        if (e.target.checked) this.checked.citys.push(i); else {
                            var r = this.checked.citys.indexOf(i);
                            r > -1 && this.checked.citys.splice(r, 1)
                        }
                        this.onUpdateProvinceChecked(parseInt(t))
                    }, onUpdateProvinceChecked: function (e) {
                        var t = this.checked.province.indexOf(e), i = t > -1;
                        this.onHasCityChecked(e) ? !i && this.checked.province.push(e) : i && this.checked.province.splice(t, 1)
                    }, onHasCityChecked: function (e) {
                        var t = this.regions[e].city;
                        for (var i in t) if (t.hasOwnProperty(i) && this.inArray(parseInt(i), this.checked.citys)) return !0;
                        return !1
                    }, inArray: function (e, t) {
                        return t.indexOf(e) > -1
                    }, isPropertyExist: function (e, t) {
                        return t.hasOwnProperty(e)
                    }, arrayMerge: function (e, t) {
                        return e.concat(t)
                    }
                }
            });
            s.api.bindevent()
        }, edit: function () {
            new c({
                el: "#app", data: function () {
                    return {
                        freightData: Config.data,
                        valuation: Config.valuation,
                        isdelivery: Config.isdelivery,
                        regions: Config.area,
                        cityCount: 407,
                        checkAll: !1,
                        checked: {province: [], citys: []},
                        disable: {province: [], citys: [], treeData: {}},
                        forms: []
                    }
                }, mounted: function () {
                    this.initializeForms()
                }, methods: {
                    initializeForms: function () {
                        var e = this;
                        if (!this.freightData.length) return !1;
                        this.freightData.forEach(function (t) {
                            for (var i in t.citys) t.citys.hasOwnProperty(i) && (t.citys[i] = parseInt(t.citys[i]));
                            t.treeData = e.getTreeData({province: t.province, citys: t.citys}), e.forms.push(t)
                        })
                    }, onAddRegionEvent: function () {
                        var e = 0;
                        if (this.forms.forEach(function (t) {
                            e += t.citys.length
                        }), console.log(e), e >= this.cityCount) return layer.msg("已经选择了所有区域~"), !1;
                        var t = this;
                        this.onShowCheckBox({
                            complete: function (e) {
                                t.forms.push({province: e.province, citys: e.citys, treeData: t.getTreeData(e)})
                            }
                        })
                    }, onCheckAll: function (e) {
                        this.checkAll = e;
                        for (var t in this.regions) if (this.regions.hasOwnProperty(t)) {
                            var i = this.regions[t];
                            if (!this.isPropertyExist(i.id, this.disable.treeData) || !this.disable.treeData[i.id].isAllCitys) {
                                var r = parseInt(i.id);
                                this.checkedProvince(r, this.checkAll)
                            }
                        }
                    }, onDisableRegion: function (e) {
                        var t = {province: [], citys: []};
                        for (var i in this.forms) if (this.forms.hasOwnProperty(i)) {
                            if (e > -1 && e === parseInt(i)) continue;
                            var r = this.forms[i];
                            t.province = this.arrayMerge(t.province, r.province), t.citys = this.arrayMerge(t.citys, r.citys)
                        }
                        this.disable = {province: t.province, citys: t.citys, treeData: this.getTreeData(t)}
                    }, getTreeData: function (e) {
                        var t = this;
                        console.log(e);
                        var i = {};
                        return e.province.forEach(function (r) {
                            var n = t.regions[r], c = [], s = 0;
                            for (var o in n.city) if (n.city.hasOwnProperty(o)) {
                                var a = n.city[o];
                                t.inArray(a.id, e.citys) && c.push({id: a.id, name: a.name}), s++
                            }
                            i[n.id] = {id: n.id, name: n.name, citys: c, isAllCitys: c.length === s}
                        }), i
                    }, onEditerForm: function (e, t) {
                        var i = this;
                        this.onShowCheckBox({
                            editerFormIndex: e,
                            checkedData: {province: t.province, citys: t.citys},
                            complete: function (e) {
                                t.province = e.province, t.citys = e.citys, t.treeData = i.getTreeData(e)
                            }
                        })
                    }, onDeleteForm: function (e) {
                        var t = this;
                        layer.confirm("确定要删除吗？", {title: "友情提示"}, function (i) {
                            t.forms.splice(e, 1), layer.close(i)
                        })
                    }, onShowCheckBox: function (t) {
                        var i = e.extend(!0, {editerFormIndex: -1, checkedData: null, complete: e.noop()}, t);
                        this.checked = i.checkedData ? i.checkedData : {
                            province: [],
                            citys: []
                        }, this.onDisableRegion(i.editerFormIndex), this.checkAll = !1;
                        var r = this;
                        layer.open({
                            type: 1,
                            shade: !1,
                            moveOut: !0,
                            title: "选择可配送区域",
                            btn: ["确定", "取消"],
                            area: ["820px", "520px"],
                            content: e(this.$refs.choice),
                            yes: function (e) {
                                if (r.checked.citys.length <= 0) return layer.msg("请选择区域~"), !1;
                                i.complete(r.checked), layer.close(e)
                            }
                        })
                    }, onCheckedProvince: function (e) {
                        var t = parseInt(e.target.value);
                        this.checkedProvince(t, e.target.checked)
                    }, checkedProvince: function (e, t) {
                        var i = this.checked.province.indexOf(e);
                        t ? -1 === i && this.checked.province.push(e) : i > -1 && this.checked.province.splice(i, 1);
                        var r = this.regions[e].city;
                        for (var n in r) if (r.hasOwnProperty(n)) {
                            var c = parseInt(n), s = this.checked.citys.indexOf(c);
                            t ? -1 === s && this.checked.citys.push(c) : s > -1 && this.checked.citys.splice(s, 1)
                        }
                    }, onCheckedCity: function (e, t) {
                        var i = parseInt(e.target.value);
                        if (e.target.checked) this.checked.citys.push(i); else {
                            var r = this.checked.citys.indexOf(i);
                            r > -1 && this.checked.citys.splice(r, 1)
                        }
                        this.onUpdateProvinceChecked(parseInt(t))
                    }, onUpdateProvinceChecked: function (e) {
                        var t = this.checked.province.indexOf(e), i = t > -1;
                        this.onHasCityChecked(e) ? !i && this.checked.province.push(e) : i && this.checked.province.splice(t, 1)
                    }, onHasCityChecked: function (e) {
                        var t = this.regions[e].city;
                        for (var i in t) if (t.hasOwnProperty(i) && this.inArray(parseInt(i), this.checked.citys)) return !0;
                        return !1
                    }, inArray: function (e, t) {
                        return t.indexOf(e) > -1
                    }, isPropertyExist: function (e, t) {
                        return t.hasOwnProperty(e)
                    }, arrayMerge: function (e, t) {
                        return e.concat(t)
                    }
                }
            });
            s.api.bindevent()
        }, api: {
            bindevent: function () {
                n.api.bindevent(e("form[role=form]"))
            }
        }
    };
    return s
});