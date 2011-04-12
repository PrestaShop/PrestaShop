/* Arabic Translation for jQuery UI date picker plugin. */
/* Khaled Al Horani -- koko.dw@gmail.com */
/* خالد الحوراني -- koko.dw@gmail.com */
/* NOTE: monthNames are the original months names and they are the Arabic names, not the new months name فبراير - يناير and there isn't any Arabic roots for these months */
jQuery(function($){
	$.datepicker.regional['ar'] = {
		clearText: 'مسح', clearStatus: 'امسح التاريخ الحالي',
		closeText: 'إغلاق', closeStatus: 'إغلاق بدون حفظ',
		prevText: '&#x3c;السابق', prevStatus: 'عرض الشهر السابق',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'التالي&#x3e;', nextStatus: 'عرض الشهر القادم',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'اليوم', currentStatus: 'عرض الشهر الحالي',
		monthNames: ['كانون الثاني', 'شباط', 'آذار', 'نيسان', 'آذار', 'حزيران',
		'تموز', 'آب', 'أيلول',	'تشرين الأول', 'تشرين الثاني', 'كانون الأول'],
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		monthStatus: 'عرض شهر آخر', yearStatus: 'عرض سنة آخرى',
		weekHeader: 'أسبوع', weekStatus: 'أسبوع السنة',
		dayNames: ['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة'],
		dayNamesShort: ['سبت', 'أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة'],
		dayNamesMin: ['سبت', 'أحد', 'اثنين', 'ثلاثاء', 'أربعاء', 'خميس', 'جمعة'],
		dayStatus: 'اختر DD لليوم الأول من الأسبوع', dateStatus: 'اختر D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: 'اختر يوم', isRTL: true};
	$.datepicker.setDefaults($.datepicker.regional['ar']);
});﻿/* Bulgarian initialisation for the jQuery UI date picker plugin. */
/* Written by Stoyan Kyosev (http://svest.org). */
jQuery(function($){
    $.datepicker.regional['bg'] = {
		clearText: 'изчисти', clearStatus: 'изчисти актуалната дата',
        closeText: 'затвори', closeStatus: 'затвори без промени',
        prevText: '&#x3c;назад', prevStatus: 'покажи последния месец',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
        nextText: 'напред&#x3e;', nextStatus: 'покажи следващия месец',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
        currentText: 'днес', currentStatus: '',
        monthNames: ['Януари','Февруари','Март','Април','Май','Юни',
        'Юли','Август','Септември','Октомври','Ноември','Декември'],
        monthNamesShort: ['Яну','Фев','Мар','Апр','Май','Юни',
        'Юли','Авг','Сеп','Окт','Нов','Дек'],
        monthStatus: 'покажи друг месец', yearStatus: 'покажи друга година',
        weekHeader: 'Wk', weekStatus: 'седмица от месеца',
        dayNames: ['Неделя','Понеделник','Вторник','Сряда','Четвъртък','Петък','Събота'],
        dayNamesShort: ['Нед','Пон','Вто','Сря','Чет','Пет','Съб'],
        dayNamesMin: ['Не','По','Вт','Ср','Че','Пе','Съ'],
        dayStatus: 'Сложи DD като първи ден от седмицата', dateStatus: 'Избери D, M d',
        dateFormat: 'dd.mm.yy', firstDay: 1,
        initStatus: 'Избери дата', isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['bg']);
});
/* Inicialitzaci� en catal� per a l'extenci� 'calendar' per jQuery. */
/* Writers: (joan.leon@gmail.com). */
jQuery(function($){
	$.datepicker.regional['ca'] = {
		clearText: 'Netejar', clearStatus: '',
		closeText: 'Tancar', closeStatus: '',
		prevText: '&#x3c;Ant', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Seg&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Avui', currentStatus: '',
		monthNames: ['Gener','Febrer','Mar&ccedil;','Abril','Maig','Juny',
		'Juliol','Agost','Setembre','Octubre','Novembre','Desembre'],
		monthNamesShort: ['Gen','Feb','Mar','Abr','Mai','Jun',
		'Jul','Ago','Set','Oct','Nov','Des'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['Diumenge','Dilluns','Dimarts','Dimecres','Dijous','Divendres','Dissabte'],
		dayNamesShort: ['Dug','Dln','Dmt','Dmc','Djs','Dvn','Dsb'],
		dayNamesMin: ['Dg','Dl','Dt','Dc','Dj','Dv','Ds'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'mm/dd/yy', firstDay: 0,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['ca']);
});﻿/* Czech initialisation for the jQuery UI date picker plugin. */
/* Written by Tomas Muller (tomas@tomas-muller.net). */
jQuery(function($){
	$.datepicker.regional['cs'] = {
		clearText: 'Vymazat', clearStatus: 'Vymaže zadané datum',
		closeText: 'Zavřít',  closeStatus: 'Zavře kalendář beze změny',
		prevText: '&#x3c;Dříve', prevStatus: 'Přejít na předchozí měsí',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Později&#x3e;', nextStatus: 'Přejít na další měsíc',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Nyní', currentStatus: 'Přejde na aktuální měsíc',
		monthNames: ['leden','únor','březen','duben','květen','červen',
        'červenec','srpen','září','říjen','listopad','prosinec'],
		monthNamesShort: ['led','úno','bře','dub','kvě','čer',
		'čvc','srp','zář','říj','lis','pro'],
		monthStatus: 'Přejít na jiný měsíc', yearStatus: 'Přejít na jiný rok',
		weekHeader: 'Týd', weekStatus: 'Týden v roce',
		dayNames: ['neděle', 'pondělí', 'úterý', 'středa', 'čtvrtek', 'pátek', 'sobota'],
		dayNamesShort: ['ne', 'po', 'út', 'st', 'čt', 'pá', 'so'],
		dayNamesMin: ['ne','po','út','st','čt','pá','so'],
		dayStatus: 'Nastavit DD jako první den v týdnu', dateStatus: '\'Vyber\' DD, M d',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: 'Vyberte datum', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['cs']);
});
﻿/* Danish initialisation for the jQuery UI date picker plugin. */
/* Written by Jan Christensen ( deletestuff@gmail.com). */
jQuery(function($){
    $.datepicker.regional['da'] = {
		clearText: 'Nulstil', clearStatus: 'Nulstil den aktuelle dato',
		closeText: 'Luk', closeStatus: 'Luk uden ændringer',
        prevText: '&#x3c;Forrige', prevStatus: 'Vis forrige måned',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Næste&#x3e;', nextStatus: 'Vis næste måned',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Idag', currentStatus: 'Vis aktuel måned',
        monthNames: ['Januar','Februar','Marts','April','Maj','Juni',
        'Juli','August','September','Oktober','November','December'],
        monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun',
        'Jul','Aug','Sep','Okt','Nov','Dec'],
		monthStatus: 'Vis en anden måned', yearStatus: 'Vis et andet år',
		weekHeader: 'Uge', weekStatus: 'Årets uge',
		dayNames: ['Søndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Lørdag'],
		dayNamesShort: ['Søn','Man','Tir','Ons','Tor','Fre','Lør'],
		dayNamesMin: ['Sø','Ma','Ti','On','To','Fr','Lø'],
		dayStatus: 'Sæt DD som første ugedag', dateStatus: 'Vælg D, M d',
        dateFormat: 'dd-mm-yy', firstDay: 0,
		initStatus: 'Vælg en dato', isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['da']);
});
﻿/* German initialisation for the jQuery UI date picker plugin. */
/* Written by Milian Wolff (mail@milianw.de). */
jQuery(function($){
	$.datepicker.regional['de'] = {
		clearText: 'löschen', clearStatus: 'aktuelles Datum löschen',
		closeText: 'schließen', closeStatus: 'ohne Änderungen schließen',
		prevText: '&#x3c;zurück', prevStatus: 'letzten Monat zeigen',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Vor&#x3e;', nextStatus: 'nächsten Monat zeigen',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'heute', currentStatus: '',
		monthNames: ['Januar','Februar','März','April','Mai','Juni',
		'Juli','August','September','Oktober','November','Dezember'],
		monthNamesShort: ['Jan','Feb','Mär','Apr','Mai','Jun',
		'Jul','Aug','Sep','Okt','Nov','Dez'],
		monthStatus: 'anderen Monat anzeigen', yearStatus: 'anderes Jahr anzeigen',
		weekHeader: 'Wo', weekStatus: 'Woche des Monats',
		dayNames: ['Sonntag','Montag','Dienstag','Mittwoch','Donnerstag','Freitag','Samstag'],
		dayNamesShort: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		dayNamesMin: ['So','Mo','Di','Mi','Do','Fr','Sa'],
		dayStatus: 'Setze DD als ersten Wochentag', dateStatus: 'Wähle D, M d',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: 'Wähle ein Datum', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['de']);
});
﻿/* Esperanto initialisation for the jQuery UI date picker plugin. */
/* Written by Olivier M. (olivierweb@ifrance.com). */
jQuery(function($){
	$.datepicker.regional['eo'] = {
		clearText: 'Vakigi', clearStatus: '',
		closeText: 'Fermi', closeStatus: 'Fermi sen modifi',
		prevText: '&lt;Anta', prevStatus: 'Vidi la antaŭan monaton',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Sekv&gt;', nextStatus: 'Vidi la sekvan monaton',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Nuna', currentStatus: 'Vidi la nunan monaton',
		monthNames: ['Januaro','Februaro','Marto','Aprilo','Majo','Junio',
		'Julio','Aŭgusto','Septembro','Oktobro','Novembro','Decembro'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun',
		'Jul','Aŭg','Sep','Okt','Nov','Dec'],
		monthStatus: 'Vidi alian monaton', yearStatus: 'Vidi alian jaron',
		weekHeader: 'Sb', weekStatus: '',
		dayNames: ['Dimanĉo','Lundo','Mardo','Merkredo','Ĵaŭdo','Vendredo','Sabato'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Ĵaŭ','Ven','Sab'],
		dayNamesMin: ['Di','Lu','Ma','Me','Ĵa','Ve','Sa'],
		dayStatus: 'Uzi DD kiel unua tago de la semajno', dateStatus: 'Elekti DD, MM d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: 'Elekti la daton', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['eo']);
});
/* Inicializaci�n en espa�ol para la extensi�n 'UI date picker' para jQuery. */
/* Traducido por Vester (xvester@gmail.com). */
jQuery(function($){
	$.datepicker.regional['es'] = {
		clearText: 'Limpiar', clearStatus: '',
		closeText: 'Cerrar', closeStatus: '',
		prevText: '&#x3c;Ant', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Sig&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Hoy', currentStatus: '',
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
		'Jul','Ago','Sep','Oct','Nov','Dic'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['Domingo','Lunes','Martes','Mi&eacute;rcoles','Jueves','Viernes','S&aacute;bado'],
		dayNamesShort: ['Dom','Lun','Mar','Mi&eacute;','Juv','Vie','S&aacute;b'],
		dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','S&aacute;'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['es']);
});﻿/* Persian (Farsi) Translation for the jQuery UI date picker plugin. */
/* Javad Mowlanezhad -- jmowla@gmail.com */
/* Jalali calendar should supported soon! (Its implemented but I have to test it) */
jQuery(function($) {
	$.datepicker.regional['fa'] = {
		clearText: 'حذف تاريخ', clearStatus: 'پاک کردن تاريخ جاري',
		closeText: 'بستن', closeStatus: 'بستن بدون اعمال تغييرات',
		prevText: '&#x3c;قبلي', prevStatus: 'نمايش ماه قبل',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'بعدي&#x3e;', nextStatus: 'نمايش ماه بعد',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'امروز', currentStatus: 'نمايش ماه جاري',
		monthNames: ['فروردين','ارديبهشت','خرداد','تير','مرداد','شهريور',
		'مهر','آبان','آذر','دي','بهمن','اسفند'],
		monthNamesShort: ['1','2','3','4','5','6','7','8','9','10','11','12'],
		monthStatus: 'نمايش ماه متفاوت', yearStatus: 'نمايش سال متفاوت',
		weekHeader: 'هف', weekStatus: 'هفتهِ سال',
		dayNames: ['يکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه','شنبه'],
		dayNamesShort: ['ي','د','س','چ','پ','ج', 'ش'],
		dayNamesMin: ['ي','د','س','چ','پ','ج', 'ش'],
		dayStatus: 'قبول DD بعنوان اولين روز هفته', dateStatus: 'انتخاب D, M d',
		dateFormat: 'yy/mm/dd', firstDay: 6,
		initStatus: 'انتخاب تاريخ', isRTL: true};
	$.datepicker.setDefaults($.datepicker.regional['fa']);
});/* Finnish initialisation for the jQuery UI date picker plugin. */
/* Written by Harri Kilpi� (harrikilpio@gmail.com). */
jQuery(function($){
    $.datepicker.regional['fi'] = {
		clearText: 'Tyhjenn&auml;', clearStatus: '',
		closeText: 'Sulje', closeStatus: '',
		prevText: '&laquo;Edellinen', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Seuraava&raquo;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'T&auml;n&auml;&auml;n', currentStatus: '',
        monthNames: ['Tammikuu','Helmikuu','Maaliskuu','Huhtikuu','Toukokuu','Kes&auml;kuu',
        'Hein&auml;kuu','Elokuu','Syyskuu','Lokakuu','Marraskuu','Joulukuu'],
        monthNamesShort: ['Tammi','Helmi','Maalis','Huhti','Touko','Kes&auml;',
        'Hein&auml;','Elo','Syys','Loka','Marras','Joulu'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Vk', weekStatus: '',
		dayNamesShort: ['Su','Ma','Ti','Ke','To','Pe','Su'],
		dayNames: ['Sunnuntai','Maanantai','Tiistai','Keskiviikko','Torstai','Perjantai','Lauantai'],
		dayNamesMin: ['Su','Ma','Ti','Ke','To','Pe','La'],
		dayStatus: 'DD', dateStatus: 'D, M d',
        dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: '', isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['fi']);
});
﻿/* French initialisation for the jQuery UI date picker plugin. */
/* Written by Keith Wood (kbwood@virginbroadband.com.au) and Stéphane Nahmani (sholby@sholby.net). */
jQuery(function($){
	$.datepicker.regional['fr'] = {
		clearText: 'Effacer', clearStatus: 'Effacer la date sélectionnée',
		closeText: 'Fermer', closeStatus: 'Fermer sans modifier',
		prevText: '&#x3c;Préc', prevStatus: 'Voir le mois précédent',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Suiv&#x3e;', nextStatus: 'Voir le mois suivant',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Courant', currentStatus: 'Voir le mois courant',
		monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
		'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
		monthNamesShort: ['Jan','Fév','Mar','Avr','Mai','Jun',
		'Jul','Aoû','Sep','Oct','Nov','Déc'],
		monthStatus: 'Voir un autre mois', yearStatus: 'Voir une autre année',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
		dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Ven','Sam'],
		dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
		dayStatus: 'Utiliser DD comme premier jour de la semaine', dateStatus: '\'Choisir\' le DD d MM',
		dateFormat: 'dd/mm/yy', firstDay: 1,
		initStatus: 'Choisir la date', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['fr']);
});﻿/* Hebrew initialisation for the UI Datepicker extension. */
/* Written by Amir Hardon (ahardon at gmail dot com). */
jQuery(function($){
	$.datepicker.regional['he'] = {
		clearText: 'נקה', clearStatus: '',
		closeText: 'סגור', closeStatus: '',
		prevText: '&#x3c;הקודם', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'הבא&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'היום', currentStatus: '',
		monthNames: ['ינואר','פברואר','מרץ','אפריל','מאי','יוני',
		'יולי','אוגוסט','ספטמבר','אוקטובר','נובמבר','דצמבר'],
		monthNamesShort: ['1','2','3','4','5','6',
		'7','8','9','10','11','12'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['ראשון','שני','שלישי','רביעי','חמישי','שישי','שבת'],
		dayNamesShort: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
		dayNamesMin: ['א\'','ב\'','ג\'','ד\'','ה\'','ו\'','שבת'],
		dayStatus: 'DD', dateStatus: 'DD, M d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: '', isRTL: true};
	$.datepicker.setDefaults($.datepicker.regional['he']);
});
﻿/* Croatian i18n for the jQuery UI date picker plugin. */
/* Written by Vjekoslav Nesek. */
jQuery(function($){
	$.datepicker.regional['hr'] = {
		clearText: 'izbriši', clearStatus: 'Izbriši trenutni datum',
		closeText: 'Zatvori', closeStatus: 'Zatvori kalendar',
		prevText: '&#x3c;', prevStatus: 'Prikaži prethodni mjesec',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: '&#x3e;', nextStatus: 'Prikaži slijedeći mjesec',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Danas', currentStatus: 'Današnji datum',
		monthNames: ['Siječanj','Veljača','Ožujak','Travanj','Svibanj','Lipani',
		'Srpanj','Kolovoz','Rujan','Listopad','Studeni','Prosinac'],
		monthNamesShort: ['Sij','Velj','Ožu','Tra','Svi','Lip',
		'Srp','Kol','Ruj','Lis','Stu','Pro'],
		monthStatus: 'Prikaži mjesece', yearStatus: 'Prikaži godine',
		weekHeader: 'Tje', weekStatus: 'Tjedan',
		dayNames: ['Nedjalja','Ponedjeljak','Utorak','Srijeda','Četvrtak','Petak','Subota'],
		dayNamesShort: ['Ned','Pon','Uto','Sri','Čet','Pet','Sub'],
		dayNamesMin: ['Ne','Po','Ut','Sr','Če','Pe','Su'],
		dayStatus: 'Odaber DD za prvi dan tjedna', dateStatus: '\'Datum\' D, M d',
		dateFormat: 'dd.mm.yy.', firstDay: 1,
		initStatus: 'Odaberi datum', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['hr']);
});/* Hungarian initialisation for the jQuery UI date picker plugin. */
/* Written by Istvan Karaszi (jquerycalendar@spam.raszi.hu). */
jQuery(function($){
	$.datepicker.regional['hu'] = {
		clearText: 'törlés', clearStatus: '',
		closeText: 'bezárás', closeStatus: '',
		prevText: '&laquo;&nbsp;vissza', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'előre&nbsp;&raquo;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'ma', currentStatus: '',
		monthNames: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június',
		'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
		monthNamesShort: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún',
		'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Hé', weekStatus: '',
		dayNames: ['Vasámap', 'Hétfö', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
		dayNamesShort: ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo'],
		dayNamesMin: ['V', 'H', 'K', 'Sze', 'Cs', 'P', 'Szo'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'yy-mm-dd', firstDay: 1,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['hu']);
});
/* Armenian(UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Levon Zakaryan (levon.zakaryan@gmail.com)*/
jQuery(function($){
	$.datepicker.regional['hy'] = {
		clearText: 'Մաքրել', clearStatus: '',
		closeText: 'Փակել', closeStatus: '',
		prevText: '&#x3c;Նախ.',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Հաջ.&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Այսօր', currentStatus: '',
		monthNames: ['Հունվար','Փետրվար','Մարտ','Ապրիլ','Մայիս','Հունիս',
		'Հուլիս','Օգոստոս','Սեպտեմբեր','Հոկտեմբեր','Նոյեմբեր','Դեկտեմբեր'],
		monthNamesShort: ['Հունվ','Փետր','Մարտ','Ապր','Մայիս','Հունիս',
		'Հուլ','Օգս','Սեպ','Հոկ','Նոյ','Դեկ'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'ՇԲՏ', weekStatus: '',
		dayNames: ['կիրակի','եկուշաբթի','երեքշաբթի','չորեքշաբթի','հինգշաբթի','ուրբաթ','շաբաթ'],
		dayNamesShort: ['կիր','երկ','երք','չրք','հնգ','ուրբ','շբթ'],
		dayNamesMin: ['կիր','երկ','երք','չրք','հնգ','ուրբ','շբթ'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['hy']);
});/* Indonesian initialisation for the jQuery UI date picker plugin. */
/* Written by Deden Fathurahman (dedenf@gmail.com). */
jQuery(function($){
	$.datepicker.regional['id'] = {
		clearText: 'kosongkan', clearStatus: 'bersihkan tanggal yang sekarang',
		closeText: 'Tutup', closeStatus: 'Tutup tanpa mengubah',
		prevText: '&#x3c;mundur', prevStatus: 'Tampilkan bulan sebelumnya',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'maju&#x3e;', nextStatus: 'Tampilkan bulan berikutnya',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'hari ini', currentStatus: 'Tampilkan bulan sekarang',
		monthNames: ['Januari','Februari','Maret','April','Mei','Juni',
		'Juli','Agustus','September','Oktober','Nopember','Desember'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Mei','Jun',
		'Jul','Agus','Sep','Okt','Nop','Des'],
		monthStatus: 'Tampilkan bulan yang berbeda', yearStatus: 'Tampilkan tahun yang berbeda',
		weekHeader: 'Mg', weekStatus: 'Minggu dalam tahun',
		dayNames: ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'],
		dayNamesShort: ['Min','Sen','Sel','Rab','kam','Jum','Sab'],
		dayNamesMin: ['Mg','Sn','Sl','Rb','Km','jm','Sb'],
		dayStatus: 'gunakan DD sebagai awal hari dalam minggu', dateStatus: 'pilih le DD, MM d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: 'Pilih Tanggal', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['id']);
});/* Icelandic initialisation for the jQuery UI date picker plugin. */
/* Written by Haukur H. Thorsson (haukur@eskill.is). */
jQuery(function($){
	$.datepicker.regional['is'] = {
		clearText: 'Hreinsa', clearStatus: '',
		closeText: 'Loka', closeStatus: '',
		prevText: '&#x3c; Fyrri', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'N&aelig;sti &#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: '&Iacute; dag', currentStatus: '',
		monthNames: ['Jan&uacute;ar','Febr&uacute;ar','Mars','Apr&iacute;l','Ma&iacute','J&uacute;n&iacute;',
		'J&uacute;l&iacute;','&Aacute;g&uacute;st','September','Okt&oacute;ber','N&oacute;vember','Desember'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Ma&iacute;','J&uacute;n',
		'J&uacute;l','&Aacute;g&uacute;','Sep','Okt','N&oacute;v','Des'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Vika', weekStatus: '',
		dayNames: ['Sunnudagur','M&aacute;nudagur','&THORN;ri&eth;judagur','Mi&eth;vikudagur','Fimmtudagur','F&ouml;studagur','Laugardagur'],
		dayNamesShort: ['Sun','M&aacute;n','&THORN;ri','Mi&eth;','Fim','F&ouml;s','Lau'],
		dayNamesMin: ['Su','M&aacute;','&THORN;r','Mi','Fi','F&ouml;','La'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['is']);
});/* Italian initialisation for the jQuery UI date picker plugin. */
/* Written by Apaella (apaella@gmail.com). */
jQuery(function($){
	$.datepicker.regional['it'] = {
		clearText: 'Svuota', clearStatus: 'Annulla',
		closeText: 'Chiudi', closeStatus: 'Chiudere senza modificare',
		prevText: '&#x3c;Prec', prevStatus: 'Mese precedente',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: 'Mostra l\'anno precedente',
		nextText: 'Succ&#x3e;', nextStatus: 'Mese successivo',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: 'Mostra l\'anno successivo',
		currentText: 'Oggi', currentStatus: 'Mese corrente',
		monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
		'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
		monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu',
		'Lug','Ago','Set','Ott','Nov','Dic'],
		monthStatus: 'Seleziona un altro mese', yearStatus: 'Seleziona un altro anno',
		weekHeader: 'Sm', weekStatus: 'Settimana dell\'anno',
		dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
		dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
		dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
		dayStatus: 'Usa DD come primo giorno della settimana', dateStatus: '\'Seleziona\' D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 1,
		initStatus: 'Scegliere una data', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['it']);
});
﻿/* Japanese initialisation for the jQuery UI date picker plugin. */
/* Written by Kentaro SATO (kentaro@ranvis.com). */
jQuery(function($){
	$.datepicker.regional['ja'] = {
		clearText: 'クリア', clearStatus: '日付をクリアします',
		closeText: '閉じる', closeStatus: '変更せずに閉じます',
		prevText: '&#x3c;前', prevStatus: '前月を表示します',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '前年を表示します',
		nextText: '次&#x3e;', nextStatus: '翌月を表示します',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '翌年を表示します',
		currentText: '今日', currentStatus: '今月を表示します',
		monthNames: ['1月','2月','3月','4月','5月','6月',
		'7月','8月','9月','10月','11月','12月'],
		monthNamesShort: ['1月','2月','3月','4月','5月','6月',
		'7月','8月','9月','10月','11月','12月'],
		monthStatus: '表示する月を変更します', yearStatus: '表示する年を変更します',
		weekHeader: '週', weekStatus: '暦週で第何週目かを表します',
		dayNames: ['日曜日','月曜日','火曜日','水曜日','木曜日','金曜日','土曜日'],
		dayNamesShort: ['日','月','火','水','木','金','土'],
		dayNamesMin: ['日','月','火','水','木','金','土'],
		dayStatus: '週の始まりをDDにします', dateStatus: 'Md日(D)',
		dateFormat: 'yy/mm/dd', firstDay: 0,
		initStatus: '日付を選択します', isRTL: false,
		showMonthAfterYear: true};
	$.datepicker.setDefaults($.datepicker.regional['ja']);
});/* Korean initialisation for the jQuery calendar extension. */
/* Written by DaeKwon Kang (ncrash.dk@gmail.com). */
jQuery(function($){
	$.datepicker.regional['ko'] = {
		clearText: '지우기', clearStatus: '',
		closeText: '닫기', closeStatus: '',
		prevText: '이전달', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: '다음달', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: '오늘', currentStatus: '',
		monthNames: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
		'7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
		monthNamesShort: ['1월(JAN)','2월(FEB)','3월(MAR)','4월(APR)','5월(MAY)','6월(JUN)',
		'7월(JUL)','8월(AUG)','9월(SEP)','10월(OCT)','11월(NOV)','12월(DEC)'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Wk', weekStatus: '',
		dayNames: ['일','월','화','수','목','금','토'],
		dayNamesShort: ['일','월','화','수','목','금','토'],
		dayNamesMin: ['일','월','화','수','목','금','토'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'yy-mm-dd', firstDay: 0,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['ko']);
});/* Lithuanian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* @author Arturas Paleicikas <arturas@avalon.lt> */
jQuery(function($){
	$.datepicker.regional['lt'] = {
		clearText: 'Išvalyti', clearStatus: '',
		closeText: 'Uždaryti', closeStatus: '',
		prevText: '&#x3c;Atgal',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Pirmyn&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Šiandien', currentStatus: '',
		monthNames: ['Sausis','Vasaris','Kovas','Balandis','Gegužė','Birželis',
		'Liepa','Rugpjūtis','Rugsėjis','Spalis','Lapkritis','Gruodis'],
		monthNamesShort: ['Sau','Vas','Kov','Bal','Geg','Bir',
		'Lie','Rugp','Rugs','Spa','Lap','Gru'],
		monthStatus: '', yearStatus: '',
		weekHeader: '', weekStatus: '',
		dayNames: ['sekmadienis','pirmadienis','antradienis','trečiadienis','ketvirtadienis','penktadienis','šeštadienis'],
		dayNamesShort: ['sek','pir','ant','tre','ket','pen','šeš'],
		dayNamesMin: ['Se','Pr','An','Tr','Ke','Pe','Še'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'yy-mm-dd', firstDay: 1,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['lt']);
});/* Latvian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* @author Arturas Paleicikas <arturas.paleicikas@metasite.net> */
jQuery(function($){
	$.datepicker.regional['lv'] = {
		clearText: 'Notīrīt', clearStatus: '',
		closeText: 'Aizvērt', closeStatus: '',
		prevText: 'Iepr',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Nāka', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Šodien', currentStatus: '',
		monthNames: ['Janvāris','Februāris','Marts','Aprīlis','Maijs','Jūnijs',
		'Jūlijs','Augusts','Septembris','Oktobris','Novembris','Decembris'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Mai','Jūn',
		'Jūl','Aug','Sep','Okt','Nov','Dec'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Nav', weekStatus: '',
		dayNames: ['svētdiena','pirmdiena','otrdiena','trešdiena','ceturtdiena','piektdiena','sestdiena'],
		dayNamesShort: ['svt','prm','otr','tre','ctr','pkt','sst'],
		dayNamesMin: ['Sv','Pr','Ot','Tr','Ct','Pk','Ss'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd-mm-yy', firstDay: 1,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['lv']);
});﻿/* Dutch (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Mathias Bynens <http://mathiasbynens.be/> */
jQuery(function($){
	$.datepicker.regional.nl = {
		clearText: 'Wissen', clearStatus: 'Wis de huidige datum',
		closeText: 'Sluiten', closeStatus: 'Sluit zonder verandering',
		prevText: '←', prevStatus: 'Bekijk de vorige maand',
		prevBigText: '«', nextBigStatus: 'Bekijk het vorige jaar',
		nextText: '→', nextStatus: 'Bekijk de volgende maand',
		nextBigText: '»', nextBigStatus: 'Bekijk het volgende jaar',
		currentText: 'Vandaag', currentStatus: 'Bekijk de huidige maand',
		monthNames: ['januari', 'februari', 'maart', 'april', 'mei', 'juni',
		'juli', 'augustus', 'september', 'oktober', 'november', 'december'],
		monthNamesShort: ['jan', 'feb', 'maa', 'apr', 'mei', 'jun',
		'jul', 'aug', 'sep', 'okt', 'nov', 'dec'],
		monthStatus: 'Bekijk een andere maand', yearStatus: 'Bekijk een ander jaar',
		weekHeader: 'Wk', weekStatus: 'Week van het jaar',
		dayNames: ['zondag', 'maandag', 'dinsdag', 'woensdag', 'donderdag', 'vrijdag', 'zaterdag'],
		dayNamesShort: ['zon', 'maa', 'din', 'woe', 'don', 'vri', 'zat'],
		dayNamesMin: ['zo', 'ma', 'di', 'wo', 'do', 'vr', 'za'],
		dayStatus: 'Stel DD in als eerste dag van de week', dateStatus: 'dd/mm/yy',
		dateFormat: 'dd/mm/yy', firstDay: 1,
		initStatus: 'Kies een datum', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional.nl);
});/* Norwegian initialisation for the jQuery UI date picker plugin. */
/* Written by Naimdjon Takhirov (naimdjon@gmail.com). */
jQuery(function($){
    $.datepicker.regional['no'] = {
		clearText: 'Tøm', clearStatus: '',
		closeText: 'Lukk', closeStatus: '',
        prevText: '&laquo;Forrige',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Neste&raquo;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'I dag', currentStatus: '',
        monthNames: ['Januar','Februar','Mars','April','Mai','Juni',
        'Juli','August','September','Oktober','November','Desember'],
        monthNamesShort: ['Jan','Feb','Mar','Apr','Mai','Jun',
        'Jul','Aug','Sep','Okt','Nov','Des'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Uke', weekStatus: '',
		dayNamesShort: ['Søn','Man','Tir','Ons','Tor','Fre','Lør'],
		dayNames: ['Søndag','Mandag','Tirsdag','Onsdag','Torsdag','Fredag','Lørdag'],
		dayNamesMin: ['Sø','Ma','Ti','On','To','Fr','Lø'],
		dayStatus: 'DD', dateStatus: 'D, M d',
        dateFormat: 'yy-mm-dd', firstDay: 0,
		initStatus: '', isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['no']);
});
/* Polish initialisation for the jQuery UI date picker plugin. */
/* Written by Jacek Wysocki (jacek.wysocki@gmail.com). */
jQuery(function($){
	$.datepicker.regional['pl'] = {
		clearText: 'Wyczyść', clearStatus: 'Wyczyść obecną datę',
		closeText: 'Zamknij', closeStatus: 'Zamknij bez zapisywania',
		prevText: '&#x3c;Poprzedni', prevStatus: 'Pokaż poprzedni miesiąc',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Następny&#x3e;', nextStatus: 'Pokaż następny miesiąc',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Dziś', currentStatus: 'Pokaż aktualny miesiąc',
		monthNames: ['Styczeń','Luty','Marzec','Kwiecień','Maj','Czerwiec',
		'Lipiec','Sierpień','Wrzesień','Październik','Listopad','Grudzień'],
		monthNamesShort: ['Sty','Lu','Mar','Kw','Maj','Cze',
		'Lip','Sie','Wrz','Pa','Lis','Gru'],
		monthStatus: 'Pokaż inny miesiąc', yearStatus: 'Pokaż inny rok',
		weekHeader: 'Tydz', weekStatus: 'Tydzień roku',
		dayNames: ['Niedziela','Poniedzialek','Wtorek','Środa','Czwartek','Piątek','Sobota'],
		dayNamesShort: ['Nie','Pn','Wt','Śr','Czw','Pt','So'],
		dayNamesMin: ['N','Pn','Wt','Śr','Cz','Pt','So'],
		dayStatus: 'Ustaw DD jako pierwszy dzień tygodnia', dateStatus: '\'Wybierz\' D, M d',
		dateFormat: 'yy-mm-dd', firstDay: 1,
		initStatus: 'Wybierz datę', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['pl']);
});
/* Brazilian initialisation for the jQuery UI date picker plugin. */
/* Written by Leonildo Costa Silva (leocsilva@gmail.com). */
jQuery(function($){
	$.datepicker.regional['pt-BR'] = {
		clearText: 'Limpar', clearStatus: '',
		closeText: 'Fechar', closeStatus: '',
		prevText: '&#x3c;Anterior', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Pr&oacute;ximo&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Hoje', currentStatus: '',
		monthNames: ['Janeiro','Fevereiro','Mar&ccedil;o','Abril','Maio','Junho',
		'Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
		monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun',
		'Jul','Ago','Set','Out','Nov','Dez'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['Domingo','Segunda-feira','Ter&ccedil;a-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sabado'],
		dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dayNamesMin: ['Dom','Seg','Ter','Qua','Qui','Sex','Sab'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['pt-BR']);
});/* Romanian initialisation for the jQuery UI date picker plugin. */
/* Written by Edmond L. (ll_edmond@walla.com). */
jQuery(function($){
	$.datepicker.regional['ro'] = {
		clearText: 'Curat', clearStatus: 'Sterge data curenta',
		closeText: 'Inchide', closeStatus: 'Inchide fara schimbare',
		prevText: '&#x3c;Anterior', prevStatus: 'Arata luna trecuta',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Urmator&#x3e;', nextStatus: 'Arata luna urmatoare',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Azi', currentStatus: 'Arata luna curenta',
		monthNames: ['Ianuarie','Februarie','Martie','Aprilie','Mai','Junie',
		'Julie','August','Septembrie','Octobrie','Noiembrie','Decembrie'],
		monthNamesShort: ['Ian', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun',
		'Jul', 'Aug', 'Sep', 'Oct', 'Noi', 'Dec'],
		monthStatus: 'Arata o luna diferita', yearStatus: 'Arat un an diferit',
		weekHeader: 'Sapt', weekStatus: 'Saptamana anului',
		dayNames: ['Duminica', 'Luni', 'Marti', 'Miercuri', 'Joi', 'Vineri', 'Sambata'],
		dayNamesShort: ['Dum', 'Lun', 'Mar', 'Mie', 'Joi', 'Vin', 'Sam'],
		dayNamesMin: ['Du','Lu','Ma','Mi','Jo','Vi','Sa'],
		dayStatus: 'Seteaza DD ca prima saptamana zi', dateStatus: 'Selecteaza D, M d',
		dateFormat: 'mm/dd/yy', firstDay: 0,
		initStatus: 'Selecteaza o data', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['ro']);
});
/* Russian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Andrew Stromnov (stromnov@gmail.com). */
jQuery(function($){
	$.datepicker.regional['ru'] = {
		clearText: 'Очистить', clearStatus: '',
		closeText: 'Закрыть', closeStatus: '',
		prevText: '&#x3c;Пред',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'След&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Сегодня', currentStatus: '',
		monthNames: ['Январь','Февраль','Март','Апрель','Май','Июнь',
		'Июль','Август','Сентябрь','Октябрь','Ноябрь','Декабрь'],
		monthNamesShort: ['Янв','Фев','Мар','Апр','Май','Июн',
		'Июл','Авг','Сен','Окт','Ноя','Дек'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Не', weekStatus: '',
		dayNames: ['воскресенье','понедельник','вторник','среда','четверг','пятница','суббота'],
		dayNamesShort: ['вск','пнд','втр','срд','чтв','птн','сбт'],
		dayNamesMin: ['Вс','Пн','Вт','Ср','Чт','Пт','Сб'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['ru']);
});/* Slovak initialisation for the jQuery UI date picker plugin. */
/* Written by Vojtech Rinik (vojto@hmm.sk). */
jQuery(function($){
	$.datepicker.regional['sk'] = {
		clearText: 'Zmazať', clearStatus: '',
		closeText: 'Zavrieť', closeStatus: '',
		prevText: '&#x3c;Predchádzajúci',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Nasledujúci&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Dnes', currentStatus: '',
		monthNames: ['Január','Február','Marec','Apríl','Máj','Jún',
		'Júl','August','September','Október','November','December'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Máj','Jún',
		'Júl','Aug','Sep','Okt','Nov','Dec'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Ty', weekStatus: '',
		dayNames: ['Nedel\'a','Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota'],
		dayNamesShort: ['Ned','Pon','Uto','Str','Štv','Pia','Sob'],
		dayNamesMin: ['Ne','Po','Ut','St','Št','Pia','So'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd.mm.yy', firstDay: 0,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['sk']);
});
/* Slovenian initialisation for the jQuery UI date picker plugin. */
/* Written by Jaka Jancar (jaka@kubje.org). */
/* c = &#x10D;, s = &#x161; z = &#x17E; C = &#x10C; S = &#x160; Z = &#x17D; */
jQuery(function($){
	$.datepicker.regional['sl'] = {
		clearText: 'Izbri&#x161;i', clearStatus: 'Izbri&#x161;i trenutni datum',
		closeText: 'Zapri', closeStatus: 'Zapri brez spreminjanja',
		prevText: '&lt;Prej&#x161;nji', prevStatus: 'Prika&#x17E;i prej&#x161;nji mesec',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Naslednji&gt;', nextStatus: 'Prika&#x17E;i naslednji mesec',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Trenutni', currentStatus: 'Prika&#x17E;i trenutni mesec',
		monthNames: ['Januar','Februar','Marec','April','Maj','Junij',
		'Julij','Avgust','September','Oktober','November','December'],
		monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun',
		'Jul','Avg','Sep','Okt','Nov','Dec'],
		monthStatus: 'Prika&#x17E;i drug mesec', yearStatus: 'Prika&#x17E;i drugo leto',
		weekHeader: 'Teden', weekStatus: 'Teden v letu',
		dayNames: ['Nedelja','Ponedeljek','Torek','Sreda','&#x10C;etrtek','Petek','Sobota'],
		dayNamesShort: ['Ned','Pon','Tor','Sre','&#x10C;et','Pet','Sob'],
		dayNamesMin: ['Ne','Po','To','Sr','&#x10C;e','Pe','So'],
		dayStatus: 'Nastavi DD za prvi dan v tednu', dateStatus: 'Izberi DD, d MM yy',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: 'Izbira datuma', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['sl']);
});
﻿/* Albanian initialisation for the jQuery UI date picker plugin. */
/* Written by Flakron Bytyqi (flakron@gmail.com). */
jQuery(function($){
	$.datepicker.regional['sq'] = {
		clearText: 'fshije', clearStatus: 'fshije datën aktuale',
		closeText: 'mbylle', closeStatus: 'mbylle pa ndryshime',
		prevText: '&#x3c;mbrapa', prevStatus: 'trego muajin e fundit',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Përpara&#x3e;', nextStatus: 'trego muajin tjetër',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'sot', currentStatus: '',
		monthNames: ['Janar','Shkurt','Mars','Pril','Maj','Qershor',
		'Korrik','Gusht','Shtator','Tetor','Nëntor','Dhjetor'],
		monthNamesShort: ['Jan','Shk','Mar','Pri','Maj','Qer',
		'Kor','Gus','Sht','Tet','Nën','Dhj'],
		monthStatus: 'trego muajin tjetër', yearStatus: 'trego tjetër vit',
		weekHeader: 'Ja', weekStatus: 'Java e muajit',
		dayNames: ['E Diel','E Hënë','E Martë','E Mërkurë','E Enjte','E Premte','E Shtune'],
		dayNamesShort: ['Di','Hë','Ma','Më','En','Pr','Sh'],
		dayNamesMin: ['Di','Hë','Ma','Më','En','Pr','Sh'],
		dayStatus: 'Vendose DD si ditë të parë të javës', dateStatus: '\'Zgjedh\' D, M d',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: 'Zgjedhe një datë', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['sq']);
});
﻿/* Swedish initialisation for the jQuery UI date picker plugin. */
/* Written by Anders Ekdahl ( anders@nomadiz.se). */
jQuery(function($){
    $.datepicker.regional['sv'] = {
		clearText: 'Rensa', clearStatus: '',
		closeText: 'Stäng', closeStatus: '',
        prevText: '&laquo;Förra',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'Nästa&raquo;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Idag', currentStatus: '',
        monthNames: ['Januari','Februari','Mars','April','Maj','Juni',
        'Juli','Augusti','September','Oktober','November','December'],
        monthNamesShort: ['Jan','Feb','Mar','Apr','Maj','Jun',
        'Jul','Aug','Sep','Okt','Nov','Dec'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Ve', weekStatus: '',
		dayNamesShort: ['Sön','Mån','Tis','Ons','Tor','Fre','Lör'],
		dayNames: ['Söndag','Måndag','Tisdag','Onsdag','Torsdag','Fredag','Lördag'],
		dayNamesMin: ['Sö','Må','Ti','On','To','Fr','Lö'],
		dayStatus: 'DD', dateStatus: 'D, M d',
        dateFormat: 'yy-mm-dd', firstDay: 1,
		initStatus: '', isRTL: false};
    $.datepicker.setDefaults($.datepicker.regional['sv']);
});
﻿/* Thai initialisation for the jQuery UI date picker plugin. */
/* Written by pipo (pipo@sixhead.com). */
jQuery(function($){
	$.datepicker.regional['th'] = {
		clearText: 'ลบ', clearStatus: '',
		closeText: 'ปิด', closeStatus: '',
		prevText: '&laquo;&nbsp;ย้อน', prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'ถัดไป&nbsp;&raquo;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'วันนี้', currentStatus: '',
		monthNames: ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน',
		'กรกฏาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'],
		monthNamesShort: ['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.',
		'ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Sm', weekStatus: '',
		dayNames: ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'],
		dayNamesShort: ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'],
		dayNamesMin: ['อา.','จ.','อ.','พ.','พฤ.','ศ.','ส.'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd/mm/yy', firstDay: 0,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['th']);
});/* Turkish initialisation for the jQuery UI date picker plugin. */
/* Written by Izzet Emre Erkan (kara@karalamalar.net). */
jQuery(function($){
	$.datepicker.regional['tr'] = {
		clearText: 'temizle', clearStatus: 'geçerli tarihi temizler',
		closeText: 'kapat', closeStatus: 'sadece göstergeyi kapat',
		prevText: '&#x3c;geri', prevStatus: 'önceki ayı göster',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: 'ileri&#x3e', nextStatus: 'sonraki ayı göster',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'bugün', currentStatus: '',
		monthNames: ['Ocak','Şubat','Mart','Nisan','Mayıs','Haziran',
		'Temmuz','Ağustos','Eylül','Ekim','Kasım','Aralık'],
		monthNamesShort: ['Oca','Şub','Mar','Nis','May','Haz',
		'Tem','Ağu','Eyl','Eki','Kas','Ara'],
		monthStatus: 'başka ay', yearStatus: 'başka yıl',
		weekHeader: 'Hf', weekStatus: 'Ayın haftaları',
		dayNames: ['Pazar','Pazartesi','Salı','Çarşamba','Perşembe','Cuma','Cumartesi'],
		dayNamesShort: ['Pz','Pt','Sa','Ça','Pe','Cu','Ct'],
		dayNamesMin: ['Pz','Pt','Sa','Ça','Pe','Cu','Ct'],
		dayStatus: 'Haftanın ilk gününü belirleyin', dateStatus: 'D, M d seçiniz',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: 'Bir tarih seçiniz', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['tr']);
});/* Ukrainian (UTF-8) initialisation for the jQuery UI date picker plugin. */
/* Written by Maxim Drogobitskiy (maxdao@gmail.com). */
jQuery(function($){
	$.datepicker.regional['uk'] = {
		clearText: 'Очистити', clearStatus: '',
		closeText: 'Закрити', closeStatus: '',
		prevText: '&#x3c;',  prevStatus: '',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '',
		nextText: '&#x3e;', nextStatus: '',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '',
		currentText: 'Сьогодні', currentStatus: '',
		monthNames: ['Січень','Лютий','Березень','Квітень','Травень','Червень',
		'Липень','Серпень','Вересень','Жовтень','Листопад','Грудень'],
		monthNamesShort: ['Січ','Лют','Бер','Кві','Тра','Чер',
		'Лип','Сер','Вер','Жов','Лис','Гру'],
		monthStatus: '', yearStatus: '',
		weekHeader: 'Не', weekStatus: '',
		dayNames: ['неділя','понеділок','вівторок','середа','четвер','пятниця','суббота'],
		dayNamesShort: ['нед','пнд','вів','срд','чтв','птн','сбт'],
		dayNamesMin: ['Нд','Пн','Вт','Ср','Чт','Пт','Сб'],
		dayStatus: 'DD', dateStatus: 'D, M d',
		dateFormat: 'dd.mm.yy', firstDay: 1,
		initStatus: '', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['uk']);
});/* Chinese initialisation for the jQuery UI date picker plugin. */
/* Written by Cloudream (cloudream@gmail.com). */
jQuery(function($){
	$.datepicker.regional['zh-CN'] = {
		clearText: '清除', clearStatus: '清除已选日期',
		closeText: '关闭', closeStatus: '不改变当前选择',
		prevText: '&#x3c;上月', prevStatus: '显示上月',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '显示上一年',
		nextText: '下月&#x3e;', nextStatus: '显示下月',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '显示下一年',
		currentText: '今天', currentStatus: '显示本月',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		monthNamesShort: ['一','二','三','四','五','六',
		'七','八','九','十','十一','十二'],
		monthStatus: '选择月份', yearStatus: '选择年份',
		weekHeader: '周', weekStatus: '年内周次',
		dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
		dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
		dayNamesMin: ['日','一','二','三','四','五','六'],
		dayStatus: '设置 DD 为一周起始', dateStatus: '选择 m月 d日, DD',
		dateFormat: 'yy-mm-dd', firstDay: 1,
		initStatus: '请选择日期', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['zh-CN']);
});
﻿/* Chinese initialisation for the jQuery UI date picker plugin. */
/* Written by Ressol (ressol@gmail.com). */
jQuery(function($){
	$.datepicker.regional['zh-TW'] = {
		clearText: '清除', clearStatus: '清除已選日期',
		closeText: '關閉', closeStatus: '不改變目前的選擇',
		prevText: '&#x3c;上月', prevStatus: '顯示上月',
		prevBigText: '&#x3c;&#x3c;', prevBigStatus: '顯示上一年',
		nextText: '下月&#x3e;', nextStatus: '顯示下月',
		nextBigText: '&#x3e;&#x3e;', nextBigStatus: '顯示下一年',
		currentText: '今天', currentStatus: '顯示本月',
		monthNames: ['一月','二月','三月','四月','五月','六月',
		'七月','八月','九月','十月','十一月','十二月'],
		monthNamesShort: ['一','二','三','四','五','六',
		'七','八','九','十','十一','十二'],
		monthStatus: '選擇月份', yearStatus: '選擇年份',
		weekHeader: '周', weekStatus: '年內周次',
		dayNames: ['星期日','星期一','星期二','星期三','星期四','星期五','星期六'],
		dayNamesShort: ['周日','周一','周二','周三','周四','周五','周六'],
		dayNamesMin: ['日','一','二','三','四','五','六'],
		dayStatus: '設定 DD 為一周起始', dateStatus: '選擇 m月 d日, DD',
		dateFormat: 'yy/mm/dd', firstDay: 1,
		initStatus: '請選擇日期', isRTL: false};
	$.datepicker.setDefaults($.datepicker.regional['zh-TW']);
});
