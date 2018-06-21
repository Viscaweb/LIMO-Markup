/* jshint browser:true, node:true, strict:true, noempty:true, noarg:true, eqeqeq:true, browser:true, bitwise:true, curly:true, undef:true, nonew:true, forin:true */
/* global module:true, define: true, exports: true, require: true, ko:true, moment:true, jquery:true, $:true */
'use strict';

var PLUGIN_NAMESPACE = 'mdl-datepicker';
var pickerHtml =
		[
			'<div class="material-datepicker-container hide">',
			'<div class="material-datepicker" tabindex="0">',
			'<section class="top-date">',
			'<span data-bind="text: month"></span> ',
			'<span data-bind="text: year"></span>',
			'</section>',
			'<section class="middle-date">',
			'<span class="day" data-bind="text: shortDay"></span>, ',
			'<span class="month" data-bind="text: shortMonth"></span> ',
			'<span class="date" data-bind="text: date"></span>',
			'</section>',
			'<section class="calendar no-select">',
			'<div class="mdl-button-group mdl-button-group--block">',
			'<a data-bind="click: prevMonth" class="mdl-button mdl-js-button mdl-button--raised mdl-button--large">',
			'<span class="mdl-button__icon icon-caret-left"></span>',
			'</a>',
			'<div class="mdl-button mdl-button--raised mdl-button--large mdl-button--wide" data-bind="text: viewingMonthName() + \' \' + viewingYear()">',
			'</div>',
			'<a data-bind="click: nextMonth" class="mdl-button mdl-js-button mdl-button--raised mdl-button--large">',
			'<span class="mdl-button__icon icon-caret-right"></span>',
			'</a>',
			'</div>',
			'<div class="headings" data-bind="foreach: getDaysOfWeek()">',
			'<span data-bind="text: $data" class="day heading"></span>',
			'</div>',
			'<div class="days" data-bind="foreach : monthStruct()">',
			'<a data-bind="css:{ selected: $parent.isSelected($data), today: $parent.isToday($data) },text: $data, click: function(data,event){ $parent.chooseDate(data) }" class="day" data-bind="text: $data"></a>',
			'</div>',
			'</section>',
			'</div>',
			'</div>'
		].join('\n');

// setup option values
var defaults = {
	format: "dd D MMM",
	colour: "#009688",
	startDayOfWeek: 0
};

var AppViewModel = function (field, picker, options) {
	var self = this;
	self.daysShort = ['S', 'M', 'T', 'W', 'T', 'F', 'S'];
	self.field = field;
	self.options = $.extend({}, defaults, options);
	self.today = ko.observable(moment(field.val(), field.data('format')));
	self.datePickerValue = ko.observable(self.today());
	self.viewingMonth = ko.observable();
	self.viewingYear = ko.observable();
	self.monthStruct = ko.observableArray();
	self.viewingMonthName = ko.computed(function () {
		var months = [
			'January', 'February', 'March', 'April', 'May',
			'June', 'July', 'August', 'September', 'October',
			'November', 'December'
		];
		return months[self.viewingMonth() - 1];
	});

	self.closePicker = function () {
		picker.addClass('hide');
	};

	self.getDaysOfWeek = function () {
		var newList = [];

		newList = self.daysShort.slice(self.options.startDayOfWeek);
		newList = newList.concat(self.daysShort.slice(0, self.options.startDayOfWeek));

		return newList;
	};

	self.processDate = function (day) {
		if (day) {
			var date = moment(self.viewingYear() + '-' + self.viewingMonth() + '-' + day, 'YYYY-MM-DD');
			self.datePickerValue(date);
			var year = self.viewingYear();
			var month = self.viewingMonth();
			var dateString = self.datePickerValue().format(self.options.format);
			$(self.field).val(dateString).trigger('click.mld-datepicker');
		}
	};

	self.processFullDate = function (fullDate) {
		if (fullDate) {
			var date = moment(fullDate, 'DD-MM-YYYY');
			self.datePickerValue(date);
			self.viewingYear(self.datePickerValue().format('YYYY'));
			self.viewingMonth(parseInt(self.datePickerValue().format('M')));
			var dateString = self.datePickerValue().format(self.options.format);
			$(self.field).val(dateString).trigger('click.mld-datepicker');
		}
	};

	self.chooseDate = function (day) {
		self.processDate(day);
		self.closePicker();
	};

	self.setupViewingDates = function () {
		self.viewingYear(self.datePickerValue().year());
		self.viewingMonth(self.datePickerValue().month() + 1);
	};

	self.buildMonthStruct = function () {
		self.monthStruct.removeAll();
		var month = self.viewingMonth();

		// fix for iOs, is not getting the correct date
		// The month must have 2 positions.
		if(month < 10){
			month = '0' + month;
		}

		var year = self.viewingYear();
		var startOfMonth = moment(year + '-' + month + '-01').startOf('month');

		var startingPoint = startOfMonth.day() - self.options.startDayOfWeek;

		if (startingPoint < 0) {
			startingPoint = self.daysShort.length + startingPoint;
		}

		var daysInMonth = startOfMonth.endOf('month').date();
		var day = 1;
		for (var i = 0; i < 50; i++) {
			if (i < startingPoint) {
				self.monthStruct.push("");
			}
			else {
				if (day <= daysInMonth) {
					self.monthStruct.push(day);
				}
				day++;
			}
		}
	};

	self.fetchDateFromField = function () {
		var dateString = $(field).val();
		self.setupViewingDates();
		self.buildMonthStruct();
	};

	// When Typing in the field
	$(field).keyup(function () {
		self.fetchDateFromField();
	});

	// When field comes into focus
	$(field).focus(function () {
		self.fetchDateFromField();
		self.processDate(self.datePickerValue().date());
	});

	self.nextMonth = function () {
		if (self.viewingMonth() < 12) {
			self.viewingMonth(self.viewingMonth() + 1);
		} else {
			self.viewingMonth(1);
			self.viewingYear(self.viewingYear() + 1);
		}
		self.buildMonthStruct();
	};

	self.prevMonth = function () {
		if (self.viewingMonth() > 1) {
			self.viewingMonth(self.viewingMonth() - 1);
		} else {
			self.viewingMonth(12);
			self.viewingYear(self.viewingYear() - 1);
		}
		self.buildMonthStruct();
	};

	self.isToday = function (day) {
		var thisDay = (day === self.today().date());
		var thisMonth = (self.viewingMonth() === (self.today().month() + 1));
		var thisYear = (self.viewingYear() === self.today().year());
		return thisDay && thisMonth && thisYear;
	};

	self.isSelected = function (day) {
		var rightMonth = (self.viewingMonth() === (self.datePickerValue().month() + 1));
		var rightYear = (self.viewingYear() === self.datePickerValue().year());
		return (day === self.date()) && rightMonth && rightYear;
	};

	self.day = ko.computed(function () {
		return self.datePickerValue().format('dddd');
	});

	self.shortDay = ko.computed(function () {
		return self.datePickerValue().format('ddd');
	});

	self.date = ko.computed(function () {
		return self.datePickerValue().date();
	});

	self.month = ko.computed(function () {
		return self.datePickerValue().format("MMMM");
	});

	self.shortMonth = ko.computed(function () {
		return self.datePickerValue().format("MMM");
	});

	self.year = ko.computed(function () {
		return self.datePickerValue().year();
	});

	/**
	 * Returns the current date of the date picker
	 */
	self.fullDate = ko.computed(function () {
		var day = self.datePickerValue().format("D");
		var month = self.datePickerValue().format("M");
		var year = self.datePickerValue().year();

		return day + "-" + month + "-" + year;
	});

	// init function
	self.init = function () {
		self.fetchDateFromField();
		self.buildMonthStruct();
	};
	self.init();
};

var DatePicker = function (el, options){
	this.el = el;
	this.$el = $(el);
	this.options = options;

	var field = this.el;
	var picker = $(pickerHtml);
	var picker_el = $(".material-datepicker", picker);

	// insert picker after the field in the DOM
	$("body").append(picker);

	// show picker when field is in focus
	$(field).focus(function (e) {
		picker.removeClass('hide');
	});

	//Prevent the picker close clicking the input
	$(field).click(function (e) {
		e.stopPropagation();
		e.preventDefault();
	});

	//Hide datepicker when click outside
	$(document).on("click", function (e) {
		if (!$(e.target).closest(picker_el).length) {
			picker.addClass('hide');
		}
	});

	// setup picker position in relation to the field
	var fieldHeight = $(field).height();
	var offsetTop = $(field).offset().top + fieldHeight + 10;
	var offsetLeft = $(field).offset().left;
	picker.css('top', offsetTop);
	picker.css('left', offsetLeft);

	this.viewModel = new AppViewModel(field, picker, options);

	ko.applyBindings(this.viewModel, picker[0]);
};

DatePicker.prototype.getViewModel = function () {
	return this.viewModel;
};

// jQuery Plugin definition.
$.fn.datepicker = function (options) {
	var args = Array.prototype.slice.call(arguments, 1);

	var $this = $(this);
	var data = $this.data(PLUGIN_NAMESPACE);

	if (!data) {
		$this.data(PLUGIN_NAMESPACE, (data = new DatePicker(this, options)));
	}

	var isCallback = (typeof options === 'string');
	if (isCallback) {
		return data[options].apply(data, args);
	}

	return this;
};
