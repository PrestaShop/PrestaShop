/*
 * this file come from:
 * http://www.phpletter.com/Demo/AjaxFileUpload-Demo/
 * v 2.1
 */
jQuery.extend({createUploadIframe:function(a,b){var c="jUploadFrame"+a;var d='<iframe id="'+c+'" name="'+c+'" style="position:absolute; top:-9999px; left:-9999px"';if(window.ActiveXObject){if(typeof b=="boolean"){d+=' src="'+"javascript:false"+'"'}else if(typeof b=="string"){d+=' src="'+b+'"'}}d+=" />";jQuery(d).appendTo(document.body);return jQuery("#"+c).get(0)},createUploadForm:function(a,b,c){var d="jUploadForm"+a;var e="jUploadFile"+a;var f=jQuery('<form  action="" method="POST" name="'+d+'" id="'+d+'" enctype="multipart/form-data"></form>');if(c){for(var g in c){jQuery('<input type="hidden" name="'+g+'" value="'+c[g]+'" />').appendTo(f)}}var h=jQuery("#"+b);var i=jQuery(h).clone();jQuery(h).attr("id",e);jQuery(h).before(i);jQuery(h).appendTo(f);jQuery(f).css("position","absolute");jQuery(f).css("top","-1200px");jQuery(f).css("left","-1200px");jQuery(f).appendTo("body");return f},ajaxFileUpload:function(a){a=jQuery.extend({},jQuery.ajaxSettings,a);var b=(new Date).getTime();var c=jQuery.createUploadForm(b,a.fileElementId,typeof a.data=="undefined"?false:a.data);var d=jQuery.createUploadIframe(b,a.secureuri);var e="jUploadFrame"+b;var f="jUploadForm"+b;if(a.global&&!(jQuery.active++)){jQuery.event.trigger("ajaxStart")}var g=false;var h={};if(a.global)jQuery.event.trigger("ajaxSend",[h,a]);var i=function(b){var d=document.getElementById(e);try{if(d.contentWindow){h.responseText=d.contentWindow.document.body?d.contentWindow.document.body.innerHTML:null;h.responseXML=d.contentWindow.document.XMLDocument?d.contentWindow.document.XMLDocument:d.contentWindow.document}else if(d.contentDocument){h.responseText=d.contentDocument.document.body?d.contentDocument.document.body.innerHTML:null;h.responseXML=d.contentDocument.document.XMLDocument?d.contentDocument.document.XMLDocument:d.contentDocument.document}}catch(f){jQuery.handleError(a,h,null,f)}if(h||b=="timeout"){g=true;var i;try{i=b!="timeout"?"success":"error";if(i!="error"){var j=jQuery.uploadHttpData(h,a.dataType);if(a.success)a.success(j,i);if(a.global)jQuery.event.trigger("ajaxSuccess",[h,a])}else jQuery.handleError(a,h,i)}catch(f){i="error";jQuery.handleError(a,h,i,f)}if(a.global)jQuery.event.trigger("ajaxComplete",[h,a]);if(a.global&&!--jQuery.active)jQuery.event.trigger("ajaxStop");if(a.complete)a.complete(h,i);jQuery(d).unbind();setTimeout(function(){try{jQuery(d).remove();jQuery(c).remove()}catch(b){jQuery.handleError(a,h,null,b)}},100);h=null}};if(a.timeout>0){setTimeout(function(){if(!g)i("timeout")},a.timeout)}try{var c=jQuery("#"+f);jQuery(c).attr("action",a.url);jQuery(c).attr("method","POST");jQuery(c).attr("target",e);if(c.encoding){jQuery(c).attr("encoding","multipart/form-data")}else{jQuery(c).attr("enctype","multipart/form-data")}jQuery(c).submit()}catch(j){jQuery.handleError(a,h,null,j)}jQuery("#"+e).on('load',i);return{abort:function(){}}},uploadHttpData:function(r,type){var data=!type;data=type=="xml"||data?r.responseXML:r.responseText;if(type=="script")jQuery.globalEval(data);if(type=="json")eval("data = "+data);if(type=="html")jQuery("<div>").html(data).evalScripts();return data}})