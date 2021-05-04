define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'teacher/index' + location.search,
                    add_url: 'teacher/add',
                    edit_url: 'teacher/edit',
                    del_url: 'teacher/del',
                    multi_url: 'teacher/multi',
                    import_url: 'teacher/import',
                    detail_url: 'teacher/detail',
                    table: 'teacher',
                },
            });

            var table = $("#table");
            // 当表格数据加载完成后
            table.on('load-success.bs.table',function (e,data) {
                //这里可以获取从服务端获取的JSON数据
                console.log('load-success.bs.table');
                //这里我们手动设置底部的值
                $("#money").text(data.extend.money);
                $("#price").text(data.extend.price);
            });



            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id', // 排序名
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'ref_date', title: __('Ref_date'), operate:'RANGE',visible:true, addclass:'datetimerange', autocomplete:false},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'number', title: __('Number')},
                        {field: 'price', title: __('Price'), operate:'BETWEEN'},
                        {field: 'status', title: __('Status'), searchList: {"2":__('Status 2'),"1":__('Status 1'),"0":__('Status 0')}, formatter: Table.api.custom_status},
                        {field: 'paid_at', title: __('Paid_at')},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        // {field: 'operate', title: __('Operate'), table: table,buttons:[
                        //     {
                        //         name: 'detail',
                        //         text: '详情',
                        //         title: '详情',
                        //         icon: 'fa fa-list',
                        //         classname: 'btn btn-xs btn-primary btn-dialog',
                        //         url: $.fn.bootstrapTable.defaults.extend.detail_url
                        //     }], formatter: function (value, row, index) {
                        //             var that = $.extend({}, this);
                        //             $(table).data("operate-del", null); // 列表页面隐藏 .编辑operate-edit  - 删除按钮operate-del
                        //             $(table).data("operate-edit", null); // 列表页面隐藏 .编辑operate-edit  - 删除按钮operate-del
                        //             that.table = table;
                        //         return Table.api.formatter.operate.call(that, value, row, index);
                        //     }}
                        {field: 'operate', title: __('Operate'), table: table, buttons:[
                            {
                                name: 'detail',
                                text: '详情',
                                title: '详情',
                                icon: 'fa fa-list',
                                classname: 'btn btn-xs btn-primary btn-dialog',
                                url: $.fn.bootstrapTable.defaults.extend.detail_url
                            }],events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ],

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
        detail: function () {
            $(document).on('click', '.btn-callback', function () {
                Fast.api.close($("input[name=callback]").val());
            });
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            custom_status:function ($value) {
                console.log($value)
            }
        }
    };
    return Controller;
});
