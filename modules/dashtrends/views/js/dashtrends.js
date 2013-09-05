function line_chart_trends(widget_name, chart_details)
{
	console.log(chart_details.data);
	nv.addGraph(function() {  
		var chart = nv.models.lineChart()
				 .useInteractiveGuideline(true)
				 .x(function(d) { return d[0] })
				 .y(function(d) { return d[1]/100 })
				 .color(d3.scale.category10().range());
	
		chart.xAxis
			.tickFormat(function(d) {
				return d3.time.format('%m/%d/%y')(new Date(d))
			});
	
		chart.yAxis
			.tickFormat(d3.format(',.1%'));
	
		d3.select('#dash_trends_chart1 svg')
			.datum(chart_details.data)
			.call(chart);
	
		//TODO: Figure out a good way to do this automatically
		nv.utils.windowResize(chart.update);
	
		return chart;
	});
}