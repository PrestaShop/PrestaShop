var dashtrends_data;
var dashtrends_chart;

function line_chart_trends(widget_name, chart_details)
{
	nv.addGraph(function() {  
		var chart = nv.models.lineChart()
			.useInteractiveGuideline(true)
			.x(function(d) { return (d !== undefined ? d[0] : 0); })
			.y(function(d) { return (d !== undefined ? d[1] : 0); })
	
		chart.xAxis.tickFormat(function(d) {
			return d3.time.format('%m/%d/%y')(new Date(d))
		});
	
		chart.yAxis.tickFormat(d3.format('%'));

		dashtrends_data = chart_details.data;
		dashtrends_chart = chart;

		d3.select('#dash_trends_chart1 svg')
			.datum(chart_details.data)
			.call(chart);
		nv.utils.windowResize(chart.update);

		return chart;
	});
}

function selectDashtrendsChart(element, type)
{
	$('#dashtrends_toolbar dl').removeClass('active');
	$(element).addClass('active');

	$.each(dashtrends_data, function(index, value) {
		if (value.id == type || value.id == type + '_compare')
			value.disabled = false;
		else
			value.disabled = true;
	});

	d3.select('#dash_trends_chart1 svg')
		.datum(dashtrends_data)
		.call(dashtrends_chart);
}