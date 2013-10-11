/* =========================================================
 * bootstrap-datepicker.js 
 * http://www.eyecon.ro/bootstrap-datepicker
 * =========================================================
 * Copyright 2012 Stefan Petre
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

$(function() {
	var currentMonth = new Date(Date.now());
	var previousMonth = new Date(new Date(currentMonth).setMonth(currentMonth.getMonth()-1));
	$('.datepicker1').datepicker("setValue",previousMonth);
	$('.datepicker2').datepicker("setValue",currentMonth);
	$("#date-start").focus().addClass("input-selected");
});

//click action
!function( $ ) {
	var click, switched, val, start, end, over;
	
	// Picker object
	var Datepicker = function(element, options){
		this.element = $(element);
		this.format = DPGlobal.parseFormat(options.format||this.element.data('date-format')||'mm/dd/yyyy');
		this.picker = $(DPGlobal.template).appendTo(this.element).show()
		.on({
			click: $.proxy(this.click, this),
			mouseover: $.proxy(this.mouseover, this),
			mouseout: $.proxy(this.mouseout, this)
		});
		//click: $.proxy(this.click, this),
		//focus: $.proxy(this.show, this),
		//keyup: $.proxy(this.update, this)

		this.minViewMode = options.minViewMode||this.element.data('date-minviewmode')||0;
		if (typeof this.minViewMode === 'string') {
			switch (this.minViewMode) {
				case 'months':
					this.minViewMode = 1;
					break;
				case 'years':
					this.minViewMode = 2;
					break;
				default:
					this.minViewMode = 0;
					break;
			}
		}
		this.viewMode = options.viewMode||this.element.data('date-viewmode')||0;
		if (typeof this.viewMode === 'string') {
			switch (this.viewMode) {
				case 'months':
					this.viewMode = 1;
					break;
				case 'years':
					this.viewMode = 2;
					break;
				default:
					this.viewMode = 0;
					break;
			}
		}
		this.startViewMode = this.viewMode;
		this.weekStart = options.weekStart||this.element.data('date-weekstart')||0;
		this.weekEnd = this.weekStart === 0 ? 6 : this.weekStart - 1;
		this.onRender = options.onRender;
		this.fillDow();
		this.fillMonths();
		this.update();
		this.showMode();
	};
	
	Datepicker.prototype = {
		constructor: Datepicker,
		
		show: function(e) {
			this.picker.show();

			if (e ) {
				e.stopPropagation();
				e.preventDefault();
			}
			var that = this;
			$(document).on('mousedown', function(ev){
				if ($(ev.target).closest('.datepicker').length === 0) {
					that.hide();
				}
			});
			this.element.trigger({
				type: 'show',
				date: this.date
			});
		},

		set: function() {
			var formated = DPGlobal.formatDate(this.date, this.format);
			this.element.data('date', formated);
		},
		
		setValue: function(newDate) {
			if (typeof newDate === 'string') {
				this.date = DPGlobal.parseDate(newDate, this.format);
			} else {
				this.date = new Date(newDate);
			}
			this.set();
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},
				
		update: function(newDate){
			this.date = DPGlobal.parseDate(
				typeof newDate === 'string' ? newDate : (this.isInput ? this.element.prop('value') : this.element.data('date')),
				this.format
			);
			this.viewDate = new Date(this.date.getFullYear(), this.date.getMonth(), 1, 0, 0, 0, 0);
			this.fill();
		},
		
		fillDow: function(){
			var dowCnt = this.weekStart;
			var html = '<tr>';
			while (dowCnt < this.weekStart + 7) {
				html += '<th class="dow">'+DPGlobal.dates.daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.datepicker-days thead').append(html);
		},
		
		fillMonths: function(){
			var html = '';
			var i = 0;
			while (i < 12) {
				html += '<span class="month">'+DPGlobal.dates.monthsShort[i++]+'</span>';
			}
			this.picker.find('.datepicker-months td').append(html);
		},
		
		fill: function() {
			var d = new Date(this.viewDate),
				year = d.getFullYear(),
				month = d.getMonth(),
				currentDate = this.date.valueOf();
			this.picker.find('.datepicker-days th:eq(1)')
						.text(year+' / '+DPGlobal.dates.months[month]);
			var prevMonth = new Date(year, month-1, 28,0,0,0,0),
				day = DPGlobal.getDaysInMonth(prevMonth.getFullYear(), prevMonth.getMonth());
			prevMonth.setDate(day);
			prevMonth.setDate(day - (prevMonth.getDay() - this.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setDate(nextMonth.getDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName,
				prevY,
				prevM;
			while(prevMonth.valueOf() < nextMonth) {
				if (prevMonth.getDay() === this.weekStart) {
					html.push('<tr>');
				}
				clsName = this.onRender(prevMonth);
				prevY = prevMonth.getFullYear();
				prevM = prevMonth.getMonth();
				if ((prevM < month &&  prevY === year) ||  prevY < year) {
					clsName += ' old';
				} else if ((prevM > month && prevY === year) || prevY > year) {
					clsName += ' new';
				}
				// if (prevMonth.valueOf() === currentDate) {
				// clsName += ' active';
				// }
				html.push('<td class="day '+clsName+'" data-val="'+prevMonth.getTime()+'">'+prevMonth.getDate() + '</td>');
				if (prevMonth.getDay() === this.weekEnd) {
					html.push('</tr>');
				}
				prevMonth.setDate(prevMonth.getDate()+1);
			}
			this.picker.find('.datepicker-days tbody').empty().append(html.join(''));
			var currentYear = this.date.getFullYear();
			
			var months = this.picker.find('.datepicker-months')
						.find('th:eq(1)')
							.text(year)
							.end()
						.find('span').removeClass('active');
			if (currentYear === year) {
				months.eq(this.date.getMonth()).addClass('active');
			}
			
			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.datepicker-years')
								.find('th:eq(1)')
									.text(year + '-' + (year + 9))
									.end()
								.find('td');
			year -= 1;
			for (var i = -1; i < 11; i++) {
				html += '<span class="year'+(i === -1 || i === 10 ? ' old' : '')+(currentYear === year ? ' active' : '')+'">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);

			$("#datepicker .day").each(function(){
				var date_val = $(this).data("val");
				if(end&&start){
					if(date_val > start && date_val < end){
						$(this).not(".old").not(".new").addClass("range");
					}
					if(date_val === start){
						$(this).not(".old").not(".new").addClass("start-selected");
					}
					if(date_val === end){
						$(this).not(".old").not(".new").addClass("end-selected");
					}
				}
			});
		},
		
		click: function(e) {
			e.stopPropagation();
			e.preventDefault();
			var target = $(e.target).closest('span, td, th');
			if (target.length === 1) {
				switch(target[0].nodeName.toLowerCase()) {
					case 'th':
						switch(target[0].className) {
							case 'switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								this.viewDate['set'+DPGlobal.modes[this.viewMode].navFnc].call(
									this.viewDate,
									this.viewDate['get'+DPGlobal.modes[this.viewMode].navFnc].call(this.viewDate) +
									DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1)
								);
								this.fill();
								this.set();
								break;
						}
						break;

					case 'span':
						if (target.is('.month')) {
							var month = target.parent().find('span').index(target);
							this.viewDate.setMonth(month);
						} else {
							var year = parseInt(target.text(), 10)||0;
							this.viewDate.setFullYear(year);
						}
						if (this.viewMode !== 0) {
							this.date = new Date(this.viewDate);
							this.element.trigger({
								type: 'changeDate',
								date: this.date,
								viewMode: DPGlobal.modes[this.viewMode].clsName
							});
						}
						this.showMode(-1);
						this.fill();
						this.set();
						break;

					case 'td':
						//reset
						if (target.is('.day') && !target.is('.disabled') && !target.is('.old') && !target.is('.new')){
							// reset process for a new range
							if(click === 2) {
								start = null;
								end = null;
								click = null;
								switched = false;
								$("td.day").removeClass("start-selected").removeClass("end-selected");
								$(".date-input").removeClass("input-selected").removeClass("input-complete");
								$(".range").removeClass("range");
							}
							//define start with first click or switched one
							if (!click || switched === true) {
								$(".start-selected").removeClass("start-selected");
								target.addClass("start-selected");
								start = target.data("val");
								$("#date-start").val(start);
								!switched ? click = 1 : click = 2;
								if(!switched){
									$("#date-end").val(null).focus().addClass("input-selected");
									target.addClass("start-selected").addClass("end-selected");
								}
								$("#date-start").removeClass("input-selected").addClass("input-complete");
							}
							//define end
							else {
								$(".end-selected").removeClass("end-selected");
								target.addClass("end-selected");
								end = target.data("val");
								$("#date-end").val(end);
								click = 2;
								$("#date-end").removeClass("input-selected").addClass("input-complete");
								//this.range();
							}
						}
						if (target.is('.old')||target.is('.new')){
								var day = parseInt(target.text(), 10)||1;
								var month = this.viewDate.getMonth();
								var year = this.viewDate.getFullYear();
								if (target.is('.old')) {
									month -= 1;
								} else if (target.is('.new')) {
									month += 1;
								}
								this.viewDate = new Date(year, month, Math.min(28, day),0,0,0,0);
								this.fill();
								this.set();
							}
						break;
				}
			}
		},

		range: function(){
			//range
			$("#datepicker .day").each(function(){
				val = parseInt($(this).data('val'),10);
				if (!end){
					if( val > start && val < over){
						$(this).not(".old").not(".new").addClass("range");
					}
				} else if (!start){
					if( val > over && val < end){
						$(this).not(".old").not(".new").addClass("range");
					}
				} else if (start&&end) {
					$(this).addClass("range");
				}
			});
		},
		
		mouseover: function(e){
			//data-val from day overed
			over = $(e.target).data("val");
			
			//this condition will focus start just after mouseover on datepicker, but it's weird...
			//if(click==2){
				//$("#date-start").focus().addClass("input-selected")
			//}

			//action after first click
			if(click === 1 && over){
				$("#datepicker .range").removeClass("range");
				//switch
				if (start && over < start ){
					end = start;
					$("#date-end").val(start).removeClass("input-selected");
					$("#date-start").val(null).focus().addClass("input-selected");
					$("#datepicker .start-selected").removeClass("start-selected").addClass("end-selected");
					start = null;
					switched = true;
				} else if (end && over > end ){
					start = end;
					$("#date-start").val(end).removeClass("input-selected");
					$("#date-end").val(null).focus().addClass("input-selected");
					$("#datepicker .end-selected").removeClass("end-selected").addClass("start-selected");
					end = null;
					switched = false;
				}
				if(start){
					$(".end-selected").removeClass("end-selected");
					$(e.target).addClass("end-selected");
				} else
				if(end){
					$(".start-selected").removeClass("start-selected");
					$(e.target).addClass("start-selected");
				}
				$(".date-input").removeClass("input-complete");
				this.range();
			}
		},

		mouseout: function(){
			if (!start||!end) {
				$("#datepicker .range").removeClass("range");
			}
			if (!end) {
				$(".end-selected").removeClass("end-selected");
			} else
			if (!start) {
				$(".start-selected").removeClass("start-selected");
			}
		},

		mousedown: function(e){
			e.stopPropagation();
			e.preventDefault();
		},
		
		showMode: function(dir) {
			if (dir) {
				this.viewMode = Math.max(this.minViewMode, Math.min(2, this.viewMode + dir));
			}
			this.picker.find('>div').hide().filter('.datepicker-'+DPGlobal.modes[this.viewMode].clsName).show();
		}
	};
	
	$.fn.datepicker = function ( option, val ) {
		return this.each(function () {
			var $this = $(this),
				data = $this.data('datepicker'),
				options = typeof option === 'object' && option;
			if (!data) {
				$this.data('datepicker', (data = new Datepicker(this, $.extend({}, $.fn.datepicker.defaults,options))));
			}
			if (typeof option === 'string') { data[option](val) }
		});
	};

	$.fn.datepicker.defaults = {
		onRender: function() {
			return '';
		}
	};
	$.fn.datepicker.Constructor = Datepicker;
	
	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
		}],
		dates:{
			days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
			months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"]
		},
		isLeapYear: function (year) {
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
		},
		getDaysInMonth: function (year, month) {
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
		},
		parseFormat: function(format){
			var separator = format.match(/[.\/\-\s].*?/),
				parts = format.split(/\W+/);
			if (!separator || !parts || parts.length === 0){
				throw new Error("Invalid date format.");
			}
			return {separator: separator, parts: parts};
		},
		parseDate: function(date, format) {
			var parts = date.split(format.separator),
				date = new Date(),
				val;
			date.setHours(0);
			date.setMinutes(0);
			date.setSeconds(0);
			date.setMilliseconds(0);
			if (parts.length === format.parts.length) {
				var year = date.getFullYear(), day = date.getDate(), month = date.getMonth();
				for (var i=0, cnt = format.parts.length; i < cnt; i++) {
					val = parseInt(parts[i], 10)||1;
					switch(format.parts[i]) {
						case 'dd':
						case 'd':
							day = val;
							date.setDate(val);
							break;
						case 'mm':
						case 'm':
							month = val - 1;
							date.setMonth(val - 1);
							break;
						case 'yy':
							year = 2000 + val;
							date.setFullYear(2000 + val);
							break;
						case 'yyyy':
							year = val;
							date.setFullYear(val);
							break;
					}
				}
				date = new Date(year, month, day, 0 ,0 ,0);
			}
			return date;
		},
		formatDate: function(date, format){
			var val = {
				d: date.getDate(),
				m: date.getMonth() + 1,
				yy: date.getFullYear().toString().substring(2),
				yyyy: date.getFullYear()
			};
			val.dd = (val.d < 10 ? '0' : '') + val.d;
			val.mm = (val.m < 10 ? '0' : '') + val.m;
			var date = [];
			for (var i=0, cnt = format.parts.length; i < cnt; i++) {
				date.push(val[format.parts[i]]);
			}
			return date.join(format.separator);
		},
		headTemplate: '<thead>'+
							'<tr>'+
								'<th class="prev"><i class="icon-angle-left"></i></th>'+
								'<th colspan="5" class="switch"></th>'+
								'<th class="next"><i class="icon-angle-right"</th>'+
							'</tr>'+
						'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>'
	};
	DPGlobal.template = '<div class="datepicker">'+
							'<div class="datepicker-days">'+
								'<table class=" table-condensed">'+
									DPGlobal.headTemplate+
									'<tbody></tbody>'+
								'</table>'+
							'</div>'+
							'<div class="datepicker-months">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
							'<div class="datepicker-years">'+
								'<table class="table-condensed">'+
									DPGlobal.headTemplate+
									DPGlobal.contTemplate+
								'</table>'+
							'</div>'+
						'</div>';

}( window.jQuery );