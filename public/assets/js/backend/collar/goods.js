define(['jquery', 'bootstrap', 'backend', 'table', 'form', "vue"], function ($, undefined, Backend, Table, Form,Vue) {
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
            var vm = new Vue({
                el: '#app',
                data() {
                    return {
                        spu: [],
                        spuItem: [],
                        sku: [],
                        batch: 0,
                        attributeData: []
                    }
                },methods: {
                    // 添加属性
                    spuAdd(){
                        var str = this.$refs['specs-name'].value || ''
                        str = str.trim();
                        if (!str){
                            Toastr.error("商品属性不能为空");
                            return
                        }
                        // 遍历
                        var arr = str.split(/\s+/);
                        for (var i=0;i<arr.length;i++)
                        {
                            this.spu.push(arr[i])
                        }
                        // 清空表单
                        this.$refs['specs-name'].value = ''
                    },
                    // 删除属性
                    spuRemove(key){
                        Vue.delete(vm.spuItem, key);
                        Vue.delete(vm.spu, key);
                        this.skuCreate();
                    },
                    // 添加规格
                    skuAdd(index) {
                        var str = this.$refs['specs-name-' + index][0].value || ''
                        str = str.trim();
                        if (!str){
                            Toastr.error("商品属性不能为空!")
                            return
                        }
                        // 遍历
                        var arr = str.split(/\s+/);
                        for (var i=0;i<arr.length;i++)
                        {
                            if (this.spuItem[index]) {
                                this.spuItem[index].push(arr[i])
                            } else {
                                this.spuItem.push([arr[i]])
                            }
                        }
                        // 清空表单
                        this.$refs['specs-name-' + index][0].value = ""
                        this.skuCreate();
                    },
                    // 删除规格
                    skuRemove(i,key){
                        Vue.delete(vm.spuItem[i], key);
                        this.skuCreate();
                    },
                    // 生成Sku
                    skuCreate() {
                        this.sku = this.skuDesign(this.spuItem)
                    },
                    skuDesign(array) {
                        if (array.length == 0) return []
                        if (array.length < 2) {
                            var res = []
                            array[0].forEach(function(v) {
                                res.push([v])
                            })
                            return res
                        }
                        return [].reduce.call(array, function(col, set) {
                            var res = [];
                            col.forEach(function(c) {
                                set.forEach(function(s) {
                                    var t = [].concat(Array.isArray(c) ? c : [c]);
                                    t.push(s);
                                    res.push(t);
                                })
                            });
                            return res;
                        });
                    },
                    // 是否开启批量
                    skuBatch(){
                        this.batch = this.batch == 0 ? 1 : 0;
                    }
                }
            })
            $("input:radio[name='row[hasoption]']").on("click",function () {
                    var as = $(this).val();
                    if(as==1){
                        $("#app").show();
                    }else{
                        $("#app").hide();
                    }
            })
            window.batchSet = function(field) {
                $('.wanl-' + field).val($('#batch-' + field).val())
            }
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
            var vm = new Vue({
                el: '#app',
                data() {
                    return {
                        spu: Config.spu,
                        spuItem: Config.spuItem,
                        sku: Config.sku,
                        skuItem: Config.skuItem,
                        attribute: Config.attribute,
                        attributeData: [],
                        batch: 0
                    }
                },
                methods: {
                    // 添加属性
                    spuAdd(){
                        var str = this.$refs['specs-name'].value || ''
                        str = str.trim();
                        if (!str){
                            Toastr.error("商品属性不能为空!");
                            return
                        }
                        // 遍历
                        var arr = str.split(/\s+/);
                        for (var i=0;i<arr.length;i++)
                        {
                            this.spu.push(arr[i])
                        }
                        // 清空表单
                        this.$refs['specs-name'].value = ''
                    },
                    // 添加规格
                    skuAdd(index) {
                        var str = this.$refs['specs-name-' + index][0].value || ''
                        str = str.trim();
                        if (!str){
                            Toastr.error("商品属性不能为空!")
                            return
                        }
                        // 遍历
                        var arr = str.split(/\s+/);
                        for (var i=0;i<arr.length;i++)
                        {
                            if (this.spuItem[index]) {
                                this.spuItem[index].push(arr[i])
                            } else {
                                this.spuItem.push([arr[i]])
                            }
                        }
                        // 清空表单
                        this.$refs['specs-name-' + index][0].value = ""
                        this.skuCreate();
                    },
                    // 删除属性
                    spuRemove(key){
                        Vue.delete(vm.spuItem, key);
                        Vue.delete(vm.spu, key);
                        this.skuCreate();
                    },
                    // 删除规格
                    skuRemove(i,key){
                        Vue.delete(vm.spuItem[i], key);
                        this.skuCreate();
                    },
                    // 生成Sku
                    skuCreate() {
                        this.sku = this.skuDesign(this.spuItem)
                    },
                    skuDesign(array) {
                        if (array.length == 0) return []
                        if (array.length < 2) {
                            var res = []
                            array[0].forEach(function(v) {
                                res.push([v])
                            })
                            return res
                        }
                        return [].reduce.call(array, function(col, set) {
                            var res = [];
                            col.forEach(function(c) {
                                set.forEach(function(s) {
                                    var t = [].concat(Array.isArray(c) ? c : [c]);
                                    t.push(s);
                                    res.push(t);
                                })
                            });
                            return res;
                        });
                    },
                    // 是否开启批量
                    skuBatch(){
                        this.batch = this.batch == 0 ? 1 : 0;
                    }
                }
            })
            window.batchSet = function(field) {
                $('.wanl-' + field).val($('#batch-' + field).val())
            }
            /*编辑隐藏规格*/
            $("input:radio[name='row[hasoption]']").on("click",function () {
                var as = $(this).val();
                if(as==1){
                    $("#app").show();
                }else{
                    $("#app").hide();
                }
            })
            /*隐藏是否编辑多规格*/
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});