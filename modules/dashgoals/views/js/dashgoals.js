function line_chart_goals(widget_name, chart_details)
{
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

		d3.select('#dash_goals_chart1 svg')
			.datum(chart_details.data)
			.call(chart);
		nv.utils.windowResize(chart.update);

		return chart;
	});
}

$(document).ready(function() {
	$('.dashgoals_config_input').keyup(function() {
		$('.dashgoals_sales').each(function() {
			var key = $(this).attr('id').substr(16);
			var sales = parseFloat($('#dashgoals_traffic_' + key).val()) * parseFloat($('#dashgoals_average_cart_value_' + key).val()) * parseFloat($('#dashgoals_conversion_rate_' + key).val()) / 100;
			if (isNaN(sales))
				$(this).text('0');
			else
				$(this).text(parseInt(sales));
		});
	});
});