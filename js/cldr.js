/* CLDR globals */
var cldrLoadedCatalogs = [];
var cldrLoaderError = false;
var cldrCatalogsPath = '/translations/cldr/datas/';

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
	var sync = (typeof callback === 'undefined' || callback == false);
	var culture = full_language_code.split('-'); // en-us -> [en, us]
	culture = culture[0] + '-' + culture[1].toUpperCase(); // en-US
	
	if (sync) {
		// Warning, Sync behavior will slow down Browser performances!
		catalogs.forEach(function(catalog) {
			var url = cldrCatalogsPath + catalog.replace(/main\/[^\/]+/, 'main/'+culture) + '.json';
			if ($.inArray(url, cldrLoadedCatalogs) == -1)
				$.ajax({
					url: url,
					dataType: 'json',
					async: false, // deprecated for modern browser, but not really other choice...
					success: function(data) {
						Globalize.load(data);
						cldrLoadedCatalogs.push(url);
					},
					error: function() {
						cldrLoaderError = true;
					}
				});
		});
		
		if (!cldrLoaderError) return Globalize(culture);
	} else {
		var deferreds = [];
		catalogs.forEach(function(catalog) {
			var url = cldrCatalogsPath + catalog.replace(/main\/[^\/]+/, 'main/'+culture) + '.json';
			if ($.inArray(url, cldrLoadedCatalogs) == -1)
				this.push($.get(url).done(function() {
						cldrLoadedCatalogs.push(url);
					}).fail(function() {
						cldrLoaderError = true;
					}));
		}, deferreds);

		$.when.apply($, deferreds).then(function() {
			return [].slice.apply( arguments, [ 0 ] ).map(function( result ) {
				return result[ 0 ];
			});
	    }).then( Globalize.load ).then(function() {
	    	if (!cldrLoaderError) 
	    		callback(Globalize(culture));
	    });
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
	var catalogs = ['main/en/ca-gregorian', 'main/en/numbers', 'supplemental/timeData',
	                'supplemental/weekData', 'supplemental/likelySubtags', 'supplemental/plurals'];
	return cldrLazyLoadCatalogs(catalogs, callback);
}



function testSync() {
	console.log(cldrForDate().formatDate( new Date(), { datetime: "medium" }));
	
}

function testAsync() {
	cldrForDate(function(gl) {
		console.log(gl.formatDate( new Date(), { datetime: "medium" }));
	});
}

