(function(define) {
/* Afrikaans initialisation for the jQuery UI date picker plugin. */
/* Written by Renier Pretorius. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.af = {
	closeText: "Selekteer",
	prevText: "Vorige",
	nextText: "Volgende",
	currentText: "Vandag",
	monthNames: [ "Januarie", "Februarie", "Maart", "April", "Mei", "Junie",
	"Julie", "Augustus", "September", "Oktober", "November", "Desember" ],
	monthNamesShort: [ "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun",
	"Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
	dayNames: [ "Sondag", "Maandag", "Dinsdag", "Woensdag", "Donderdag", "Vrydag", "Saterdag" ],
	dayNamesShort: [ "Son", "Maa", "Din", "Woe", "Don", "Vry", "Sat" ],
	dayNamesMin: [ "So", "Ma", "Di", "Wo", "Do", "Vr", "Sa" ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.af );

return datepicker.regional.af;

} );

}).call(this);

(function(define) {
/* Algerian Arabic Translation for jQuery UI date picker plugin.
/* Used in most of Maghreb countries, primarily in Algeria, Tunisia, Morocco.
/* Mohamed Cherif BOUCHELAGHEM -- cherifbouchelaghem@yahoo.fr */
/* Mohamed Amine HADDAD -- zatamine@gmail.com */

( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "ar-DZ" ] = {
	closeText: "إغلاق",
	prevText: "السابق",
	nextText: "التالي",
	currentText: "اليوم",
	monthNames: [ "جانفي", "فيفري", "مارس", "أفريل", "ماي", "جوان",
	"جويلية", "أوت", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر" ],
	monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
	dayNames: [ "الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت" ],
	dayNamesShort: [ "الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت" ],
	dayNamesMin: [ "ح", "ن", "ث", "ر", "خ", "ج", "س" ],
	weekHeader: "أسبوع",
	dateFormat: "dd/mm/yy",
	firstDay: 6,
		isRTL: true,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "ar-DZ" ] );

return datepicker.regional[ "ar-DZ" ];

} );

}).call(this);

(function(define) {
/* Arabic Translation for jQuery UI date picker plugin. */
/* Used in most of Arab countries, primarily in Bahrain, */
/* Kuwait, Oman, Qatar, Saudi Arabia and the United Arab Emirates, Egypt, Sudan and Yemen. */
/* Written by Mohammed Alshehri -- m@dralshehri.com */

( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ar = {
	closeText: "إغلاق",
	prevText: "السابق",
	nextText: "التالي",
	currentText: "اليوم",
	monthNames: [ "يناير", "فبراير", "مارس", "أبريل", "مايو", "يونيو",
	"يوليو", "أغسطس", "سبتمبر", "أكتوبر", "نوفمبر", "ديسمبر" ],
	monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
	dayNames: [ "الأحد", "الاثنين", "الثلاثاء", "الأربعاء", "الخميس", "الجمعة", "السبت" ],
	dayNamesShort: [ "أحد", "اثنين", "ثلاثاء", "أربعاء", "خميس", "جمعة", "سبت" ],
	dayNamesMin: [ "ح", "ن", "ث", "ر", "خ", "ج", "س" ],
	weekHeader: "أسبوع",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
		isRTL: true,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ar );

return datepicker.regional.ar;

} );

}).call(this);

(function(define) {
/* Azerbaijani (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Jamil Najafov (necefov33@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.az = {
	closeText: "Bağla",
	prevText: "Geri",
	nextText: "İrəli",
	currentText: "Bugün",
	monthNames: [ "Yanvar", "Fevral", "Mart", "Aprel", "May", "İyun",
	"İyul", "Avqust", "Sentyabr", "Oktyabr", "Noyabr", "Dekabr" ],
	monthNamesShort: [ "Yan", "Fev", "Mar", "Apr", "May", "İyun",
	"İyul", "Avq", "Sen", "Okt", "Noy", "Dek" ],
	dayNames: [ "Bazar", "Bazar ertəsi", "Çərşənbə axşamı", "Çərşənbə", "Cümə axşamı", "Cümə", "Şənbə" ],
	dayNamesShort: [ "B", "Be", "Ça", "Ç", "Ca", "C", "Ş" ],
	dayNamesMin: [ "B", "B", "Ç", "С", "Ç", "C", "Ş" ],
	weekHeader: "Hf",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.az );

return datepicker.regional.az;

} );

}).call(this);

(function(define) {
/* Belarusian initialisation for the jQuery UI date picker plugin. */
/* Written by Pavel Selitskas <p.selitskas@gmail.com> */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.be = {
	closeText: "Зачыніць",
	prevText: "Папяр.",
	nextText: "Наст.",
	currentText: "Сёньня",
	monthNames: [ "Студзень", "Люты", "Сакавік", "Красавік", "Травень", "Чэрвень",
	"Ліпень", "Жнівень", "Верасень", "Кастрычнік", "Лістапад", "Сьнежань" ],
	monthNamesShort: [ "Сту", "Лют", "Сак", "Кра", "Тра", "Чэр",
	"Ліп", "Жні", "Вер", "Кас", "Ліс", "Сьн" ],
	dayNames: [ "нядзеля", "панядзелак", "аўторак", "серада", "чацьвер", "пятніца", "субота" ],
	dayNamesShort: [ "ндз", "пнд", "аўт", "срд", "чцв", "птн", "сбт" ],
	dayNamesMin: [ "Нд", "Пн", "Аў", "Ср", "Чц", "Пт", "Сб" ],
	weekHeader: "Тд",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.be );

return datepicker.regional.be;

} );

}).call(this);

(function(define) {
/* Bulgarian initialisation for the jQuery UI date picker plugin. */
/* Written by Stoyan Kyosev. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.bg = {
	closeText: "затвори",
	prevText: "назад",
	nextText: "напред",
	nextBigText: "&#x3E;&#x3E;",
	currentText: "днес",
	monthNames: [ "Януари", "Февруари", "Март", "Април", "Май", "Юни",
	"Юли", "Август", "Септември", "Октомври", "Ноември", "Декември" ],
	monthNamesShort: [ "Яну", "Фев", "Мар", "Апр", "Май", "Юни",
	"Юли", "Авг", "Сеп", "Окт", "Нов", "Дек" ],
	dayNames: [ "Неделя", "Понеделник", "Вторник", "Сряда", "Четвъртък", "Петък", "Събота" ],
	dayNamesShort: [ "Нед", "Пон", "Вто", "Сря", "Чет", "Пет", "Съб" ],
	dayNamesMin: [ "Не", "По", "Вт", "Ср", "Че", "Пе", "Съ" ],
	weekHeader: "Wk",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.bg );

return datepicker.regional.bg;

} );

}).call(this);

(function(define) {
/* Bosnian i18n for the jQuery UI date picker plugin. */
/* Written by Kenan Konjo. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.bs = {
	closeText: "Zatvori",
	prevText: "Prethodno",
	nextText: "Sljedeći",
	currentText: "Danas",
	monthNames: [ "Januar", "Februar", "Mart", "April", "Maj", "Juni",
	"Juli", "August", "Septembar", "Oktobar", "Novembar", "Decembar" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Maj", "Jun",
	"Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ],
	dayNames: [ "Nedelja", "Ponedeljak", "Utorak", "Srijeda", "Četvrtak", "Petak", "Subota" ],
	dayNamesShort: [ "Ned", "Pon", "Uto", "Sri", "Čet", "Pet", "Sub" ],
	dayNamesMin: [ "Ne", "Po", "Ut", "Sr", "Če", "Pe", "Su" ],
	weekHeader: "Wk",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.bs );

return datepicker.regional.bs;

} );

}).call(this);

(function(define) {
/* Inicialització en català per a l'extensió 'UI date picker' per jQuery. */
/* Writers: (joan.leon@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ca = {
	closeText: "Tanca",
	prevText: "Anterior",
	nextText: "Següent",
	currentText: "Avui",
	monthNames: [ "gener", "febrer", "març", "abril", "maig", "juny",
	"juliol", "agost", "setembre", "octubre", "novembre", "desembre" ],
	monthNamesShort: [ "gen", "feb", "març", "abr", "maig", "juny",
	"jul", "ag", "set", "oct", "nov", "des" ],
	dayNames: [ "diumenge", "dilluns", "dimarts", "dimecres", "dijous", "divendres", "dissabte" ],
	dayNamesShort: [ "dg", "dl", "dt", "dc", "dj", "dv", "ds" ],
	dayNamesMin: [ "dg", "dl", "dt", "dc", "dj", "dv", "ds" ],
	weekHeader: "Set",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ca );

return datepicker.regional.ca;

} );

}).call(this);

(function(define) {
/* Czech initialisation for the jQuery UI date picker plugin. */
/* Written by Tomas Muller (tomas@tomas-muller.net). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.cs = {
	closeText: "Zavřít",
	prevText: "Dříve",
	nextText: "Později",
	currentText: "Nyní",
	monthNames: [ "leden", "únor", "březen", "duben", "květen", "červen",
	"červenec", "srpen", "září", "říjen", "listopad", "prosinec" ],
	monthNamesShort: [ "led", "úno", "bře", "dub", "kvě", "čer",
	"čvc", "srp", "zář", "říj", "lis", "pro" ],
	dayNames: [ "neděle", "pondělí", "úterý", "středa", "čtvrtek", "pátek", "sobota" ],
	dayNamesShort: [ "ne", "po", "út", "st", "čt", "pá", "so" ],
	dayNamesMin: [ "ne", "po", "út", "st", "čt", "pá", "so" ],
	weekHeader: "Týd",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.cs );

return datepicker.regional.cs;

} );

}).call(this);

(function(define) {
/* Welsh/UK initialisation for the jQuery UI date picker plugin. */
/* Written by William Griffiths. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "cy-GB" ] = {
	closeText: "Done",
	prevText: "Prev",
	nextText: "Next",
	currentText: "Today",
	monthNames: [ "Ionawr", "Chwefror", "Mawrth", "Ebrill", "Mai", "Mehefin",
	"Gorffennaf", "Awst", "Medi", "Hydref", "Tachwedd", "Rhagfyr" ],
	monthNamesShort: [ "Ion", "Chw", "Maw", "Ebr", "Mai", "Meh",
	"Gor", "Aws", "Med", "Hyd", "Tac", "Rha" ],
	dayNames: [
		"Dydd Sul",
		"Dydd Llun",
		"Dydd Mawrth",
		"Dydd Mercher",
		"Dydd Iau",
		"Dydd Gwener",
		"Dydd Sadwrn"
	],
	dayNamesShort: [ "Sul", "Llu", "Maw", "Mer", "Iau", "Gwe", "Sad" ],
	dayNamesMin: [ "Su", "Ll", "Ma", "Me", "Ia", "Gw", "Sa" ],
	weekHeader: "Wy",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "cy-GB" ] );

return datepicker.regional[ "cy-GB" ];

} );

}).call(this);

(function(define) {
/* Danish initialisation for the jQuery UI date picker plugin. */
/* Written by Jan Christensen ( deletestuff@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.da = {
	closeText: "Luk",
	prevText: "Forrige",
	nextText: "Næste",
	currentText: "I dag",
	monthNames: [ "Januar", "Februar", "Marts", "April", "Maj", "Juni",
	"Juli", "August", "September", "Oktober", "November", "December" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Maj", "Jun",
	"Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ],
	dayNames: [ "Søndag", "Mandag", "Tirsdag", "Onsdag", "Torsdag", "Fredag", "Lørdag" ],
	dayNamesShort: [ "Søn", "Man", "Tir", "Ons", "Tor", "Fre", "Lør" ],
	dayNamesMin: [ "Sø", "Ma", "Ti", "On", "To", "Fr", "Lø" ],
	weekHeader: "Uge",
	dateFormat: "dd-mm-yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.da );

return datepicker.regional.da;

} );

}).call(this);

(function(define) {
/* German/Austrian initialisation for the jQuery UI date picker plugin. */
/* Based on the de initialisation. */

( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "de-AT" ] = {
	closeText: "Schließen",
	prevText: "Zurück",
	nextText: "Vor",
	currentText: "Heute",
	monthNames: [ "Jänner", "Februar", "März", "April", "Mai", "Juni",
	"Juli", "August", "September", "Oktober", "November", "Dezember" ],
	monthNamesShort: [ "Jän", "Feb", "Mär", "Apr", "Mai", "Jun",
	"Jul", "Aug", "Sep", "Okt", "Nov", "Dez" ],
	dayNames: [ "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
	dayNamesShort: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
	dayNamesMin: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
	weekHeader: "KW",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "de-AT" ] );

return datepicker.regional[ "de-AT" ];

} );

}).call(this);

(function(define) {
/* German initialisation for the jQuery UI date picker plugin. */
/* Written by Milian Wolff (mail@milianw.de). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.de = {
	closeText: "Schließen",
	prevText: "Zurück",
	nextText: "Vor",
	currentText: "Heute",
	monthNames: [ "Januar", "Februar", "März", "April", "Mai", "Juni",
	"Juli", "August", "September", "Oktober", "November", "Dezember" ],
	monthNamesShort: [ "Jan", "Feb", "Mär", "Apr", "Mai", "Jun",
	"Jul", "Aug", "Sep", "Okt", "Nov", "Dez" ],
	dayNames: [ "Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag" ],
	dayNamesShort: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
	dayNamesMin: [ "So", "Mo", "Di", "Mi", "Do", "Fr", "Sa" ],
	weekHeader: "KW",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.de );

return datepicker.regional.de;

} );

}).call(this);

(function(define) {
/* Greek (el) initialisation for the jQuery UI date picker plugin. */
/* Written by Alex Cicovic (https://alexcicovic.com) */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.el = {
	closeText: "Κλείσιμο",
	prevText: "Προηγούμενος",
	nextText: "Επόμενος",
	currentText: "Σήμερα",
	monthNames: [ "Ιανουάριος", "Φεβρουάριος", "Μάρτιος", "Απρίλιος", "Μάιος", "Ιούνιος",
	"Ιούλιος", "Αύγουστος", "Σεπτέμβριος", "Οκτώβριος", "Νοέμβριος", "Δεκέμβριος" ],
	monthNamesShort: [ "Ιαν", "Φεβ", "Μαρ", "Απρ", "Μαι", "Ιουν",
	"Ιουλ", "Αυγ", "Σεπ", "Οκτ", "Νοε", "Δεκ" ],
	dayNames: [ "Κυριακή", "Δευτέρα", "Τρίτη", "Τετάρτη", "Πέμπτη", "Παρασκευή", "Σάββατο" ],
	dayNamesShort: [ "Κυρ", "Δευ", "Τρι", "Τετ", "Πεμ", "Παρ", "Σαβ" ],
	dayNamesMin: [ "Κυ", "Δε", "Τρ", "Τε", "Πε", "Πα", "Σα" ],
	weekHeader: "Εβδ",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.el );

return datepicker.regional.el;

} );

}).call(this);

(function(define) {
/* English/Australia initialisation for the jQuery UI date picker plugin. */
/* Based on the en-GB initialisation. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "en-AU" ] = {
	closeText: "Done",
	prevText: "Prev",
	nextText: "Next",
	currentText: "Today",
	monthNames: [ "January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun",
	"Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
	dayNames: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
	dayNamesShort: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
	dayNamesMin: [ "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa" ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "en-AU" ] );

return datepicker.regional[ "en-AU" ];

} );

}).call(this);

(function(define) {
/* English/UK initialisation for the jQuery UI date picker plugin. */
/* Written by Stuart. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "en-GB" ] = {
	closeText: "Done",
	prevText: "Prev",
	nextText: "Next",
	currentText: "Today",
	monthNames: [ "January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun",
	"Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
	dayNames: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
	dayNamesShort: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
	dayNamesMin: [ "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa" ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "en-GB" ] );

return datepicker.regional[ "en-GB" ];

} );

}).call(this);

(function(define) {
/* English/New Zealand initialisation for the jQuery UI date picker plugin. */
/* Based on the en-GB initialisation. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "en-NZ" ] = {
	closeText: "Done",
	prevText: "Prev",
	nextText: "Next",
	currentText: "Today",
	monthNames: [ "January", "February", "March", "April", "May", "June",
	"July", "August", "September", "October", "November", "December" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "May", "Jun",
	"Jul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
	dayNames: [ "Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday" ],
	dayNamesShort: [ "Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat" ],
	dayNamesMin: [ "Su", "Mo", "Tu", "We", "Th", "Fr", "Sa" ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "en-NZ" ] );

return datepicker.regional[ "en-NZ" ];

} );

}).call(this);

(function(define) {
/* Esperanto initialisation for the jQuery UI date picker plugin. */
/* Written by Olivier M. (olivierweb@ifrance.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.eo = {
	closeText: "Fermi",
	prevText: "Anta",
	nextText: "Sekv",
	currentText: "Nuna",
	monthNames: [ "Januaro", "Februaro", "Marto", "Aprilo", "Majo", "Junio",
	"Julio", "Aŭgusto", "Septembro", "Oktobro", "Novembro", "Decembro" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Maj", "Jun",
	"Jul", "Aŭg", "Sep", "Okt", "Nov", "Dec" ],
	dayNames: [ "Dimanĉo", "Lundo", "Mardo", "Merkredo", "Ĵaŭdo", "Vendredo", "Sabato" ],
	dayNamesShort: [ "Dim", "Lun", "Mar", "Mer", "Ĵaŭ", "Ven", "Sab" ],
	dayNamesMin: [ "Di", "Lu", "Ma", "Me", "Ĵa", "Ve", "Sa" ],
	weekHeader: "Sb",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.eo );

return datepicker.regional.eo;

} );

}).call(this);

(function(define) {
/* Inicialización en español para la extensión 'UI date picker' para jQuery. */
/* Traducido por Vester (xvester@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.es = {
	closeText: "Cerrar",
	prevText: "Ant",
	nextText: "Sig",
	currentText: "Hoy",
	monthNames: [ "enero", "febrero", "marzo", "abril", "mayo", "junio",
	"julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre" ],
	monthNamesShort: [ "ene", "feb", "mar", "abr", "may", "jun",
	"jul", "ago", "sep", "oct", "nov", "dic" ],
	dayNames: [ "domingo", "lunes", "martes", "miércoles", "jueves", "viernes", "sábado" ],
	dayNamesShort: [ "dom", "lun", "mar", "mié", "jue", "vie", "sáb" ],
	dayNamesMin: [ "D", "L", "M", "X", "J", "V", "S" ],
	weekHeader: "Sm",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.es );

return datepicker.regional.es;

} );

}).call(this);

(function(define) {
/* Estonian initialisation for the jQuery UI date picker plugin. */
/* Written by Mart Sõmermaa (mrts.pydev at gmail com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.et = {
	closeText: "Sulge",
	prevText: "Eelnev",
	nextText: "Järgnev",
	currentText: "Täna",
	monthNames: [ "Jaanuar", "Veebruar", "Märts", "Aprill", "Mai", "Juuni",
	"Juuli", "August", "September", "Oktoober", "November", "Detsember" ],
	monthNamesShort: [ "Jaan", "Veebr", "Märts", "Apr", "Mai", "Juuni",
	"Juuli", "Aug", "Sept", "Okt", "Nov", "Dets" ],
	dayNames: [
		"Pühapäev",
		"Esmaspäev",
		"Teisipäev",
		"Kolmapäev",
		"Neljapäev",
		"Reede",
		"Laupäev"
	],
	dayNamesShort: [ "Pühap", "Esmasp", "Teisip", "Kolmap", "Neljap", "Reede", "Laup" ],
	dayNamesMin: [ "P", "E", "T", "K", "N", "R", "L" ],
	weekHeader: "näd",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.et );

return datepicker.regional.et;

} );

}).call(this);

(function(define) {
/* Karrikas-ek itzulia (karrikas@karrikas.com) */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.eu = {
	closeText: "Egina",
	prevText: "Aur",
	nextText: "Hur",
	currentText: "Gaur",
	monthNames: [ "urtarrila", "otsaila", "martxoa", "apirila", "maiatza", "ekaina",
		"uztaila", "abuztua", "iraila", "urria", "azaroa", "abendua" ],
	monthNamesShort: [ "urt.", "ots.", "mar.", "api.", "mai.", "eka.",
		"uzt.", "abu.", "ira.", "urr.", "aza.", "abe." ],
	dayNames: [ "igandea", "astelehena", "asteartea", "asteazkena", "osteguna", "ostirala", "larunbata" ],
	dayNamesShort: [ "ig.", "al.", "ar.", "az.", "og.", "ol.", "lr." ],
	dayNamesMin: [ "ig", "al", "ar", "az", "og", "ol", "lr" ],
	weekHeader: "As",
	dateFormat: "yy-mm-dd",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.eu );

return datepicker.regional.eu;

} );

}).call(this);

(function(define) {
/* Persian (Farsi) Translation for the jQuery UI date picker plugin. */
/* Javad Mowlanezhad -- jmowla@gmail.com */
/* Jalali calendar should supported soon! (Its implemented but I have to test it) */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.fa = {
	closeText: "بستن",
	prevText: "قبلی",
	nextText: "بعدی",
	currentText: "امروز",
	monthNames: [
		"ژانویه",
		"فوریه",
		"مارس",
		"آوریل",
		"مه",
		"ژوئن",
		"ژوئیه",
		"اوت",
		"سپتامبر",
		"اکتبر",
		"نوامبر",
		"دسامبر"
	],
	monthNamesShort: [ "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12" ],
	dayNames: [
		"يکشنبه",
		"دوشنبه",
		"سه‌شنبه",
		"چهارشنبه",
		"پنجشنبه",
		"جمعه",
		"شنبه"
	],
	dayNamesShort: [
		"ی",
		"د",
		"س",
		"چ",
		"پ",
		"ج",
		"ش"
	],
	dayNamesMin: [
		"ی",
		"د",
		"س",
		"چ",
		"پ",
		"ج",
		"ش"
	],
	weekHeader: "هف",
	dateFormat: "yy/mm/dd",
	firstDay: 6,
	isRTL: true,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.fa );

return datepicker.regional.fa;

} );

}).call(this);

(function(define) {
/* Finnish initialisation for the jQuery UI date picker plugin. */
/* Written by Harri Kilpiö (harrikilpio@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.fi = {
	closeText: "Sulje",
	prevText: "Edellinen",
	nextText: "Seuraava",
	currentText: "Tänään",
	monthNames: [ "Tammikuu", "Helmikuu", "Maaliskuu", "Huhtikuu", "Toukokuu", "Kesäkuu",
	"Heinäkuu", "Elokuu", "Syyskuu", "Lokakuu", "Marraskuu", "Joulukuu" ],
	monthNamesShort: [ "Tammi", "Helmi", "Maalis", "Huhti", "Touko", "Kesä",
	"Heinä", "Elo", "Syys", "Loka", "Marras", "Joulu" ],
	dayNamesShort: [ "Su", "Ma", "Ti", "Ke", "To", "Pe", "La" ],
	dayNames: [ "Sunnuntai", "Maanantai", "Tiistai", "Keskiviikko", "Torstai", "Perjantai", "Lauantai" ],
	dayNamesMin: [ "Su", "Ma", "Ti", "Ke", "To", "Pe", "La" ],
	weekHeader: "Vk",
	dateFormat: "d.m.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.fi );

return datepicker.regional.fi;

} );

}).call(this);

(function(define) {
/* Faroese initialisation for the jQuery UI date picker plugin */
/* Written by Sverri Mohr Olsen, sverrimo@gmail.com */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.fo = {
	closeText: "Lat aftur",
	prevText: "Fyrra",
	nextText: "Næsta",
	currentText: "Í dag",
	monthNames: [ "Januar", "Februar", "Mars", "Apríl", "Mei", "Juni",
	"Juli", "August", "September", "Oktober", "November", "Desember" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
	"Jul", "Aug", "Sep", "Okt", "Nov", "Des" ],
	dayNames: [
		"Sunnudagur",
		"Mánadagur",
		"Týsdagur",
		"Mikudagur",
		"Hósdagur",
		"Fríggjadagur",
		"Leyardagur"
	],
	dayNamesShort: [ "Sun", "Mán", "Týs", "Mik", "Hós", "Frí", "Ley" ],
	dayNamesMin: [ "Su", "Má", "Tý", "Mi", "Hó", "Fr", "Le" ],
	weekHeader: "Vk",
	dateFormat: "dd-mm-yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.fo );

return datepicker.regional.fo;

} );

}).call(this);

(function(define) {
/* Canadian-French initialisation for the jQuery UI date picker plugin. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "fr-CA" ] = {
	closeText: "Fermer",
	prevText: "Précédent",
	nextText: "Suivant",
	currentText: "Aujourd'hui",
	monthNames: [ "janvier", "février", "mars", "avril", "mai", "juin",
		"juillet", "août", "septembre", "octobre", "novembre", "décembre" ],
	monthNamesShort: [ "janv.", "févr.", "mars", "avril", "mai", "juin",
		"juil.", "août", "sept.", "oct.", "nov.", "déc." ],
	dayNames: [ "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" ],
	dayNamesShort: [ "dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam." ],
	dayNamesMin: [ "D", "L", "M", "M", "J", "V", "S" ],
	weekHeader: "Sem.",
	dateFormat: "yy-mm-dd",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ""
};
datepicker.setDefaults( datepicker.regional[ "fr-CA" ] );

return datepicker.regional[ "fr-CA" ];

} );

}).call(this);

(function(define) {
/* Swiss-French initialisation for the jQuery UI date picker plugin. */
/* Written Martin Voelkle (martin.voelkle@e-tc.ch). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "fr-CH" ] = {
	closeText: "Fermer",
	prevText: "Préc",
	nextText: "Suiv",
	currentText: "Courant",
	monthNames: [ "janvier", "février", "mars", "avril", "mai", "juin",
		"juillet", "août", "septembre", "octobre", "novembre", "décembre" ],
	monthNamesShort: [ "janv.", "févr.", "mars", "avril", "mai", "juin",
		"juil.", "août", "sept.", "oct.", "nov.", "déc." ],
	dayNames: [ "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" ],
	dayNamesShort: [ "dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam." ],
	dayNamesMin: [ "D", "L", "M", "M", "J", "V", "S" ],
	weekHeader: "Sm",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "fr-CH" ] );

return datepicker.regional[ "fr-CH" ];

} );

}).call(this);

(function(define) {
/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood{at}iinet.com.au),
			  Stéphane Nahmani (sholby@sholby.net),
			  Stéphane Raimbault <stephane.raimbault@gmail.com> */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.fr = {
	closeText: "Fermer",
	prevText: "Précédent",
	nextText: "Suivant",
	currentText: "Aujourd'hui",
	monthNames: [ "janvier", "février", "mars", "avril", "mai", "juin",
		"juillet", "août", "septembre", "octobre", "novembre", "décembre" ],
	monthNamesShort: [ "janv.", "févr.", "mars", "avr.", "mai", "juin",
		"juil.", "août", "sept.", "oct.", "nov.", "déc." ],
	dayNames: [ "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" ],
	dayNamesShort: [ "dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam." ],
	dayNamesMin: [ "D", "L", "M", "M", "J", "V", "S" ],
	weekHeader: "Sem.",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.fr );

return datepicker.regional.fr;

} );

}).call(this);

(function(define) {
/* Galician localization for 'UI date picker' jQuery extension. */
/* Translated by Jorge Barreiro <yortx.barry@gmail.com>. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.gl = {
	closeText: "Pechar",
	prevText: "Ant",
	nextText: "Seg",
	currentText: "Hoxe",
	monthNames: [ "Xaneiro", "Febreiro", "Marzo", "Abril", "Maio", "Xuño",
	"Xullo", "Agosto", "Setembro", "Outubro", "Novembro", "Decembro" ],
	monthNamesShort: [ "Xan", "Feb", "Mar", "Abr", "Mai", "Xuñ",
	"Xul", "Ago", "Set", "Out", "Nov", "Dec" ],
	dayNames: [ "Domingo", "Luns", "Martes", "Mércores", "Xoves", "Venres", "Sábado" ],
	dayNamesShort: [ "Dom", "Lun", "Mar", "Mér", "Xov", "Ven", "Sáb" ],
	dayNamesMin: [ "Do", "Lu", "Ma", "Mé", "Xo", "Ve", "Sá" ],
	weekHeader: "Sm",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.gl );

return datepicker.regional.gl;

} );

}).call(this);

(function(define) {
/* Hebrew initialisation for the UI Datepicker extension. */
/* Written by Amir Hardon (ahardon at gmail dot com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.he = {
	closeText: "סגור",
	prevText: "הקודם",
	nextText: "הבא",
	currentText: "היום",
	monthNames: [ "ינואר", "פברואר", "מרץ", "אפריל", "מאי", "יוני",
	"יולי", "אוגוסט", "ספטמבר", "אוקטובר", "נובמבר", "דצמבר" ],
	monthNamesShort: [ "ינו", "פבר", "מרץ", "אפר", "מאי", "יוני",
	"יולי", "אוג", "ספט", "אוק", "נוב", "דצמ" ],
	dayNames: [ "ראשון", "שני", "שלישי", "רביעי", "חמישי", "שישי", "שבת" ],
	dayNamesShort: [ "א'", "ב'", "ג'", "ד'", "ה'", "ו'", "שבת" ],
	dayNamesMin: [ "א'", "ב'", "ג'", "ד'", "ה'", "ו'", "שבת" ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
	isRTL: true,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.he );

return datepicker.regional.he;

} );

}).call(this);

(function(define) {
/* Hindi initialisation for the jQuery UI date picker plugin. */
/* Written by Michael Dawart. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.hi = {
	closeText: "बंद",
	prevText: "पिछला",
	nextText: "अगला",
	currentText: "आज",
	monthNames: [ "जनवरी ", "फरवरी", "मार्च", "अप्रेल", "मई", "जून",
	"जूलाई", "अगस्त ", "सितम्बर", "अक्टूबर", "नवम्बर", "दिसम्बर" ],
	monthNamesShort: [ "जन", "फर", "मार्च", "अप्रेल", "मई", "जून",
	"जूलाई", "अग", "सित", "अक्ट", "नव", "दि" ],
	dayNames: [ "रविवार", "सोमवार", "मंगलवार", "बुधवार", "गुरुवार", "शुक्रवार", "शनिवार" ],
	dayNamesShort: [ "रवि", "सोम", "मंगल", "बुध", "गुरु", "शुक्र", "शनि" ],
	dayNamesMin: [ "रवि", "सोम", "मंगल", "बुध", "गुरु", "शुक्र", "शनि" ],
	weekHeader: "हफ्ता",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.hi );

return datepicker.regional.hi;

} );

}).call(this);

(function(define) {
/* Croatian i18n for the jQuery UI date picker plugin. */
/* Written by Vjekoslav Nesek. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.hr = {
	closeText: "Zatvori",
	prevText: "Prethodno",
	nextText: "Sljedeći",
	currentText: "Danas",
	monthNames: [ "Siječanj", "Veljača", "Ožujak", "Travanj", "Svibanj", "Lipanj",
	"Srpanj", "Kolovoz", "Rujan", "Listopad", "Studeni", "Prosinac" ],
	monthNamesShort: [ "Sij", "Velj", "Ožu", "Tra", "Svi", "Lip",
	"Srp", "Kol", "Ruj", "Lis", "Stu", "Pro" ],
	dayNames: [ "Nedjelja", "Ponedjeljak", "Utorak", "Srijeda", "Četvrtak", "Petak", "Subota" ],
	dayNamesShort: [ "Ned", "Pon", "Uto", "Sri", "Čet", "Pet", "Sub" ],
	dayNamesMin: [ "Ne", "Po", "Ut", "Sr", "Če", "Pe", "Su" ],
	weekHeader: "Tje",
	dateFormat: "dd.mm.yy.",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.hr );

return datepicker.regional.hr;

} );

}).call(this);

(function(define) {
/* Hungarian initialisation for the jQuery UI date picker plugin. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.hu = {
	closeText: "Bezár",
	prevText: "Vissza",
	nextText: "Előre",
	currentText: "Ma",
	monthNames: [ "Január", "Február", "Március", "Április", "Május", "Június",
	"Július", "Augusztus", "Szeptember", "Október", "November", "December" ],
	monthNamesShort: [ "Jan", "Feb", "Már", "Ápr", "Máj", "Jún",
	"Júl", "Aug", "Szep", "Okt", "Nov", "Dec" ],
	dayNames: [ "Vasárnap", "Hétfő", "Kedd", "Szerda", "Csütörtök", "Péntek", "Szombat" ],
	dayNamesShort: [ "Vas", "Hét", "Ked", "Sze", "Csü", "Pén", "Szo" ],
	dayNamesMin: [ "V", "H", "K", "Sze", "Cs", "P", "Szo" ],
	weekHeader: "Hét",
	dateFormat: "yy.mm.dd.",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.hu );

return datepicker.regional.hu;

} );

}).call(this);

(function(define) {
/* Armenian(UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Levon Zakaryan (levon.zakaryan@gmail.com)*/
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.hy = {
	closeText: "Փակել",
	prevText: "Նախ.",
	nextText: "Հաջ.",
	currentText: "Այսօր",
	monthNames: [ "Հունվար", "Փետրվար", "Մարտ", "Ապրիլ", "Մայիս", "Հունիս",
	"Հուլիս", "Օգոստոս", "Սեպտեմբեր", "Հոկտեմբեր", "Նոյեմբեր", "Դեկտեմբեր" ],
	monthNamesShort: [ "Հունվ", "Փետր", "Մարտ", "Ապր", "Մայիս", "Հունիս",
	"Հուլ", "Օգս", "Սեպ", "Հոկ", "Նոյ", "Դեկ" ],
	dayNames: [ "կիրակի", "եկուշաբթի", "երեքշաբթի", "չորեքշաբթի", "հինգշաբթի", "ուրբաթ", "շաբաթ" ],
	dayNamesShort: [ "կիր", "երկ", "երք", "չրք", "հնգ", "ուրբ", "շբթ" ],
	dayNamesMin: [ "կիր", "երկ", "երք", "չրք", "հնգ", "ուրբ", "շբթ" ],
	weekHeader: "ՇԲՏ",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.hy );

return datepicker.regional.hy;

} );

}).call(this);

(function(define) {
/* Indonesian initialisation for the jQuery UI date picker plugin. */
/* Written by Deden Fathurahman (dedenf@gmail.com). */
/* Fixed by Denny Septian Panggabean (xamidimura@gmail.com) */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.id = {
	closeText: "Tutup",
	prevText: "Mundur",
	nextText: "Maju",
	currentText: "Hari ini",
	monthNames: [ "Januari", "Februari", "Maret", "April", "Mei", "Juni",
	"Juli", "Agustus", "September", "Oktober", "Nopember", "Desember" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mei", "Jun",
	"Jul", "Agus", "Sep", "Okt", "Nop", "Des" ],
	dayNames: [ "Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu" ],
	dayNamesShort: [ "Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab" ],
	dayNamesMin: [ "Mg", "Sn", "Sl", "Rb", "Km", "Jm", "Sb" ],
	weekHeader: "Mg",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.id );

return datepicker.regional.id;

} );

}).call(this);

(function(define) {
/* Icelandic initialisation for the jQuery UI date picker plugin. */
/* Written by Haukur H. Thorsson (haukur@eskill.is). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.is = {
	closeText: "Loka",
	prevText: "Fyrri",
	nextText: "Næsti ",
	currentText: "Í dag",
	monthNames: [ "Janúar", "Febrúar", "Mars", "Apríl", "Maí", "Júní",
	"Júlí", "Ágúst", "September", "Október", "Nóvember", "Desember" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Maí", "Jún",
	"Júl", "Ágú", "Sep", "Okt", "Nóv", "Des" ],
	dayNames: [
		"Sunnudagur",
		"Mánudagur",
		"Þriðjudagur",
		"Miðvikudagur",
		"Fimmtudagur",
		"Föstudagur",
		"Laugardagur"
	],
	dayNamesShort: [ "Sun", "Mán", "Þri", "Mið", "Fim", "Fös", "Lau" ],
	dayNamesMin: [ "Su", "Má", "Þr", "Mi", "Fi", "Fö", "La" ],
	weekHeader: "Vika",
	dateFormat: "dd.mm.yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.is );

return datepicker.regional.is;

} );

}).call(this);

(function(define) {
/* Italian initialisation for the jQuery UI date picker plugin. */
/* Written by Antonello Pasella (antonello.pasella@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "it-CH" ] = {
	closeText: "Chiudi",
	prevText: "Prec",
	nextText: "Succ",
	currentText: "Oggi",
	monthNames: [ "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
		"Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre" ],
	monthNamesShort: [ "Gen", "Feb", "Mar", "Apr", "Mag", "Giu",
		"Lug", "Ago", "Set", "Ott", "Nov", "Dic" ],
	dayNames: [ "Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato" ],
	dayNamesShort: [ "Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab" ],
	dayNamesMin: [ "Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa" ],
	weekHeader: "Sm",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "it-CH" ] );

return datepicker.regional[ "it-CH" ];

} );

}).call(this);

(function(define) {
/* Italian initialisation for the jQuery UI date picker plugin. */
/* Written by Antonello Pasella (antonello.pasella@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.it = {
	closeText: "Chiudi",
	prevText: "Prec",
	nextText: "Succ",
	currentText: "Oggi",
	monthNames: [ "Gennaio", "Febbraio", "Marzo", "Aprile", "Maggio", "Giugno",
		"Luglio", "Agosto", "Settembre", "Ottobre", "Novembre", "Dicembre" ],
	monthNamesShort: [ "Gen", "Feb", "Mar", "Apr", "Mag", "Giu",
		"Lug", "Ago", "Set", "Ott", "Nov", "Dic" ],
	dayNames: [ "Domenica", "Lunedì", "Martedì", "Mercoledì", "Giovedì", "Venerdì", "Sabato" ],
	dayNamesShort: [ "Dom", "Lun", "Mar", "Mer", "Gio", "Ven", "Sab" ],
	dayNamesMin: [ "Do", "Lu", "Ma", "Me", "Gi", "Ve", "Sa" ],
	weekHeader: "Sm",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.it );

return datepicker.regional.it;

} );

}).call(this);

(function(define) {
/* Japanese initialisation for the jQuery UI date picker plugin. */
/* Written by Kentaro SATO (kentaro@ranvis.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ja = {
	closeText: "閉じる",
	prevText: "前",
	nextText: "次",
	currentText: "今日",
	monthNames: [ "1月", "2月", "3月", "4月", "5月", "6月",
	"7月", "8月", "9月", "10月", "11月", "12月" ],
	monthNamesShort: [ "1月", "2月", "3月", "4月", "5月", "6月",
	"7月", "8月", "9月", "10月", "11月", "12月" ],
	dayNames: [ "日曜日", "月曜日", "火曜日", "水曜日", "木曜日", "金曜日", "土曜日" ],
	dayNamesShort: [ "日", "月", "火", "水", "木", "金", "土" ],
	dayNamesMin: [ "日", "月", "火", "水", "木", "金", "土" ],
	weekHeader: "週",
	dateFormat: "yy/mm/dd",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "年" };
datepicker.setDefaults( datepicker.regional.ja );

return datepicker.regional.ja;

} );

}).call(this);

(function(define) {
/* Georgian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Lado Lomidze (lado.lomidze@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ka = {
	closeText: "დახურვა",
	prevText: "წინა",
	nextText: "შემდეგი ",
	currentText: "დღეს",
	monthNames: [
		"იანვარი",
		"თებერვალი",
		"მარტი",
		"აპრილი",
		"მაისი",
		"ივნისი",
		"ივლისი",
		"აგვისტო",
		"სექტემბერი",
		"ოქტომბერი",
		"ნოემბერი",
		"დეკემბერი"
	],
	monthNamesShort: [ "იან", "თებ", "მარ", "აპრ", "მაი", "ივნ", "ივლ", "აგვ", "სექ", "ოქტ", "ნოე", "დეკ" ],
	dayNames: [ "კვირა", "ორშაბათი", "სამშაბათი", "ოთხშაბათი", "ხუთშაბათი", "პარასკევი", "შაბათი" ],
	dayNamesShort: [ "კვ", "ორშ", "სამ", "ოთხ", "ხუთ", "პარ", "შაბ" ],
	dayNamesMin: [ "კვ", "ორშ", "სამ", "ოთხ", "ხუთ", "პარ", "შაბ" ],
	weekHeader: "კვირა",
	dateFormat: "dd-mm-yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ka );

return datepicker.regional.ka;

} );

}).call(this);

(function(define) {
/* Kazakh (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Dmitriy Karasyov (dmitriy.karasyov@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.kk = {
	closeText: "Жабу",
	prevText: "Алдыңғы",
	nextText: "Келесі",
	currentText: "Бүгін",
	monthNames: [ "Қаңтар", "Ақпан", "Наурыз", "Сәуір", "Мамыр", "Маусым",
	"Шілде", "Тамыз", "Қыркүйек", "Қазан", "Қараша", "Желтоқсан" ],
	monthNamesShort: [ "Қаң", "Ақп", "Нау", "Сәу", "Мам", "Мау",
	"Шіл", "Там", "Қыр", "Қаз", "Қар", "Жел" ],
	dayNames: [ "Жексенбі", "Дүйсенбі", "Сейсенбі", "Сәрсенбі", "Бейсенбі", "Жұма", "Сенбі" ],
	dayNamesShort: [ "жкс", "дсн", "ссн", "срс", "бсн", "жма", "снб" ],
	dayNamesMin: [ "Жк", "Дс", "Сс", "Ср", "Бс", "Жм", "Сн" ],
	weekHeader: "Не",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.kk );

return datepicker.regional.kk;

} );

}).call(this);

(function(define) {
/* Khmer initialisation for the jQuery calendar extension. */
/* Written by Chandara Om (chandara.teacher@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.km = {
	closeText: "ធ្វើ​រួច",
	prevText: "មុន",
	nextText: "បន្ទាប់",
	currentText: "ថ្ងៃ​នេះ",
	monthNames: [ "មករា", "កុម្ភៈ", "មីនា", "មេសា", "ឧសភា", "មិថុនា",
	"កក្កដា", "សីហា", "កញ្ញា", "តុលា", "វិច្ឆិកា", "ធ្នូ" ],
	monthNamesShort: [ "មករា", "កុម្ភៈ", "មីនា", "មេសា", "ឧសភា", "មិថុនា",
	"កក្កដា", "សីហា", "កញ្ញា", "តុលា", "វិច្ឆិកា", "ធ្នូ" ],
	dayNames: [ "អាទិត្យ", "ចន្ទ", "អង្គារ", "ពុធ", "ព្រហស្បតិ៍", "សុក្រ", "សៅរ៍" ],
	dayNamesShort: [ "អា", "ច", "អ", "ពុ", "ព្រហ", "សុ", "សៅ" ],
	dayNamesMin: [ "អា", "ច", "អ", "ពុ", "ព្រហ", "សុ", "សៅ" ],
	weekHeader: "សប្ដាហ៍",
	dateFormat: "dd-mm-yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.km );

return datepicker.regional.km;

} );

}).call(this);

(function(define) {
/* Korean initialisation for the jQuery calendar extension. */
/* Written by DaeKwon Kang (ncrash.dk@gmail.com), Edited by Genie and Myeongjin Lee. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ko = {
	closeText: "닫기",
	prevText: "이전달",
	nextText: "다음달",
	currentText: "오늘",
	monthNames: [ "1월", "2월", "3월", "4월", "5월", "6월",
	"7월", "8월", "9월", "10월", "11월", "12월" ],
	monthNamesShort: [ "1월", "2월", "3월", "4월", "5월", "6월",
	"7월", "8월", "9월", "10월", "11월", "12월" ],
	dayNames: [ "일요일", "월요일", "화요일", "수요일", "목요일", "금요일", "토요일" ],
	dayNamesShort: [ "일", "월", "화", "수", "목", "금", "토" ],
	dayNamesMin: [ "일", "월", "화", "수", "목", "금", "토" ],
	weekHeader: "주",
	dateFormat: "yy. m. d.",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "년" };
datepicker.setDefaults( datepicker.regional.ko );

return datepicker.regional.ko;

} );

}).call(this);

(function(define) {
/* Kyrgyz (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Sergey Kartashov (ebishkek@yandex.ru). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ky = {
	closeText: "Жабуу",
	prevText: "Мур",
	nextText: "Кий",
	currentText: "Бүгүн",
	monthNames: [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
	"Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" ],
	monthNamesShort: [ "Янв", "Фев", "Мар", "Апр", "Май", "Июн",
	"Июл", "Авг", "Сен", "Окт", "Ноя", "Дек" ],
	dayNames: [ "жекшемби", "дүйшөмбү", "шейшемби", "шаршемби", "бейшемби", "жума", "ишемби" ],
	dayNamesShort: [ "жек", "дүй", "шей", "шар", "бей", "жум", "ише" ],
	dayNamesMin: [ "Жк", "Дш", "Шш", "Шр", "Бш", "Жм", "Иш" ],
	weekHeader: "Жум",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ""
};
datepicker.setDefaults( datepicker.regional.ky );

return datepicker.regional.ky;

} );

}).call(this);

(function(define) {
/* Luxembourgish initialisation for the jQuery UI date picker plugin. */
/* Written by Michel Weimerskirch <michel@weimerskirch.net> */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.lb = {
	closeText: "Fäerdeg",
	prevText: "Zréck",
	nextText: "Weider",
	currentText: "Haut",
	monthNames: [ "Januar", "Februar", "Mäerz", "Abrëll", "Mee", "Juni",
	"Juli", "August", "September", "Oktober", "November", "Dezember" ],
	monthNamesShort: [ "Jan", "Feb", "Mäe", "Abr", "Mee", "Jun",
	"Jul", "Aug", "Sep", "Okt", "Nov", "Dez" ],
	dayNames: [
		"Sonndeg",
		"Méindeg",
		"Dënschdeg",
		"Mëttwoch",
		"Donneschdeg",
		"Freideg",
		"Samschdeg"
	],
	dayNamesShort: [ "Son", "Méi", "Dën", "Mët", "Don", "Fre", "Sam" ],
	dayNamesMin: [ "So", "Mé", "Dë", "Më", "Do", "Fr", "Sa" ],
	weekHeader: "W",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.lb );

return datepicker.regional.lb;

} );

}).call(this);

(function(define) {
/* Lithuanian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* @author Arturas Paleicikas <arturas@avalon.lt> */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.lt = {
	closeText: "Uždaryti",
	prevText: "Atgal",
	nextText: "Pirmyn",
	currentText: "Šiandien",
	monthNames: [ "Sausis", "Vasaris", "Kovas", "Balandis", "Gegužė", "Birželis",
	"Liepa", "Rugpjūtis", "Rugsėjis", "Spalis", "Lapkritis", "Gruodis" ],
	monthNamesShort: [ "Sau", "Vas", "Kov", "Bal", "Geg", "Bir",
	"Lie", "Rugp", "Rugs", "Spa", "Lap", "Gru" ],
	dayNames: [
		"sekmadienis",
		"pirmadienis",
		"antradienis",
		"trečiadienis",
		"ketvirtadienis",
		"penktadienis",
		"šeštadienis"
	],
	dayNamesShort: [ "sek", "pir", "ant", "tre", "ket", "pen", "šeš" ],
	dayNamesMin: [ "Se", "Pr", "An", "Tr", "Ke", "Pe", "Še" ],
	weekHeader: "SAV",
	dateFormat: "yy-mm-dd",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.lt );

return datepicker.regional.lt;

} );

}).call(this);

(function(define) {
/* Latvian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* @author Arturas Paleicikas <arturas.paleicikas@metasite.net> */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.lv = {
	closeText: "Aizvērt",
	prevText: "Iepr.",
	nextText: "Nāk.",
	currentText: "Šodien",
	monthNames: [ "Janvāris", "Februāris", "Marts", "Aprīlis", "Maijs", "Jūnijs",
	"Jūlijs", "Augusts", "Septembris", "Oktobris", "Novembris", "Decembris" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Mai", "Jūn",
	"Jūl", "Aug", "Sep", "Okt", "Nov", "Dec" ],
	dayNames: [
		"svētdiena",
		"pirmdiena",
		"otrdiena",
		"trešdiena",
		"ceturtdiena",
		"piektdiena",
		"sestdiena"
	],
	dayNamesShort: [ "svt", "prm", "otr", "tre", "ctr", "pkt", "sst" ],
	dayNamesMin: [ "Sv", "Pr", "Ot", "Tr", "Ct", "Pk", "Ss" ],
	weekHeader: "Ned.",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.lv );

return datepicker.regional.lv;

} );

}).call(this);

(function(define) {
/* Macedonian i18n for the jQuery UI date picker plugin. */
/* Written by Stojce Slavkovski. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.mk = {
	closeText: "Затвори",
	prevText: "Претходна",
	nextText: "Следно",
	currentText: "Денес",
	monthNames: [ "Јануари", "Февруари", "Март", "Април", "Мај", "Јуни",
	"Јули", "Август", "Септември", "Октомври", "Ноември", "Декември" ],
	monthNamesShort: [ "Јан", "Фев", "Мар", "Апр", "Мај", "Јун",
	"Јул", "Авг", "Сеп", "Окт", "Ное", "Дек" ],
	dayNames: [ "Недела", "Понеделник", "Вторник", "Среда", "Четврток", "Петок", "Сабота" ],
	dayNamesShort: [ "Нед", "Пон", "Вто", "Сре", "Чет", "Пет", "Саб" ],
	dayNamesMin: [ "Не", "По", "Вт", "Ср", "Че", "Пе", "Са" ],
	weekHeader: "Сед",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.mk );

return datepicker.regional.mk;

} );

}).call(this);

(function(define) {
/* Malayalam (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Saji Nediyanchath (saji89@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ml = {
	closeText: "ശരി",
	prevText: "മുന്നത്തെ",
	nextText: "അടുത്തത് ",
	currentText: "ഇന്ന്",
	monthNames: [ "ജനുവരി", "ഫെബ്രുവരി", "മാര്‍ച്ച്", "ഏപ്രില്‍", "മേയ്", "ജൂണ്‍",
	"ജൂലൈ", "ആഗസ്റ്റ്", "സെപ്റ്റംബര്‍", "ഒക്ടോബര്‍", "നവംബര്‍", "ഡിസംബര്‍" ],
	monthNamesShort: [ "ജനു", "ഫെബ്", "മാര്‍", "ഏപ്രി", "മേയ്", "ജൂണ്‍",
	"ജൂലാ", "ആഗ", "സെപ്", "ഒക്ടോ", "നവം", "ഡിസ" ],
	dayNames: [ "ഞായര്‍", "തിങ്കള്‍", "ചൊവ്വ", "ബുധന്‍", "വ്യാഴം", "വെള്ളി", "ശനി" ],
	dayNamesShort: [ "ഞായ", "തിങ്ക", "ചൊവ്വ", "ബുധ", "വ്യാഴം", "വെള്ളി", "ശനി" ],
	dayNamesMin: [ "ഞാ", "തി", "ചൊ", "ബു", "വ്യാ", "വെ", "ശ" ],
	weekHeader: "ആ",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ml );

return datepicker.regional.ml;

} );

}).call(this);

(function(define) {
/* Malaysian initialisation for the jQuery UI date picker plugin. */
/* Written by Mohd Nawawi Mohamad Jamili (nawawi@ronggeng.net). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ms = {
	closeText: "Tutup",
	prevText: "Sebelum",
	nextText: "Selepas",
	currentText: "hari ini",
	monthNames: [ "Januari", "Februari", "Mac", "April", "Mei", "Jun",
	"Julai", "Ogos", "September", "Oktober", "November", "Disember" ],
	monthNamesShort: [ "Jan", "Feb", "Mac", "Apr", "Mei", "Jun",
	"Jul", "Ogo", "Sep", "Okt", "Nov", "Dis" ],
	dayNames: [ "Ahad", "Isnin", "Selasa", "Rabu", "Khamis", "Jumaat", "Sabtu" ],
	dayNamesShort: [ "Aha", "Isn", "Sel", "Rab", "kha", "Jum", "Sab" ],
	dayNamesMin: [ "Ah", "Is", "Se", "Ra", "Kh", "Ju", "Sa" ],
	weekHeader: "Mg",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ms );

return datepicker.regional.ms;

} );

}).call(this);

(function(define) {
/* Norwegian Bokmål initialisation for the jQuery UI date picker plugin. */
/* Written by Bjørn Johansen (post@bjornjohansen.no). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.nb = {
	closeText: "Lukk",
	prevText: "Forrige",
	nextText: "Neste",
	currentText: "I dag",
	monthNames: [
		"januar",
		"februar",
		"mars",
		"april",
		"mai",
		"juni",
		"juli",
		"august",
		"september",
		"oktober",
		"november",
		"desember"
	],
	monthNamesShort: [ "jan", "feb", "mar", "apr", "mai", "jun", "jul", "aug", "sep", "okt", "nov", "des" ],
	dayNamesShort: [ "søn", "man", "tir", "ons", "tor", "fre", "lør" ],
	dayNames: [ "søndag", "mandag", "tirsdag", "onsdag", "torsdag", "fredag", "lørdag" ],
	dayNamesMin: [ "sø", "ma", "ti", "on", "to", "fr", "lø" ],
	weekHeader: "Uke",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ""
};
datepicker.setDefaults( datepicker.regional.nb );

return datepicker.regional.nb;

} );

}).call(this);

(function(define) {
/* Dutch (Belgium) initialisation for the jQuery UI date picker plugin. */
/* David De Sloovere @DavidDeSloovere */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "nl-BE" ] = {
	closeText: "Sluiten",
	prevText: "Vorig",
	nextText: "Volgende",
	currentText: "Vandaag",
	monthNames: [ "januari", "februari", "maart", "april", "mei", "juni",
	"juli", "augustus", "september", "oktober", "november", "december" ],
	monthNamesShort: [ "jan", "feb", "mrt", "apr", "mei", "jun",
	"jul", "aug", "sep", "okt", "nov", "dec" ],
	dayNames: [ "zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag" ],
	dayNamesShort: [ "zon", "maa", "din", "woe", "don", "vri", "zat" ],
	dayNamesMin: [ "zo", "ma", "di", "wo", "do", "vr", "za" ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "nl-BE" ] );

return datepicker.regional[ "nl-BE" ];

} );

}).call(this);

(function(define) {
/* Dutch (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Mathias Bynens <https://mathiasbynens.be/> */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.nl = {
	closeText: "Sluiten",
	prevText: "Vorig",
	nextText: "Volgende",
	currentText: "Vandaag",
	monthNames: [ "januari", "februari", "maart", "april", "mei", "juni",
	"juli", "augustus", "september", "oktober", "november", "december" ],
	monthNamesShort: [ "jan", "feb", "mrt", "apr", "mei", "jun",
	"jul", "aug", "sep", "okt", "nov", "dec" ],
	dayNames: [ "zondag", "maandag", "dinsdag", "woensdag", "donderdag", "vrijdag", "zaterdag" ],
	dayNamesShort: [ "zon", "maa", "din", "woe", "don", "vri", "zat" ],
	dayNamesMin: [ "zo", "ma", "di", "wo", "do", "vr", "za" ],
	weekHeader: "Wk",
	dateFormat: "dd-mm-yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.nl );

return datepicker.regional.nl;

} );

}).call(this);

(function(define) {
/* Norwegian Nynorsk initialisation for the jQuery UI date picker plugin. */
/* Written by Bjørn Johansen (post@bjornjohansen.no). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.nn = {
	closeText: "Lukk",
	prevText: "Førre",
	nextText: "Neste",
	currentText: "I dag",
	monthNames: [
		"januar",
		"februar",
		"mars",
		"april",
		"mai",
		"juni",
		"juli",
		"august",
		"september",
		"oktober",
		"november",
		"desember"
	],
	monthNamesShort: [ "jan", "feb", "mar", "apr", "mai", "jun", "jul", "aug", "sep", "okt", "nov", "des" ],
	dayNamesShort: [ "sun", "mån", "tys", "ons", "tor", "fre", "lau" ],
	dayNames: [ "sundag", "måndag", "tysdag", "onsdag", "torsdag", "fredag", "laurdag" ],
	dayNamesMin: [ "su", "må", "ty", "on", "to", "fr", "la" ],
	weekHeader: "Veke",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ""
};
datepicker.setDefaults( datepicker.regional.nn );

return datepicker.regional.nn;

} );

}).call(this);

(function(define) {
/* Norwegian initialisation for the jQuery UI date picker plugin. */
/* Written by Naimdjon Takhirov (naimdjon@gmail.com). */

( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.no = {
	closeText: "Lukk",
	prevText: "Forrige",
	nextText: "Neste",
	currentText: "I dag",
	monthNames: [
		"januar",
		"februar",
		"mars",
		"april",
		"mai",
		"juni",
		"juli",
		"august",
		"september",
		"oktober",
		"november",
		"desember"
	],
	monthNamesShort: [ "jan", "feb", "mar", "apr", "mai", "jun", "jul", "aug", "sep", "okt", "nov", "des" ],
	dayNamesShort: [ "søn", "man", "tir", "ons", "tor", "fre", "lør" ],
	dayNames: [ "søndag", "mandag", "tirsdag", "onsdag", "torsdag", "fredag", "lørdag" ],
	dayNamesMin: [ "sø", "ma", "ti", "on", "to", "fr", "lø" ],
	weekHeader: "Uke",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: ""
};
datepicker.setDefaults( datepicker.regional.no );

return datepicker.regional.no;

} );

}).call(this);

(function(define) {
/* Polish initialisation for the jQuery UI date picker plugin. */
/* Written by Jacek Wysocki (jacek.wysocki@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.pl = {
	closeText: "Zamknij",
	prevText: "Poprzedni",
	nextText: "Następny",
	currentText: "Dziś",
	monthNames: [ "Styczeń", "Luty", "Marzec", "Kwiecień", "Maj", "Czerwiec",
	"Lipiec", "Sierpień", "Wrzesień", "Październik", "Listopad", "Grudzień" ],
	monthNamesShort: [ "Sty", "Lu", "Mar", "Kw", "Maj", "Cze",
	"Lip", "Sie", "Wrz", "Pa", "Lis", "Gru" ],
	dayNames: [ "Niedziela", "Poniedziałek", "Wtorek", "Środa", "Czwartek", "Piątek", "Sobota" ],
	dayNamesShort: [ "Nie", "Pn", "Wt", "Śr", "Czw", "Pt", "So" ],
	dayNamesMin: [ "N", "Pn", "Wt", "Śr", "Cz", "Pt", "So" ],
	weekHeader: "Tydz",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.pl );

return datepicker.regional.pl;

} );

}).call(this);

(function(define) {
/* Brazilian initialisation for the jQuery UI date picker plugin. */
/* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "pt-BR" ] = {
	closeText: "Fechar",
	prevText: "Anterior",
	nextText: "Próximo",
	currentText: "Hoje",
	monthNames: [ "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
	"Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" ],
	monthNamesShort: [ "Jan", "Fev", "Mar", "Abr", "Mai", "Jun",
	"Jul", "Ago", "Set", "Out", "Nov", "Dez" ],
	dayNames: [
		"Domingo",
		"Segunda-feira",
		"Terça-feira",
		"Quarta-feira",
		"Quinta-feira",
		"Sexta-feira",
		"Sábado"
	],
	dayNamesShort: [ "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb" ],
	dayNamesMin: [ "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb" ],
	weekHeader: "Sm",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "pt-BR" ] );

return datepicker.regional[ "pt-BR" ];

} );

}).call(this);

(function(define) {
/* Portuguese initialisation for the jQuery UI date picker plugin. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.pt = {
	closeText: "Fechar",
	prevText: "Anterior",
	nextText: "Seguinte",
	currentText: "Hoje",
	monthNames: [ "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
	"Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" ],
	monthNamesShort: [ "Jan", "Fev", "Mar", "Abr", "Mai", "Jun",
	"Jul", "Ago", "Set", "Out", "Nov", "Dez" ],
	dayNames: [
		"Domingo",
		"Segunda-feira",
		"Terça-feira",
		"Quarta-feira",
		"Quinta-feira",
		"Sexta-feira",
		"Sábado"
	],
	dayNamesShort: [ "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb" ],
	dayNamesMin: [ "Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sáb" ],
	weekHeader: "Sem",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.pt );

return datepicker.regional.pt;

} );

}).call(this);

(function(define) {
/* Romansh initialisation for the jQuery UI date picker plugin. */
/* Written by Yvonne Gienal (yvonne.gienal@educa.ch). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.rm = {
	closeText: "Serrar",
	prevText: "Suandant",
	nextText: "Precedent",
	currentText: "Actual",
	monthNames: [
		"Schaner",
		"Favrer",
		"Mars",
		"Avrigl",
		"Matg",
		"Zercladur",
		"Fanadur",
		"Avust",
		"Settember",
		"October",
		"November",
		"December"
	],
	monthNamesShort: [
		"Scha",
		"Fev",
		"Mar",
		"Avr",
		"Matg",
		"Zer",
		"Fan",
		"Avu",
		"Sett",
		"Oct",
		"Nov",
		"Dec"
	],
	dayNames: [ "Dumengia", "Glindesdi", "Mardi", "Mesemna", "Gievgia", "Venderdi", "Sonda" ],
	dayNamesShort: [ "Dum", "Gli", "Mar", "Mes", "Gie", "Ven", "Som" ],
	dayNamesMin: [ "Du", "Gl", "Ma", "Me", "Gi", "Ve", "So" ],
	weekHeader: "emna",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.rm );

return datepicker.regional.rm;

} );

}).call(this);

(function(define) {
/* Romanian initialisation for the jQuery UI date picker plugin.
 *
 * Written by Edmond L. (ll_edmond@walla.com)
 * and Ionut G. Stan (ionut.g.stan@gmail.com)
 */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ro = {
	closeText: "Închide",
	prevText: "Luna precedentă",
	nextText: "Luna următoare ",
	currentText: "Azi",
	monthNames: [ "Ianuarie", "Februarie", "Martie", "Aprilie", "Mai", "Iunie",
	"Iulie", "August", "Septembrie", "Octombrie", "Noiembrie", "Decembrie" ],
	monthNamesShort: [ "Ian", "Feb", "Mar", "Apr", "Mai", "Iun",
	"Iul", "Aug", "Sep", "Oct", "Nov", "Dec" ],
	dayNames: [ "Duminică", "Luni", "Marţi", "Miercuri", "Joi", "Vineri", "Sâmbătă" ],
	dayNamesShort: [ "Dum", "Lun", "Mar", "Mie", "Joi", "Vin", "Sâm" ],
	dayNamesMin: [ "Du", "Lu", "Ma", "Mi", "Jo", "Vi", "Sâ" ],
	weekHeader: "Săpt",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ro );

return datepicker.regional.ro;

} );

}).call(this);

(function(define) {
/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Andrew Stromnov (stromnov@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ru = {
	closeText: "Закрыть",
	prevText: "Пред",
	nextText: "След",
	currentText: "Сегодня",
	monthNames: [ "Январь", "Февраль", "Март", "Апрель", "Май", "Июнь",
	"Июль", "Август", "Сентябрь", "Октябрь", "Ноябрь", "Декабрь" ],
	monthNamesShort: [ "Янв", "Фев", "Мар", "Апр", "Май", "Июн",
	"Июл", "Авг", "Сен", "Окт", "Ноя", "Дек" ],
	dayNames: [ "воскресенье", "понедельник", "вторник", "среда", "четверг", "пятница", "суббота" ],
	dayNamesShort: [ "вск", "пнд", "втр", "срд", "чтв", "птн", "сбт" ],
	dayNamesMin: [ "Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
	weekHeader: "Нед",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ru );

return datepicker.regional.ru;

} );

}).call(this);

(function(define) {
/* Slovak initialisation for the jQuery UI date picker plugin. */
/* Written by Vojtech Rinik (vojto@hmm.sk). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.sk = {
	closeText: "Zavrieť",
	prevText: "Predchádzajúci",
	nextText: "Nasledujúci",
	currentText: "Dnes",
	monthNames: [ "január", "február", "marec", "apríl", "máj", "jún",
	"júl", "august", "september", "október", "november", "december" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Máj", "Jún",
	"Júl", "Aug", "Sep", "Okt", "Nov", "Dec" ],
	dayNames: [ "nedeľa", "pondelok", "utorok", "streda", "štvrtok", "piatok", "sobota" ],
	dayNamesShort: [ "Ned", "Pon", "Uto", "Str", "Štv", "Pia", "Sob" ],
	dayNamesMin: [ "Ne", "Po", "Ut", "St", "Št", "Pia", "So" ],
	weekHeader: "Ty",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.sk );

return datepicker.regional.sk;

} );

}).call(this);

(function(define) {
/* Slovenian initialisation for the jQuery UI date picker plugin. */
/* Written by Jaka Jancar (jaka@kubje.org). */
/* c = č, s = š z = ž C = Č S = Š Z = Ž */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.sl = {
	closeText: "Zapri",
	prevText: "Prejšnji",
	nextText: "Naslednji",
	currentText: "Trenutni",
	monthNames: [ "Januar", "Februar", "Marec", "April", "Maj", "Junij",
	"Julij", "Avgust", "September", "Oktober", "November", "December" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Maj", "Jun",
	"Jul", "Avg", "Sep", "Okt", "Nov", "Dec" ],
	dayNames: [ "Nedelja", "Ponedeljek", "Torek", "Sreda", "Četrtek", "Petek", "Sobota" ],
	dayNamesShort: [ "Ned", "Pon", "Tor", "Sre", "Čet", "Pet", "Sob" ],
	dayNamesMin: [ "Ne", "Po", "To", "Sr", "Če", "Pe", "So" ],
	weekHeader: "Teden",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.sl );

return datepicker.regional.sl;

} );

}).call(this);

(function(define) {
/* Albanian initialisation for the jQuery UI date picker plugin. */
/* Written by Flakron Bytyqi (flakron@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.sq = {
	closeText: "mbylle",
	prevText: "mbrapa",
	nextText: "Përpara",
	currentText: "sot",
	monthNames: [ "Janar", "Shkurt", "Mars", "Prill", "Maj", "Qershor",
	"Korrik", "Gusht", "Shtator", "Tetor", "Nëntor", "Dhjetor" ],
	monthNamesShort: [ "Jan", "Shk", "Mar", "Pri", "Maj", "Qer",
	"Kor", "Gus", "Sht", "Tet", "Nën", "Dhj" ],
	dayNames: [ "E Diel", "E Hënë", "E Martë", "E Mërkurë", "E Enjte", "E Premte", "E Shtune" ],
	dayNamesShort: [ "Di", "Hë", "Ma", "Më", "En", "Pr", "Sh" ],
	dayNamesMin: [ "Di", "Hë", "Ma", "Më", "En", "Pr", "Sh" ],
	weekHeader: "Ja",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.sq );

return datepicker.regional.sq;

} );

}).call(this);

(function(define) {
/* Serbian i18n for the jQuery UI date picker plugin. */
/* Written by Dejan Dimić. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "sr-SR" ] = {
	closeText: "Zatvori",
	prevText: "Prethodno",
	nextText: "Sljedeći",
	currentText: "Danas",
	monthNames: [ "Januar", "Februar", "Mart", "April", "Maj", "Jun",
	"Jul", "Avgust", "Septembar", "Oktobar", "Novembar", "Decembar" ],
	monthNamesShort: [ "Jan", "Feb", "Mar", "Apr", "Maj", "Jun",
	"Jul", "Avg", "Sep", "Okt", "Nov", "Dec" ],
	dayNames: [ "Nedelja", "Ponedeljak", "Utorak", "Sreda", "Četvrtak", "Petak", "Subota" ],
	dayNamesShort: [ "Ned", "Pon", "Uto", "Sre", "Čet", "Pet", "Sub" ],
	dayNamesMin: [ "Ne", "Po", "Ut", "Sr", "Če", "Pe", "Su" ],
	weekHeader: "Sed",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional[ "sr-SR" ] );

return datepicker.regional[ "sr-SR" ];

} );

}).call(this);

(function(define) {
/* Serbian i18n for the jQuery UI date picker plugin. */
/* Written by Dejan Dimić. */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.sr = {
	closeText: "Затвори",
	prevText: "Претходна",
	nextText: "Следећи",
	currentText: "Данас",
	monthNames: [ "Јануар", "Фебруар", "Март", "Април", "Мај", "Јун",
	"Јул", "Август", "Септембар", "Октобар", "Новембар", "Децембар" ],
	monthNamesShort: [ "Јан", "Феб", "Мар", "Апр", "Мај", "Јун",
	"Јул", "Авг", "Сеп", "Окт", "Нов", "Дец" ],
	dayNames: [ "Недеља", "Понедељак", "Уторак", "Среда", "Четвртак", "Петак", "Субота" ],
	dayNamesShort: [ "Нед", "Пон", "Уто", "Сре", "Чет", "Пет", "Суб" ],
	dayNamesMin: [ "Не", "По", "Ут", "Ср", "Че", "Пе", "Су" ],
	weekHeader: "Сед",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.sr );

return datepicker.regional.sr;

} );

}).call(this);

(function(define) {
/* Swedish initialisation for the jQuery UI date picker plugin. */
/* Written by Anders Ekdahl ( anders@nomadiz.se). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.sv = {
	closeText: "Stäng",
	prevText: "Förra",
	nextText: "Nästa",
	currentText: "Idag",
	monthNames: [ "januari", "februari", "mars", "april", "maj", "juni",
	"juli", "augusti", "september", "oktober", "november", "december" ],
	monthNamesShort: [ "jan.", "feb.", "mars", "apr.", "maj", "juni",
	"juli", "aug.", "sep.", "okt.", "nov.", "dec." ],
	dayNamesShort: [ "sön", "mån", "tis", "ons", "tor", "fre", "lör" ],
	dayNames: [ "söndag", "måndag", "tisdag", "onsdag", "torsdag", "fredag", "lördag" ],
	dayNamesMin: [ "sö", "må", "ti", "on", "to", "fr", "lö" ],
	weekHeader: "Ve",
	dateFormat: "yy-mm-dd",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.sv );

return datepicker.regional.sv;

} );

}).call(this);

(function(define) {
/* Tamil (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by S A Sureshkumar (saskumar@live.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.ta = {
	closeText: "மூடு",
	prevText: "முன்னையது",
	nextText: "அடுத்தது",
	currentText: "இன்று",
	monthNames: [ "தை", "மாசி", "பங்குனி", "சித்திரை", "வைகாசி", "ஆனி",
	"ஆடி", "ஆவணி", "புரட்டாசி", "ஐப்பசி", "கார்த்திகை", "மார்கழி" ],
	monthNamesShort: [ "தை", "மாசி", "பங்", "சித்", "வைகா", "ஆனி",
	"ஆடி", "ஆவ", "புர", "ஐப்", "கார்", "மார்" ],
	dayNames: [
		"ஞாயிற்றுக்கிழமை",
		"திங்கட்கிழமை",
		"செவ்வாய்க்கிழமை",
		"புதன்கிழமை",
		"வியாழக்கிழமை",
		"வெள்ளிக்கிழமை",
		"சனிக்கிழமை"
	],
	dayNamesShort: [
		"ஞாயிறு",
		"திங்கள்",
		"செவ்வாய்",
		"புதன்",
		"வியாழன்",
		"வெள்ளி",
		"சனி"
	],
	dayNamesMin: [ "ஞா", "தி", "செ", "பு", "வி", "வெ", "ச" ],
	weekHeader: "Не",
	dateFormat: "dd/mm/yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.ta );

return datepicker.regional.ta;

} );

}).call(this);

(function(define) {
/* Thai initialisation for the jQuery UI date picker plugin. */
/* Written by pipo (pipo@sixhead.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.th = {
	closeText: "ปิด",
	prevText: "ย้อน",
	nextText: "ถัดไป",
	currentText: "วันนี้",
	monthNames: [ "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
	"กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม" ],
	monthNamesShort: [ "ม.ค.", "ก.พ.", "มี.ค.", "เม.ย.", "พ.ค.", "มิ.ย.",
	"ก.ค.", "ส.ค.", "ก.ย.", "ต.ค.", "พ.ย.", "ธ.ค." ],
	dayNames: [ "อาทิตย์", "จันทร์", "อังคาร", "พุธ", "พฤหัสบดี", "ศุกร์", "เสาร์" ],
	dayNamesShort: [ "อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส." ],
	dayNamesMin: [ "อา.", "จ.", "อ.", "พ.", "พฤ.", "ศ.", "ส." ],
	weekHeader: "Wk",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.th );

return datepicker.regional.th;

} );

}).call(this);

(function(define) {
/* Tajiki (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Abdurahmon Saidov (saidovab@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.tj = {
	closeText: "Идома",
	prevText: "Қафо",
	nextText: "Пеш",
	currentText: "Имрӯз",
	monthNames: [ "Январ", "Феврал", "Март", "Апрел", "Май", "Июн",
	"Июл", "Август", "Сентябр", "Октябр", "Ноябр", "Декабр" ],
	monthNamesShort: [ "Янв", "Фев", "Мар", "Апр", "Май", "Июн",
	"Июл", "Авг", "Сен", "Окт", "Ноя", "Дек" ],
	dayNames: [ "якшанбе", "душанбе", "сешанбе", "чоршанбе", "панҷшанбе", "ҷумъа", "шанбе" ],
	dayNamesShort: [ "якш", "душ", "сеш", "чор", "пан", "ҷум", "шан" ],
	dayNamesMin: [ "Як", "Дш", "Сш", "Чш", "Пш", "Ҷм", "Шн" ],
	weekHeader: "Хф",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.tj );

return datepicker.regional.tj;

} );

}).call(this);

(function(define) {
/* Turkish initialisation for the jQuery UI date picker plugin. */
/* Written by Izzet Emre Erkan (kara@karalamalar.net). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.tr = {
	closeText: "kapat",
	prevText: "geri",
	nextText: "ileri",
	currentText: "bugün",
	monthNames: [ "Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran",
	"Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık" ],
	monthNamesShort: [ "Oca", "Şub", "Mar", "Nis", "May", "Haz",
	"Tem", "Ağu", "Eyl", "Eki", "Kas", "Ara" ],
	dayNames: [ "Pazar", "Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi" ],
	dayNamesShort: [ "Pz", "Pt", "Sa", "Ça", "Pe", "Cu", "Ct" ],
	dayNamesMin: [ "Pz", "Pt", "Sa", "Ça", "Pe", "Cu", "Ct" ],
	weekHeader: "Hf",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.tr );

return datepicker.regional.tr;

} );

}).call(this);

(function(define) {
/* Ukrainian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Maxim Drogobitskiy (maxdao@gmail.com). */
/* Corrected by Igor Milla (igor.fsp.milla@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.uk = {
	closeText: "Закрити",
	prevText: "Попередній",
	nextText: "найближчий",
	currentText: "Сьогодні",
	monthNames: [ "Січень", "Лютий", "Березень", "Квітень", "Травень", "Червень",
	"Липень", "Серпень", "Вересень", "Жовтень", "Листопад", "Грудень" ],
	monthNamesShort: [ "Січ", "Лют", "Бер", "Кві", "Тра", "Чер",
	"Лип", "Сер", "Вер", "Жов", "Лис", "Гру" ],
	dayNames: [ "неділя", "понеділок", "вівторок", "середа", "четвер", "п’ятниця", "субота" ],
	dayNamesShort: [ "нед", "пнд", "вів", "срд", "чтв", "птн", "сбт" ],
	dayNamesMin: [ "Нд", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб" ],
	weekHeader: "Тиж",
	dateFormat: "dd.mm.yy",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.uk );

return datepicker.regional.uk;

} );

}).call(this);

(function(define) {
/* Vietnamese initialisation for the jQuery UI date picker plugin. */
/* Translated by Le Thanh Huy (lthanhhuy@cit.ctu.edu.vn). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional.vi = {
	closeText: "Đóng",
	prevText: "Trước",
	nextText: "Tiếp",
	currentText: "Hôm nay",
	monthNames: [ "Tháng Một", "Tháng Hai", "Tháng Ba", "Tháng Tư", "Tháng Năm", "Tháng Sáu",
	"Tháng Bảy", "Tháng Tám", "Tháng Chín", "Tháng Mười", "Tháng Mười Một", "Tháng Mười Hai" ],
	monthNamesShort: [ "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6",
	"Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12" ],
	dayNames: [ "Chủ Nhật", "Thứ Hai", "Thứ Ba", "Thứ Tư", "Thứ Năm", "Thứ Sáu", "Thứ Bảy" ],
	dayNamesShort: [ "CN", "T2", "T3", "T4", "T5", "T6", "T7" ],
	dayNamesMin: [ "CN", "T2", "T3", "T4", "T5", "T6", "T7" ],
	weekHeader: "Tu",
	dateFormat: "dd/mm/yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: false,
	yearSuffix: "" };
datepicker.setDefaults( datepicker.regional.vi );

return datepicker.regional.vi;

} );

}).call(this);

(function(define) {
/* Chinese initialisation for the jQuery UI date picker plugin. */
/* Written by Cloudream (cloudream@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "zh-CN" ] = {
	closeText: "关闭",
	prevText: "上月",
	nextText: "下月",
	currentText: "今天",
	monthNames: [ "一月", "二月", "三月", "四月", "五月", "六月",
	"七月", "八月", "九月", "十月", "十一月", "十二月" ],
	monthNamesShort: [ "一月", "二月", "三月", "四月", "五月", "六月",
	"七月", "八月", "九月", "十月", "十一月", "十二月" ],
	dayNames: [ "星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六" ],
	dayNamesShort: [ "周日", "周一", "周二", "周三", "周四", "周五", "周六" ],
	dayNamesMin: [ "日", "一", "二", "三", "四", "五", "六" ],
	weekHeader: "周",
	dateFormat: "yy-mm-dd",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "年" };
datepicker.setDefaults( datepicker.regional[ "zh-CN" ] );

return datepicker.regional[ "zh-CN" ];

} );

}).call(this);

(function(define) {
/* Chinese initialisation for the jQuery UI date picker plugin. */
/* Written by SCCY (samuelcychan@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "zh-HK" ] = {
	closeText: "關閉",
	prevText: "上月",
	nextText: "下月",
	currentText: "今天",
	monthNames: [ "一月", "二月", "三月", "四月", "五月", "六月",
	"七月", "八月", "九月", "十月", "十一月", "十二月" ],
	monthNamesShort: [ "一月", "二月", "三月", "四月", "五月", "六月",
	"七月", "八月", "九月", "十月", "十一月", "十二月" ],
	dayNames: [ "星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六" ],
	dayNamesShort: [ "周日", "周一", "周二", "周三", "周四", "周五", "周六" ],
	dayNamesMin: [ "日", "一", "二", "三", "四", "五", "六" ],
	weekHeader: "周",
	dateFormat: "dd-mm-yy",
	firstDay: 0,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "年" };
datepicker.setDefaults( datepicker.regional[ "zh-HK" ] );

return datepicker.regional[ "zh-HK" ];

} );

}).call(this);

(function(define) {
/* Chinese initialisation for the jQuery UI date picker plugin. */
/* Written by Ressol (ressol@gmail.com). */
( function( factory ) {
	"use strict";

	if ( typeof define === "function" && define.amd ) {

		// AMD. Register as an anonymous module.
		define( [ "../widgets/datepicker" ], factory );
	} else {

		// Browser globals
		factory( jQuery.datepicker );
	}
} )( function( datepicker ) {
"use strict";

datepicker.regional[ "zh-TW" ] = {
	closeText: "關閉",
	prevText: "上個月",
	nextText: "下個月",
	currentText: "今天",
	monthNames: [ "一月", "二月", "三月", "四月", "五月", "六月",
	"七月", "八月", "九月", "十月", "十一月", "十二月" ],
	monthNamesShort: [ "一月", "二月", "三月", "四月", "五月", "六月",
	"七月", "八月", "九月", "十月", "十一月", "十二月" ],
	dayNames: [ "星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六" ],
	dayNamesShort: [ "週日", "週一", "週二", "週三", "週四", "週五", "週六" ],
	dayNamesMin: [ "日", "一", "二", "三", "四", "五", "六" ],
	weekHeader: "週",
	dateFormat: "yy/mm/dd",
	firstDay: 1,
	isRTL: false,
	showMonthAfterYear: true,
	yearSuffix: "年" };
datepicker.setDefaults( datepicker.regional[ "zh-TW" ] );

return datepicker.regional[ "zh-TW" ];

} );

}).call(this);
