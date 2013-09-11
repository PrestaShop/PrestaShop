function pie_chart_trends(widget_name, chart_details)
{
	nv.addGraph(function() {
		var chart = nv.models.pieChart()
			.x(function(d) { return d.key })
			.y(function(d) { return d.y })
			.color(d3.scale.category10().range())
			.donut(true)
			.showLabels(false)
			.showLegend(false);
		  d3.select("#dash_traffic_chart2 svg")
			.datum(chart_details.data)
			.transition().duration(1200)
			.call(chart);
		nv.utils.windowResize(chart.update);
		return chart;
	});	
}