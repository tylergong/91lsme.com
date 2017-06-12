var chart = new Highcharts.Chart({
	chart: {
		renderTo: "container_pie",
		plotBackgroundColor: null,
		plotBorderWidth: null,
		plotShadow: false,
//		options3d: {
//			enabled: true,
//			alpha: 45,
//			beta: 0
//		}
	},
	title: {
		text: '2D pie'
	},
	subtitle: {
		text: ' '
	},
	tooltip: {
		pointFormat: '{series.name}: <b>{point.y:.0f}%</b>'
	},
	plotOptions: {
		pie: {
			allowPointSelect: true,
			cursor: 'pointer',
			depth: 35,
//			innerSize: 100,
			dataLabels: {
				enabled: true,
				format: '<b>{point.name}</b>: {point.y:.0f}%',
				style: {
					color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
				}
			}
		}
	},
	series: [{
			type: 'pie',
			name: '市场占有 ',
			data: []
		}]
});


//$.ajaxSettings.async = false;
var data = [];
$.getJSON("/admin-HighCharts-getpie", function(dict) {
	//console.log(dict);
	for (var key in dict) {
		if (dict.hasOwnProperty(key)) {
			data.push([key, parseInt(dict[key])]);
		}
	}
	chart.series[0].setData(data);
});

