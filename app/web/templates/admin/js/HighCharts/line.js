var chart = new Highcharts.Chart({
	chart: {
		renderTo: "container_line"
	},
	title: {
		text: 'line',
		x: -20 //center
	},
	subtitle: {
		text: " ",
		x: -20
	},
	xAxis: {
		type: 'category',
		labels: {
			rotation: -90,
			style: {
				fontSize: '12px',
				fontFamily: 'Verdana, sans-serif'
			}
		}
	},
	yAxis: {
		min: 0,
		title: {
			text: ''
		},
		plotLines: [{
				value: 0,
				width: 1,
				color: '#808080'
			}]
	},
	tooltip: {
		valueSuffix: ''
	},
	legend: {
		layout: 'horizontal',
		align: 'center',
		verticalAlign: 'bottom',
		borderWidth: 0
	},
	series: [{
			name: 'code1',
			data: []
		}, {
			name: 'code2',
			data: []
		}, {
			name: 'code3',
			data: []
		}]
});

var data1 = [];
var data2 = [];
var data3 = [];
$.getJSON("/admin-HighCharts-getline", function(dict) {
	//console.log(dict);
	for (var key in dict) {
		if (dict.hasOwnProperty(key)) {
			data1.push([key, parseInt(dict[key]['a'])]);
			data2.push([key, parseInt(dict[key]['b'])]);
			data3.push([key, parseInt(dict[key]['c'])]);
		}
	}
	chart.series[0].setData(data1);
	chart.series[1].setData(data2);
	chart.series[2].setData(data3);
});