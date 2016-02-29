// javascript variables available on all Admin pages
var currency;
var full_language_code;
var priceDisplayPrecision;

describe('CLDR api', function() {

	$.mockjax(function(settings) {
		var doesMatch = settings.url.match(/\/translations\/cldr\/datas\/(.*)$/);
		if (doesMatch) {
			var response = '';
			switch(settings.url) {
				case cldrCatalogsPath+'main/en-US/numbers.json':
					response = fakeEnNumbersCatalog;
					break;
				case cldrCatalogsPath+'supplemental/likelySubtags.json':
					response = fakeSupplementalLikelySubtagsCatalog;
					break;
				case cldrCatalogsPath+'supplemental/numberingSystems.json':
					response = fakeSupplementalNumberingSystemsCatalog;
					break;
				case cldrCatalogsPath+'main/en-US/ca-gregorian.json':
					response = fakeEnCalendarCatalog;
					break;
				case cldrCatalogsPath+'main/en-US/currencies.json':
					response = fakeEnCurrenciesCatalog;
					break;
				case cldrCatalogsPath+'main/en-US/timeZoneNames.json':
					response = fakeEnTimeZoneNamesCatalog;
					break;
				case cldrCatalogsPath+'supplemental/currencyData.json':
					response = fakeSupplementalCurrencyDataCatalog;
					break;
				case cldrCatalogsPath+'supplemental/timeData.json':
					response = fakeSupplementalTimeDataCatalog;
					break;
				case cldrCatalogsPath+'supplemental/plurals.json':
					response = fakeSupplementalPluralsCatalog;
					break;
				case cldrCatalogsPath+'supplemental/weekData.json':
					response = fakeSupplementalWeekDataCatalog;
					break;
				default:
					throw Error(settings.url+' was not mocked for tests!');
			}
		    return {
		    	responseText: response,
		    	status: 200,
		    	contentType: 'application/json'
		    };
		}
		// If you get here, there was no url match
		return;
	});
	
	beforeEach(function() {
		currency = { 'iso_code': 'EUR' };
		full_language_code = 'en-us';
		priceDisplayPrecision = 2;
		cldrLoadedCatalogs = []; // force to reload all catalogs for each it()
	});

	it('cannot load an unknown/void catalog', function() {
		var unknownCatalog = function() {
			cldrLazyLoadCatalogs(['i/do/not/know/you']);
		};
		expect(unknownCatalog).toThrowError(Error, /was not mocked for tests/);
		
		var undefinedCatalog = function() {
			cldrLazyLoadCatalogs();
		};
		expect(undefinedCatalog).toThrowError(Error, /No catalog to load!/);
		
		var voidCatalog = function() {
			cldrLazyLoadCatalogs([]);
		};
		expect(voidCatalog).toThrowError(Error);
	});

	
	it('can load an existing catalog synchronously', function() {
		cldrLazyLoadCatalogs(['supplemental/likelySubtags']);
		
		expect(cldrLoadedCatalogs).toContain(cldrCatalogsPath+'supplemental/likelySubtags.json');
	});
	
	
	it('can load existing catalogs asynchronously', function(done) {
		var callback = function(globalize) {
			expect(cldrLoadedCatalogs).toContain(cldrCatalogsPath+'supplemental/likelySubtags.json');
			done();
		};
		cldrLazyLoadCatalogs(['supplemental/likelySubtags', 'supplemental/plurals'], callback);
	});
	
	
	it('can load catalogs for Numbers features, and can make simple formatter/parser calls', function() {
		var globalize = cldrForNumber();
		var t1 = globalize.numberFormatter({
				minimumSignificantDigits: 1,
				maximumSignificantDigits: 3
			})(3.141592);
		var t2 = globalize.numberParser()('$57.67');
		
		expect(t1).toEqual('3.14');
		expect(t2).toEqual(57.67);
	});

	
	it('can load catalogs for Date features, and can make simple formatter calls', function() {
		var globalize = cldrForDate();
		var t1 = globalize.dateFormatter({ datetime: 'medium' })(new Date(2011, 11, 31, 22, 45, 59));
		
		expect(t1).toEqual('Dec 31, 2011, 10:45:59 PM');
	});
	
	
	it('can load catalogs for Currency features, and can make simple formatter calls with manual currency set (to EUR)', function() {
		var currencyIsoCode = 'EUR';
		var globalize = cldrForCurrencies();
		var t1 = globalize.currencyFormatter(currencyIsoCode)(59.99);
		
		expect(t1).toEqual('€59.99');
	});
	
	
	it('can load catalogs for Currency features through currency wrapper method, and can make simple formatter calls with auto currency set', function() {
		var formatter = cldrForCurrencyFormatterWrapper(null, {style: 'name'});
		var t1 = formatter(39.991268);
		
		expect(t1).toEqual('39.99 euros');
	});
	
	
	it('can give same result than old wrapped api for Number features with floats', function(done) {
		var t1 = formatNumber(1234.09876543, 4, '_', ';');
		var t2 = '';
		formatNumberCldr(1234.09876543, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		}, 4);
	});
	
	
	it('can give same result than old wrapped api for Number features with integers', function(done) {
		var t1 = formatNumber(1234, 4, '_', ';');
		var t2 = '';
		formatNumberCldr(1234, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		}, 4);
	});
	
	
	it('can give same result than old wrapped api for Number features with rounds', function(done) {
		var t1 = formatNumber(1234.00000000001, 4, '_', ';');
		var t2 = '';
		formatNumberCldr(1234.00000000001, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		}, 4);
	});
	
	
	it('can give same result than old wrapped api for Currency features with 2 decimals precision', function(done) {
		priceDisplayPrecision = 2;
		
		var t1 = formatCurrency(12344.12345657, 1, '$', 0);
		var t2 = '';
		formatCurrencyCldr(12344.12345657, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		});
	});
	
	
	it('can give same result than old wrapped api for Currency features with 6 decimals precision', function(done) {
		priceDisplayPrecision = 6;
		
		var t1 = formatCurrency(22344.12345657, 1, '$', 0);
		var t2 = '';
		formatCurrencyCldr(22344.12345657, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		});
	});
	
	
	it('can give same result than old wrapped api for Currency features with negative prices', function(done) {
		priceDisplayPrecision = 2;
		
		var t1 = formatCurrency(-0.5698, 1, '$', 0);
		var t2 = '';
		formatCurrencyCldr(-0.5698, function(v) {
			t2 = v;
			expect(t1).toEqual(t2);
			done();
		});
	});
});
