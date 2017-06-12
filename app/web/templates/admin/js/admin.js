var menu = {
	// 加载页面
	tourl: function() {
		var data = "_r=" + Math.random();
		$.ajax({
			type: "GET",
			url: arguments[0],
			data: data,
			dataType: "json",
			success: function(data) {
				$('.main').html(data);
			},
		});
	},
	// 更新缓存
	upCache: function() {
		var data = "act=" + arguments[0] + "&type=" + arguments[1] + "&_r=" + Math.random();
		var _this = arguments[2];
		$.ajax({
			type: "POST",
			url: "/admin-upcache",
			data: data,
			dataType: "json",
			beforeSend: function() {
				$(_this).val("更新中......");
			},
			success: function(data) {
				if (data) {
					$(_this).val("更新成功");
				}

			},
		});
	},
	// 更新文章页及其当前列表页
	upActicle: function() {
		var data = "id=" + arguments[0] + "&cid=" + arguments[1] + "&_r=" + Math.random();
		var _this = arguments[2];
		$.ajax({
			type: "POST",
			url: "/admin-upacticle",
			data: data,
			dataType: "json",
			beforeSend: function() {
				$(_this).val("更新中......");
			},
			success: function(data) {
				if (data) {
					$(_this).val("更新成功");
				}

			},
		});
	},
};











