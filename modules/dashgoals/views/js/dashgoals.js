function bar_chart_goals(widget_name, chart_details)
{
	nv.addGraph(function() {
		var chart = nv.models.multiBarChart()
			.stacked(true)
			.showControls(false)
			.tooltipContent(function(key, y, e, graph) {
				var perf = parseInt(e) - 100;

				return '/modules/dashgoals/views/js/dashgoals.js : Deprecated, now we need to retrieve the content with ajax';

				if (perf > 0)
					return '<section class="panel"><header class="panel-heading">' + key + '</header><span class="dash_trend dash_trend_up">+' + perf + '%</span></section>';
				else if (perf < 0)
					return '<section class="panel"><header class="panel-heading">' + key + '</header><span class="dash_trend dash_trend_down">' + perf + '%</span></section>';
				else
					return '<section class="panel"><header class="panel-heading">' + key + '</header><span class="dash_trend dash_trend_right">-</span></section>';
			});

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

	$.ajax({
		url: dashgoals_ajax_link,
		data: {
			ajax: true,
			action: 'changeconfyear',
			year: new_year
		},
		success : function(result){
			$('#dashgoals_title').text($('#dashgoals_title').text().replace(dashgoals_year, new_year));
			var hide_conf = $('#dashgoals_config').hasClass('hide');
			$('#dashgoals_config').replaceWith(result);
			dashgoals_calc_sales();
			if (!hide_conf)
				$('#dashgoals_config').removeClass('hide');
			$('.dashgoals_config_input').off();
			$('.dashgoals_config_input').keyup(function() { dashgoals_calc_sales(); });
			dashgoals_year = new_year;
			refreshDashboard('dashgoals', false, dashgoals_year);
		}
	});
}

$(document).ready(function() {
	$('.dashgoals_config_input').keyup(function() { dashgoals_calc_sales(); });
	dashgoals_calc_sales();
});
