$('#loginBtn').click(function() {
	var uname = $('#username');
	var upwd = $('#password');
	if (empty_c(uname.val())) {
		uname.focus();
		$('.warn').html('请输入用户名');
		return;
	}
	if (empty_c(upwd.val())) {
		upwd.focus();
		$('.warn').html('请输入密码');
		return;
	}

	var data = "uname=" + uname.val() + "&upwd=" + upwd.val() + "&_r=" + Math.random();
	$.ajax({
		type: "POST",
		url: "/admin-loginsub",
		data: data,
		dataType: "json",
		success: function(data) {
			if (data.error == 0) {
				window.location.href = '/admin';
			} else {
				$('.warn').html(data.message);
				return;
			}
		}
	})
})
function empty_c(str) {
	return (str == "" || str == null || str == undefined || str == 0) ? true : false
}