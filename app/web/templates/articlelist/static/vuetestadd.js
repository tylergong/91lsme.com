// 实例vue
var vm = new Vue({
    el: '#article-detail',
    data: {
        details: [],
        cnames: {'1': '感知', '2': '觉醒', '3': '践行', '4': '生活随笔', '5': '关于'},
        a: true
    },
    // 初始化调用
    created: function () {
        this.getDetail(this.getQueryString('id'));
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
        // ajax获取详情页面
        getDetail: function (id) {
            var _this = this
            axios.get('/articlelist-detail', {params: {'id': this.getQueryString('id')}})
                .then(function (response) {
                    _this.details = response.data
                })
                .catch(function (error) {
                    _this.details = [];
                })
        },
        // 提交修改后的数据
        sub: function () {
            console.log(this.details)
            var _this = this
            var _data = new URLSearchParams();
            _data.append('detail', JSON.stringify(_this.details));
            axios.post('/articlelist-save', _data)
                .then(function (response) {
                    if (response) {
                        alert('修改成功')
                    } else {
                        alert('修改失败')
                    }
                    _this.getDetail(_this.getQueryString('id'))
                })
                .catch(function (error) {
                    _this.details = [];
                })
        }
    }
})