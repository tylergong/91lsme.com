var chart = new Highcharts.Chart({
	chart: {
		type: 'column',
		renderTo: "container_column"
	},
	title: {
		text: 'column'
	},
	subtitle: {
		text: ''
	},
	xAxis: {
		type: 'category',
		labels: {
			//rotation: -45,
			style: {
				fontSize: '13px',
				fontFamily: 'Verdana, sans-serif'
			}
		}
	},
	yAxis: {
		min: 0,
		title: {
			text: '出现频次'
		}
	},
	legend: {
		enabled: false
	},
	tooltip: {
		pointFormat: '出现频次<b>{point.y}</b>'
	},
	plotOptions: {
		series: {
			borderWidth: 0,
			dataLabels: {
				enabled: true,
				format: '{point.y}'
			}
		}
	},
	series: [{
			type: 'column',
			name: 'Population',
			//colorByPoint: true,
			data: [],
			dataLabels: {
				enabled: true,
				//rotation: -90,
				color: '#FFFFFF',
				align: 'right',
				x: -2,
				y: 18,
				style: {
					fontSize: '13px',
					fontFamily: 'Verdana, sans-serif',
					textShadow: '0 0 3px black'
				}
			}
		}],
});


var data = [];
$.getJSON("/admin-HighCharts-getcolumn", function(dict) {
	//console.log(dict);
	for (var key in dict) {
		if (dict.hasOwnProperty(key)) {
			data.push([key, parseInt(dict[key])]);
		}
	}
	chart.series[0].setData(data);
});
