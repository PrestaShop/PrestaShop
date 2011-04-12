function addLoadEvent_MR(func, args)
{ 
	var oldonload = window.onload;
	if (typeof window.onload != 'function')
		window.onload = func;
	else
	{
		window.onload = function()
			{
				if (oldonload)
					oldonload();
				func(args);
			} 
	}
}

var gl_base_dir;

function set_html_MR_recherche(obj, num, base_dir, cpt)
{
gl_base_dir = base_dir;

var html = '\
	<div style=\'z-index:9998;cursor: wait;position:absolute; top:0; left:0; height:100%; width:100%;display:none;\
		         background-color:#cccccc;-moz-opacity:0.8; opacity:0.8; filter:alpha(opacity=80); -khtml-opacity:0.8;\' id=\'bg_detail_md_'+num+'\'></div>\
				 <div id=\'detail_md_'+num+'\' style=\'position: absolute; background-color:#ffffff; float:left; width:740px; height:480px; border:1px solid #89a; overflow:none; text-align:left; z-index:9999; display:none;\'></div>\
				 <table style=\'border:none;\'>\
	<tr style=\'border:none;\'>\
	<td valign=top style=\'border:none;\'><img src="'+base_dir+'modules/mondialrelay/kit_mondialrelay/MR_small.gif"></td>\
	<td valign=top style=\'border:none; width:500px;\'><strong><span id="MR_LgAdr1_'+obj.num+'">'+obj.address1+'</span></strong> - \
<span id="MR_LgAdr2_'+obj.num+'">'+obj.address2+'</span>';
if (obj.address2 != '')
	html = html + ' - ';
html = html + '<span id="MR_LgAdr3_'+obj.num+'">'+obj.address3+'</span>';
if (obj.address3 != '')
	html = html + ' - ';
html = html + '<span id="MR_LgAdr4_'+obj.num+'">'+obj.address4+'</span>';
if (obj.address4 != '')
	html = html + ' - ';
html = html + '<span id="MR_CP_'+obj.num+'">'+obj.postcode+'</span> \
<span id="MR_Ville_'+obj.num+'">'+obj.city+'</span> - \
<span id="MR_Pays_'+obj.num+'">'+obj.iso_country+'</span></td>\
	<td valign=top style=\'border:none;\'><a href="#" onclick="recherche_MR_detail(\''+obj.num+'\',\''+obj.iso_country+'\',\''+num+'\'); return false;"><img src="'+base_dir+'modules/mondialrelay/kit_mondialrelay/loupe.gif" border="0"></a></td>\
	<td valign=top style=\'border:none;\'><input  id="MRchoixRelais'+num+'_'+obj.num+'" name="MRchoixRelais_'+num+'" value="MR'+obj.num+'" onclick="select_PR_MR(\''+obj.num+'\',\''+num+'\')" type="radio"';
	if (obj.checked == '1' || cpt == 0)
		html = html + ' checked="checked" ';
	
	html = html + '> </td>\
	</tr>\
	</table>';
	$('#mondialrelay_'+num).html($('#mondialrelay_'+num).html()+html);
	if (obj.checked == '1' || cpt == 0)
		select_PR_MR(obj.num, num);
}

function include_mondialrelay(num)
{
	$('#form_mondialrelay_'+num).html('\
	<input id="MR_Selected_Num_'+num+'" name="MR_Selected_Num_'+num+'" type="hidden" >\
	<input id="MR_Selected_LgAdr1_'+num+'" name="MR_Selected_LgAdr1_'+num+'" type="hidden" >\
	<input id="MR_Selected_LgAdr2_'+num+'" name="MR_Selected_LgAdr2_'+num+'" type="hidden" >\
	<input id="MR_Selected_LgAdr3_'+num+'" name="MR_Selected_LgAdr3_'+num+'" type="hidden" >\
	<input id="MR_Selected_LgAdr4_'+num+'" name="MR_Selected_LgAdr4_'+num+'" type="hidden" >\
	<input id="MR_Selected_CP_'+num+'" name="MR_Selected_CP_'+num+'" type="hidden" >\
	<input id="MR_Selected_Ville_'+num+'" name="MR_Selected_Ville_'+num+'" type="hidden" >\
	<input id="MR_Selected_Pays_'+num+'" name="MR_Selected_Pays_'+num+'" type="hidden" >\
	\
	\
	<div id="recherche_MR_form_'+num+'" >\
	</div>\
	<div id="mondialprelay_'+num+'" style="padding:3px;"></div>');
}
