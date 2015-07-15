// jshint undef:false
// javascript variables available on all Admin pages
var currency;
var full_language_code;
var priceDisplayPrecision;

describe("CLDR api", function() {
	
	beforeEach(function() {
		
		currency = { 'iso_code': 'EUR' };
		full_language_code = 'en-us';
		priceDisplayPrecision = 2;
		
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
				case cldrCatalogsPath+'main/en-US/ca-gregorian.json':
					return fakeEnCalendarCatalog;
				case cldrCatalogsPath+'main/en-US/currencies.json':
					return fakeEnCurrenciesCatalog;
				case cldrCatalogsPath+'main/en-US/timeZoneNames.json':
					return fakeEnTimeZoneNamesCatalog;
				case cldrCatalogsPath+'supplemental/currencyData.json':
					return fakeSupplementalCurrencyDataCatalog;
				case cldrCatalogsPath+'supplemental/timeData.json':
					return fakeSupplementalTimeDataCatalog;
				case cldrCatalogsPath+'supplemental/plurals.json':
					return fakeSupplementalPluralsCatalog;
				case cldrCatalogsPath+'supplemental/weekData.json':
					return fakeSupplementalWeekDataCatalog;
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
	
	
	// FIXME: how to test async behavior, since we have to mock $.get function correctly...
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

	
	it("can load catalogs for Date features, and can make simple formatter calls", function() {
		var globalize = cldrForDate();
		var t1 = globalize.dateFormatter({ datetime: "medium" })(new Date(2011, 11, 31, 22, 45, 59));
		
		expect(t1).toEqual('Dec 31, 2011, 10:45:59 PM');
	});
	
	
	it("can load catalogs for Currency features, and can make simple formatter calls with manual currency set (to EUR)", function() {
		var currencyIsoCode = 'EUR';
		var globalize = cldrForCurrencies();
		var t1 = globalize.currencyFormatter(currencyIsoCode)(59.99);
		
		expect(t1).toEqual('€59.99');
	});
	
	
	it("can load catalogs for Currency features through currency wrapper method, and can make simple formatter calls with auto currency set", function() {
		var formatter = cldrForCurrencyFormatterWrapper(null, {style: "name"});
		var t1 = formatter(39.991268);
		
		expect(t1).toEqual('39.99 euros');
	});
	
	
	it("can give same result than old wrapped api for Number features with floats", function(done) {
		// FIXME: We try firstly the old wrapped method in order to load cldr catalogs synchronously,
		// because async loading does not work since we cannot moke $.get call properly.
		var t1 = formatNumber(1234.09876543, 4, '_', ';');
		var t2 = '';
		formatNumberCldr(1234.09876543, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		}, 4);
	});
	
	
	it("can give same result than old wrapped api for Number features with integers", function(done) {
		// FIXME: We try firstly the old wrapped method in order to load cldr catalogs synchronously,
		// because async loading does not work since we cannot moke $.get call properly.
		var t1 = formatNumber(1234, 4, '_', ';');
		var t2 = '';
		formatNumberCldr(1234, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		}, 4);
	});
	
	
	it("can give same result than old wrapped api for Number features with rounds", function(done) {
		// FIXME: We try firstly the old wrapped method in order to load cldr catalogs synchronously,
		// because async loading does not work since we cannot moke $.get call properly.
		var t1 = formatNumber(1234.00000000001, 4, '_', ';');
		var t2 = '';
		formatNumberCldr(1234.00000000001, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		}, 4);
	});
	
	
	it("can give same result than old wrapped api for Currency features with 2 decimals precision", function(done) {
		priceDisplayPrecision = 2;
		
		// FIXME: We try firstly the old wrapped method in order to load cldr catalogs synchronously,
		// because async loading does not work since we cannot moke $.get call properly.
		var t1 = formatCurrency(12344.12345657, 1, '$', 0);
		var t2 = '';
		formatCurrencyCldr(12344.12345657, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		});
	});
	
	
	it("can give same result than old wrapped api for Currency features with 6 decimals precision", function(done) {
		priceDisplayPrecision = 6;
		
		// FIXME: We try firstly the old wrapped method in order to load cldr catalogs synchronously,
		// because async loading does not work since we cannot moke $.get call properly.
		var t1 = formatCurrency(22344.12345657, 1, '$', 0);
		var t2 = '';
		formatCurrencyCldr(22344.12345657, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		});
	});
	
	
	it("can give same result than old wrapped api for Currency features with negative prices", function(done) {
		priceDisplayPrecision = 2;
		
		// FIXME: We try firstly the old wrapped method in order to load cldr catalogs synchronously,
		// because async loading does not work since we cannot moke $.get call properly.
		var t1 = formatCurrency(-0.5698, 1, '$', 0);
		var t2 = '';
		formatCurrencyCldr(-0.5698, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		});
	});
});
