/*
 * this file come from:
 * http://www.phpletter.com/Demo/AjaxFileUpload-Demo/
 * v 1.0
 */

jQuery.extend({createUploadIframe:function(id,uri)
{var frameId='jUploadFrame'+id;if(window.ActiveXObject){var io=document.createElement('<iframe id="'+frameId+'" name="'+frameId+'" />');if(typeof uri=='boolean'){io.src='javascript:false';}
else if(typeof uri=='string'){io.src=uri;}}
else{var io=document.createElement('iframe');io.id=frameId;io.name=frameId;}
io.style.position='absolute';io.style.top='-1000px';io.style.left='-1000px';document.body.appendChild(io);return io},createUploadForm:function(id,fileElementId)
{var formId='jUploadForm'+id;var fileId='jUploadFile'+id;var form=$('<form  action="" method="POST" name="'+formId+'" id="'+formId+'" enctype="multipart/form-data"></form>');var oldElement=$('#'+fileElementId);var newElement=$(oldElement).clone();$(oldElement).attr('id',fileId);$(oldElement).before(newElement);$(oldElement).appendTo(form);$(form).css('position','absolute');$(form).css('top','-1200px');$(form).css('left','-1200px');$(form).appendTo('body');return form;},ajaxFileUpload:function(s){s=jQuery.extend({},jQuery.ajaxSettings,s);var id=new Date().getTime()
var form=jQuery.createUploadForm(id,s.fileElementId);var io=jQuery.createUploadIframe(id,s.secureuri);var frameId='jUploadFrame'+id;var formId='jUploadForm'+id;if(s.global&&!jQuery.active++)
{jQuery.event.trigger("ajaxStart");}
var requestDone=false;var xml={}
if(s.global)
jQuery.event.trigger("ajaxSend",[xml,s]);var uploadCallback=function(isTimeout)
{var io=document.getElementById(frameId);try
{if(io.contentWindow)
{xml.responseText=io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;xml.responseXML=io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;}else if(io.contentDocument)
{xml.responseText=io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;xml.responseXML=io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document;}}catch(e)
{jQuery.handleError(s,xml,null,e);}
if(xml||isTimeout=="timeout")
{requestDone=true;var status;try{status=isTimeout!="timeout"?"success":"error";if(status!="error")
{var data=jQuery.uploadHttpData(xml,s.dataType);if(s.success)
s.success(data,status);if(s.global)
jQuery.event.trigger("ajaxSuccess",[xml,s]);}else
jQuery.handleError(s,xml,status);}catch(e)
{status="error";jQuery.handleError(s,xml,status,e);}
if(s.global)
jQuery.event.trigger("ajaxComplete",[xml,s]);if(s.global&&!--jQuery.active)
jQuery.event.trigger("ajaxStop");if(s.complete)
s.complete(xml,status);jQuery(io).unbind()
setTimeout(function()
{try
{$(io).remove();$(form).remove();}catch(e)
{jQuery.handleError(s,xml,null,e);}},100)
xml=null}}
if(s.timeout>0)
{setTimeout(function(){if(!requestDone)uploadCallback("timeout");},s.timeout);}
try
{var form=$('#'+formId);$(form).attr('action',s.url);$(form).attr('method','POST');$(form).attr('target',frameId);if(form.encoding)
{form.encoding='multipart/form-data';}
else
{form.enctype='multipart/form-data';}
$(form).submit();}catch(e)
{jQuery.handleError(s,xml,null,e);}
if(window.attachEvent){document.getElementById(frameId).attachEvent('onload',uploadCallback);}
else{document.getElementById(frameId).addEventListener('load',uploadCallback,false);}
return{abort:function(){}};},uploadHttpData:function(r,type){var data=!type;data=type=="xml"||data?r.responseXML:r.responseText;if(type=="script")
jQuery.globalEval(data);if(type=="json")
eval("data = "+data);if(type=="html")
jQuery("<div>").html(data).evalScripts();return data;}})
