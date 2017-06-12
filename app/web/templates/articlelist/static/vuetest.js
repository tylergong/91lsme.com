// 定义文章类型
var cname = {'1': '感知', '2': '觉醒', '3': '践行', '4': '生活随笔', '5': '关于'};
// 实例vue
new Vue({
    el: '#article-list',
    data () {
        return {
            lists: '',                  //列表数据
            cklists: [],                // 选中列表
            all: 0,                     //总页数
            cur: 1,                     //当前页码
            selectedAllState: false,    //是否全选
            isshow: false,
        }
    },
    // 初始化调用
    created: function () {
        this.getList(this.cur)
    },
    filters: {
        // 过滤文章类型
        cname: function (cid) {
            return cname[cid];
        }
    },
    methods: {
        getQueryString: function (name) {
            var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)", "i");
            var r = window.location.search.substr(1).match(reg);
            if (r != null) {
                return unescape(r[2])
            }
            return null
        },
        // ajax获取数据
        getList: function (cur) {
            var _this = this
            axios.get('/articlelist-show', {params: {'cid': this.getQueryString('cid'), 'p': cur}})
                .then(function (response) {
                    _this.lists = response.data.list
                    _this.all = response.data.all
                    _this.cur = response.data.cur
                    _this.cklists = [];
                    _this.selectedAllState = false;
                })
                .catch(function (error) {
                    _this.lists = [];
                })
        },
        // 跳转地址
        tourl: function (id) {
            window.location.href = '/articlelist/vuetestadd.shtml?id=' + id;
        },
        // 点击第几页
        btnClick: function (cur) {
            this.getList(cur)
        },
        // 点击上一页、下一页
        pageClick: function () {
            this.getList(this.cur)
        },
        // 勾选
        selecedPro: function (item) {
            var _this = this
            _this.cklists = [];
            _this.lists.forEach(function (item, index) {
                if (item.proCheck) {
                    _this.cklists.push(item.id)
                }
            });
            if (_this.cklists.length == _this.lists.length) {
                _this.selectedAllState = true;
            } else {
                _this.selectedAllState = false;
            }
        },
        // 全选
        selectedAll: function () {
            var _this = this
            _this.cklists = [];
            _this.selectedAllState = !_this.selectedAllState;
            _this.lists.forEach(function (item, index) {
                item.proCheck = _this.selectedAllState;
                _this.cklists.push(item.id)
            });
            if (_this.selectedAllState == false) {
                _this.cklists = []
            }
        },
        // 删除数据并刷新数据
        del: function () {
            var _this = this
            if (_this.cklists.length > 0) {
                axios.get('/articlelist-del', {params: {'ids': JSON.stringify(_this.cklists), 'p': _this.cur}})
                    .then(function (response) {
                        if (_this.selectedAllState == true && _this.cur == _this.all) {
                            _this.cur = parseInt(_this.cur) - parseInt(1);
                        }
                        _this.cklists = []
                        _this.getList(_this.cur)
                    })
                    .catch(function (error) {
                    })
            }
        },
        // 勾选删除 or 批量删除
        delMulti: function () {
            // 弹层确认
            this.isshow = true
        },
        // 单行删除
        delOnce: function (item) {
            // 弹层确认
            this.isshow = true
            this.cklists = []
            this.cklists.push(item.id)
        },
        // 关闭弹窗
        closePop: function (bool) {
            if (!bool) {
                this.isshow = false
            } else {
                this.del()
                this.isshow = false
            }
        }
    },
    computed: {
        // 是否全选文案变化
        selectedAllText: function () {
            return this.selectedAllState ? "取消全选" : "全选"
        },
        // 翻页页码
        indexs: function () {
            var left = 1;
            var right = this.all;
            var ar = [];
            if (this.all >= 5) {
                if (this.cur > 3 && this.cur < this.all - 2) {
                    left = parseInt(this.cur) - parseInt(2)
                    right = parseInt(this.cur) + parseInt(2)
                } else {
                    if (this.cur <= 3) {
                        left = 1
                        right = 5
                    } else {
                        right = this.all
                        left = parseInt(this.all) - parseInt(4)
                    }
                }
            }
            while (left <= right) {
                ar.push(left)
                left++
            }
            return ar
        }
    }
})