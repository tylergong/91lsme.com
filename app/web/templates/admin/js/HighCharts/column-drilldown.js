var colors = Highcharts.getOptions().colors;
var name = 'Browser brands';

function setChart(name, categories, data, color) {
	chart.xAxis[0].setCategories(categories, false);
	chart.series[0].remove(false);
	chart.addSeries({
		name: name,
		data: data,
		color: color || 'white'
	}, false);
	chart.redraw();
}

//var chart = $('#container_column3').highcharts({
var chart = new Highcharts.Chart({
	chart: {
		type: 'column',
		renderTo: "container_column-drilldown"
	},
	title: {
		text: 'Browser market share, April, 2011'
	},
	subtitle: {
		text: 'Click the columns to view versions. Click again to view brands.'
	},
	xAxis: {
		categories: []
	},
	yAxis: {
		title: {
			text: 'Total percent market share'
		}
	},
	plotOptions: {
		column: {
			cursor: 'pointer',
			point: {
				events: {
					click: function() {
						var drilldown = this.drilldown;
						if (drilldown) { // drill down
							setChart(drilldown.name, drilldown.categories, drilldown.data, drilldown.color);
						} else { // restore
							setChart(name, categories, brandsData);
						}
					}
				}
			},
			dataLabels: {
				enabled: true,
				color: 'red',
				align: 'center',  
				style: {
					fontWeight: 'bold'
				},
				formatter: function() {
					return this.y + '%';
				}
			}
		}
	},
	tooltip: {
		formatter: function() {
			var point = this.point,
					s = this.x + ': <b>' + this.y + '%  market share</b><br>';
			if (point.drilldown) {
				s += 'Click to view ' + point.category + ' versions';
			} else {
				s += 'Click to return to browser brands';
			}
			return s;
		}
	},
	series: [{
			name: 'Browser brands',
			data: [],
			color: 'white'
		}],
	exporting: {
		enabled: true
	}
})


var brands = {};
var versions = {};
var versionsD = {};
var categories = [];
var brandsData = [];
$.getJSON("/admin-HighCharts-getbrowser", function(dict) {
	//console.log(dict);
	for (var key in dict) {
		categories.push(key);
		//console.log(categories);
		for (var key2 in dict[key]) {
			if (!brands[key]) {
				brands[key] = parseFloat(dict[key][key2]);
			} else {
				brands[key] += parseFloat(dict[key][key2]);
			}

			if (!versions[key]) {
				versions[key] = [];
			}
			versions[key].push(key2);

			if (!versionsD[key]) {
				versionsD[key] = [];
			}
			versionsD[key].push(parseFloat(dict[key][key2]));
		}
	}
	//console.log(brands);
	//console.log(versions);
	var i = 0;
	$.each(brands, function(key, item) {
		brandsData.push({
			name: key,
			y: item,
			color: colors[i],
			drilldown: {
				name: key + ' versions',
				color: colors[i],
				categories: versions[key],
				data: versionsD[key],
			}
		});
		i++;
	});
	//console.log(brandsData);
	chart.series[0].setData(brandsData);
	chart.xAxis[0].setCategories(categories);
});