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

//TODO unify this with other function in calenda.js and replace date.js functions
Date.parseDate = function(date, format) {
	if (format === undefined)
		format = 'Y-m-d';

	var formatSeparator = format.match(/[.\/\-\s].*?/);
	var formatParts     = format.split(/\W+/);
	var parts           = date.split(formatSeparator);
	var date            = new Date();

	if (parts.length === formatParts.length) {
		date.setHours(0);
		date.setMinutes(0);
		date.setSeconds(0);
		date.setMilliseconds(0);

		for (var i=0; i<=formatParts.length; i++) {
			switch(formatParts[i]) {
				case 'dd':
				case 'd':
				case 'j':
				date.setDate(parseInt(parts[i], 10)||1);
				break;

				case 'mm':
				case 'm':
				date.setMonth((parseInt(parts[i], 10)||1) - 1);
				break;

				case 'yy':
				case 'y':
				date.setFullYear(2000 + (parseInt(parts[i], 10)||1));
				break;

				case 'yyyy':
				case 'Y':
				date.setFullYear(parseInt(parts[i], 10)||1);
				break;
			}
		}
	}

	return date;
};

Date.prototype.format = function(format) {
	if (format === undefined)
		return this.toString();

	var formatSeparator = format.match(/[.\/\-\s].*?/);
	var formatParts     = format.split(/\W+/);
	var result          = '';

	for (var i=0; i<=formatParts.length; i++) {
		switch(formatParts[i]) {
			case 'd':
			case 'j':
			result += this.getDate() + formatSeparator;
			break;

			case 'dd':
			result += (this.getDate() < 10 ? '0' : '')+this.getDate() + formatSeparator;
			break;

			case 'm':
			result += (this.getMonth() + 1) + formatSeparator;
			break;

			case 'mm':
			result += (this.getMonth() < 9 ? '0' : '')+(this.getMonth() + 1) + formatSeparator;
			break;

			case 'yy':
			case 'y':
			result += this.getFullYear() + formatSeparator;
			break;

			case 'yyyy':
			case 'Y':
			result += this.getFullYear() + formatSeparator;
			break;
		}
	}

	return result.slice(0, -1);
}

$(document).ready(function() {
	if (typeof date_subtitle === "undefined")
		var date_subtitle = '(from %s to %s)';

	if (typeof date_format === "undefined")
		var date_format = 'Y-mm-dd';

	$('#date-start').change(function() {
		start = Date.parseDate($('#date-start').val(), 'Y-m-d');
		end = Date.parseDate($('#date-end').val(), 'Y-m-d');
		$('#customers-newsletters-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
		$('#traffic-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
	});

	$('#date-end').change(function() {
		start = Date.parseDate($('#date-start').val(), 'Y-m-d');
		end = Date.parseDate($('#date-end').val(), 'Y-m-d');
		
		$('#customers-newsletters-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
		$('#traffic-subtitle').html(sprintf(date_subtitle, start.format(date_format), end.format(date_format)));
	});
});