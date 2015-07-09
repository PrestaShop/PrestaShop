var cldrLoadedCatalogs = [];
var cldrLoaderError = false;
var cldrCatalogsPath = '/translations/cldr/datas/';

function cldrLoadCatalogs(catalogs, callback) {
	var sync = (typeof catalog === 'undefined' || callback == false);
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
				deferreds.push($.Deferred(function () {
					$.get(url).done(function() {
						cldrLoadedCatalogs.push(url);
					}).fail(function() {
						cldrLoaderError = true;
					});
				}));
		});
		
		// TODO : mettre deferreds dans $.when.apply(null, deferreds).then...
	}
}

function cldrForDate(callback) {
	var catalogs = ['main/en/ca-gregorian', 'main/en/numbers', 'supplemental/timeData',
	                'supplemental/weekData', 'supplemental/likelySubtags', 'supplemental/plurals'];
	return cldrLoadCatalogs(catalogs, callback);
}

function testSync() {
	console.log(cldrForDate().formatDate( new Date(), { datetime: "medium" }));
}

function testAsync() {
	cldrForDate(function(gl) {
		console.log(gl.formatDate( new Date(), { datetime: "medium" }));
	});
}