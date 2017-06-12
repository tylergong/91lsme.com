Vue.component('my-header', {
    props: ['uName','uTime'],
    template: '<div class="description">欢迎您，<span id="username">{{ uName }}</span>（在线）管理员<span id="date">{{ uTime }}</span></div>' +
    '<div class="topNav">' +
    '<span class="icon index"></span><a href="/" target="_blank">站点首页</a>' +
    '<span class="icon exit"></span><a href="/admin-logout">退出</a>' +
    '</div>',
})