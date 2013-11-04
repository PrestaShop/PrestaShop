function bar_chart_goals(widget_name, chart_details)
{
	nv.addGraph(function() {
		var chart = nv.models.multiBarChart();

		chart.yAxis.tickFormat(d3.format('%'));

		d3.select('#dash_goals_chart1 svg')
			.datum(chart_details.data)
			.transition()
			.call(chart);

		nv.utils.windowResize(chart.update);

		return chart;
	});
}

function dashgoals_calc_sales()
{
	$('.dashgoals_sales').each(function() {
		var key = $(this).attr('id').substr(16);
		var sales = parseFloat($('#dashgoals_traffic_' + key).val()) * parseFloat($('#dashgoals_avg_cart_value_' + key).val()) * parseFloat($('#dashgoals_conversion_' + key).val()) / 100;
		if (isNaN(sales))
			$(this).text(formatCurrency(0, currency_format, currency_sign, currency_blank));
		else
			$(this).text(formatCurrency(parseInt(sales), currency_format, currency_sign, currency_blank));
	});
}

function dashgoals_changeYear(xward)
{
	var new_year = dashgoals_year;
	if (xward == 'forward')
		new_year = dashgoals_year + 1;
	else if (xward == 'backward')
		new_year = dashgoals_year - 1;
	$('#dashgoals_title').text($('#dashgoals_title').text().replace(dashgoals_year, new_year));
	dashgoals_year = new_year;
	refreshDashboard('dashgoals', false, dashgoals_year);
}

function dashgoals_changeConfYear(xward)
{
	// Todo
	// getModuleLink('dashgoals', 'ajax', array('year' => $('#dashgoals_conftitle').text()), $ssl = null, $id_lang = null, $id_shop = null)
	if (xward == 'forward')
		$('#dashgoals_conftitle').text(parseInt($('#dashgoals_conftitle').text()) + 1);
	else if (xward == 'backward')
		$('#dashgoals_conftitle').text(parseInt($('#dashgoals_conftitle').text()) - 1);
}

$(document).ready(function() {
	$('.dashgoals_config_input').keyup(function() {
		dashgoals_calc_sales();
	});
	dashgoals_calc_sales();
});
