/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/* CLDR globals */
var cldrLoadedCatalogs = [];
var cldrLoaderError = false;
var cldrCatalogsPath = (typeof baseDir !== 'undefined' ? baseDir : '') + 'translations/cldr/datas/';

/**
 * Will get list of CLDR catalogs by XHR.
 * Please do not call this directly except if you know what you do. Prefer to call the wrapper methods cldrForXXX()
 * containing catalogs depending on the locale type you want to use.
 *
 * Asynchronous behavior: If callback is defined and callable, then each ajax request will be
 * asynchronous and the callback will be called with a Globalize object in its parameter.
 *
 * Synchronous behavior: If callback is undefined, then ajax request will be SYNCHRONOUS.
 * The function will return a Globalize object instance.
 * WARNING: Please avoid as much as you can this SYNC behavior till its deprecated for browser
 * because of a slow down process in browsers (will freeze javascript process until each CLDR
 * catalogs are fully loaded).
 *
 * @param catalogs An array of strings representing the catalogs to load.
 * @param callback A function to execute in ASYNC behavior. This will receive a Globalize object as parameter.
 * @returns Globalize instance in SYNC behavior only.
 */
function cldrLazyLoadCatalogs(catalogs, callback) {
	if (typeof catalogs !== 'object' || catalogs.length < 1) {
		throw Error('No catalog to load!');
	}
	var sync = (typeof callback === 'undefined' || !$.isFunction(callback));
	var culture = full_cldr_language_code;

	if (sync) {
		// Warning, Sync behavior will slow down Browser performances!
		catalogs.forEach(function(catalog) {
			var url = cldrCatalogsPath + catalog.replace(/main\/[^\/]+/, 'main/'+culture) + '.json';
			if ($.inArray(url, cldrLoadedCatalogs) === -1) {
				$.ajax({
					url: url,
					dataType: 'json',
					async: false, // deprecated for modern browser, but not really other choice...
					success: function(data) {
						Globalize.load(data);
						cldrLoadedCatalogs.push(url);
					},
					error: function(xhr) {
						cldrLoaderError = true;
					}
				});
			}
		});

		if (!cldrLoaderError) {
			return new Globalize(culture);
		}
	} else {
		var deferreds = [];
		catalogs.forEach(function(catalog) {
			var url = cldrCatalogsPath + catalog.replace(/main\/[^\/]+/, 'main/'+culture) + '.json';
			if ($.inArray(url, cldrLoadedCatalogs) === -1) {
				this.push($.get(url).done(function() {
						cldrLoadedCatalogs.push(url);
					}).fail(function() {
						cldrLoaderError = true;
					}));
			}
		}, deferreds);

		if (deferreds.length > 0) {
			$.when.apply($, deferreds).then(function() {
				return [].slice.apply( arguments, [ 0 ] ).map(function( result ) {
					return result[ 0 ];
				});
		    }).then( Globalize.load ).then(function() {
		    	if (!cldrLoaderError) {
		    		callback(new Globalize(culture));
		    	} else {
		    		throw Error('Cannot load given catalogs.');
		    	}
		    });
		} else {
			callback(new Globalize(culture));
		}
	}
}

/**
 * Will load CLDR catalogs for Number conversions.
 *
 * Asynchronous behavior: If callback is defined and callable, then each ajax request will be
 * asynchronous and the callback will be called with a Globalize object in its parameter.
 *
 * Synchronous behavior: If callback is undefined, then ajax request will be SYNCHRONOUS.
 * The function will return a Globalize object instance.
 * WARNING: Please avoid as much as you can this SYNC behavior till its deprecated for browser
 * because of a slow down process in browsers (will freeze javascript process until each CLDR
 * catalogs are fully loaded).
 *
 * @param callback A function to execute in ASYNC behavior. This will receive a Globalize object as parameter.
 * @returns Globalize instance in SYNC behavior only.
 */
function cldrForNumber(callback) {
	var catalogs = ['main/en/numbers', 'supplemental/likelySubtags', 'supplemental/numberingSystems'];
	return cldrLazyLoadCatalogs(catalogs, callback);
}

/**
 * Will load CLDR catalogs for Currencies conversions.
 *
 * Asynchronous behavior: If callback is defined and callable, then each ajax request will be
 * asynchronous and the callback will be called with a Globalize object in its parameter.
 *
 * Synchronous behavior: If callback is undefined, then ajax request will be SYNCHRONOUS.
 * The function will return a Globalize object instance.
 * WARNING: Please avoid as much as you can this SYNC behavior till its deprecated for browser
 * because of a slow down process in browsers (will freeze javascript process until each CLDR
 * catalogs are fully loaded).
 *
 * @param callback A function to execute in ASYNC behavior. This will receive a Globalize object as parameter.
 * @returns Globalize instance in SYNC behavior only.
 */
function cldrForCurrencies(callback) {
	var catalogs = ['main/en/numbers', 'main/en/currencies', 'supplemental/likelySubtags',
	                'supplemental/currencyData', 'supplemental/plurals'];
	return cldrLazyLoadCatalogs(catalogs, callback);
}

/**
 * A small wrapper for currency, returning directly the currency formatter with the good currency.
 * Works in SYNC or ASYNC behaviors.
 * Warning: SYNC behavior should be avoided.
 * @see cldrForCurrencies(callback)
 *
 * @param callback A function to execute in ASYNC behavior. This will receive a currencyFormatter object as parameter.
 * @param options An option hash table to transfer to formatter factory.
 * @returns currencyFormatter instance in SYNC behavior only.
 */
function cldrForCurrencyFormatterWrapper(callback, options) {
	var sync = (typeof callback === 'undefined' || !$.isFunction(callback));
	var currencyIsoCode = currency.iso_code;

	if (sync) {
		var globalize = cldrForCurrencies();
		return globalize.currencyFormatter(currencyIsoCode, options);
	} else {
		var callbackEncap = function(globalize) {
			callback(globalize.currencyFormatter(currencyIsoCode, options));
		};
		cldrForCurrencies(callbackEncap);
	}
}

/**
 * Will load CLDR catalogs for Date conversions.
 *
 * Asynchronous behavior: If callback is defined and callable, then each ajax request will be
 * asynchronous and the callback will be called with a Globalize object in its parameter.
 *
 * Synchronous behavior: If callback is undefined, then ajax request will be SYNCHRONOUS.
 * The function will return a Globalize object instance.
 * WARNING: Please avoid as much as you can this SYNC behavior till its deprecated for browser
 * because of a slow down process in browsers (will freeze javascript process until each CLDR
 * catalogs are fully loaded).
 *
 * @param callback A function to execute in ASYNC behavior. This will receive a Globalize object as parameter.
 * @returns Globalize instance in SYNC behavior only.
 */
function cldrForDate(callback) {
	var catalogs = ['main/en/numbers', 'main/en/ca-gregorian', 'main/en/timeZoneNames', 'supplemental/timeData',
	                'supplemental/weekData', 'supplemental/likelySubtags'];
	return cldrLazyLoadCatalogs(catalogs, callback);
}
