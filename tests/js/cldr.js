//////////////////
// UNIT TESTING //
//////////////////

function testCurrenciesSync() {
	var glCurrencyFormatter = cldrForCurrencyFormatterWrapper();
	console.log(glCurrencyFormatter(59.99));
}
function testCurrenciesAsync() {
	cldrForCurrencyFormatterWrapper(function(formatter) {
		console.log(formatter(39.991268));
	}, {style: "name"});
}

function testNumbers() {
	var glNumb = cldrForNumber();
	console.log(glNumb.numberFormatter({
			minimumSignificantDigits: 1,
			maximumSignificantDigits: 3
		})(3.141592));
	console.log(glNumb.numberParser()("$57.67"));
}

function testDateSync() {
	console.log(cldrForDate().formatDate( new Date(), { datetime: "medium" }));
}
function testDateAsync() {
	cldrForDate(function(gl) {
		console.log(gl.formatDate( new Date(), { datetime: "medium" }));
	});
}

//////////////////////////
// NON REGRESSION TESTS //
//////////////////////////

function testFormatNumber() {
	console.log(formatNumber(1234.09876543, 4, '_', ';'));
	formatNumberCldr(1234.09876543, function(v) {
		console.log(v);
	}, 4);
	
	console.log(formatNumber(1234, 4, '_', ';'));
	formatNumberCldr(1234, function(v) {
		console.log(v);
	}, 4);
	
	console.log(formatNumber(1234.00000000001, 4, '_', ';'));
	formatNumberCldr(1234.00000000001, function(v) {
		console.log(v);
	}, 4);
	
	formatNumberCldr(123456.987654, function(v) {
		console.log(v);
	});
}

function testFormatCurrency() {
	priceDisplayPrecision = 2; // global should be already defined
	console.log(formatCurrency(12344.12345657, 1, '$', 0));
	formatCurrencyCldr(12344.12345657, function(v) {
		console.log(v);
	});
	
	priceDisplayPrecision = 6; // global should be already defined
	console.log(formatCurrency(22344.12345657, 1, '$', 0));
	formatCurrencyCldr(22344.12345657, function(v) {
		console.log(v);
	});
	
	priceDisplayPrecision = 2; // global should be already defined
	console.log(formatCurrency(-.5698, 1, '$', 0));
	formatCurrencyCldr(-.5698, function(v) {
		console.log(v);
	});
}
