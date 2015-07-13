// javascript variables available on all Admin pages
var currency;
var full_language_code;

describe("CLDR api", function() {
	
	beforeEach(function() {
		
		currency = { 'iso_code': 'EUR' };
		full_language_code = 'en-us';
		
		cldrLoadedCatalogs = []; // reset registry of loaded catalogs
		cldrLoaderError = false;
		
		// simulate ajax response when downloading a catalog
		var fakeCatalogGetter = function (url) {
			switch(url) {
				case cldrCatalogsPath+'main/en-US/numbers.json':
					return fakeEnNumbersCatalog;
				case cldrCatalogsPath+'supplemental/likelySubtags.json':
					return fakeSupplementalLikelySubtagsCatalog;
				case cldrCatalogsPath+'supplemental/numberingSystems.json':
					return fakeSupplementalNumberingSystemsCatalog;
				default:
					throw Error(url+" was not mocked for tests!");
			}
		};
		spyOn($, 'ajax').and.callFake(function(options) { options.success(fakeCatalogGetter(options.url)) });
	});

	it("cannot load an unknown/void catalog", function() {
		var unknownCatalog = function() {
			cldrLazyLoadCatalogs(['i/do/not/know/you']);
		};
		expect(unknownCatalog).toThrowError(Error, /not mocked for tests!/);
		
		var undefinedCatalog = function() {
			cldrLazyLoadCatalogs();
		};
		expect(undefinedCatalog).toThrowError(Error);
		
		var voidCatalog = function() {
			cldrLazyLoadCatalogs([]);
		};
		expect(voidCatalog).toThrowError(Error);
	});

	
	it("can load an existing catalog synchronously", function() {
		cldrLazyLoadCatalogs(['supplemental/likelySubtags']);
		
		expect(cldrLoadedCatalogs).toContain(cldrCatalogsPath+'supplemental/likelySubtags.json');
	});
	
	
	// FIXME: how to test async behavior, since we have to mock $.get object...
	xit("can load an existing catalog asynchronously", function() {
		var callback = jasmine.createSpy();
		
		cldrLazyLoadCatalogs(['supplemental/likelySubtags'], callback);
		
		expect(cldrLoadedCatalogs).toContain(cldrCatalogsPath+'supplemental/likelySubtags.json');
		expect(callback).toHaveBeenCalled();
	});
	
	
	it("can load catalogs for Numbers features, and can make simple formatter/parser calls", function() {
		var globalize = cldrForNumber();
		var t1 = globalize.numberFormatter({
				minimumSignificantDigits: 1,
				maximumSignificantDigits: 3
			})(3.141592);
		var t2 = globalize.numberParser()("$57.67");
		
		expect(t1).toEqual('3.14');
		expect(t2).toEqual(57.67);
	});

});


// TODO : refacto les tests ci-dessous par du jasmine :)

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
