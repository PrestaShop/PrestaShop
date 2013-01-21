//function below added by logan (cailongqun [at] yahoo [dot] com [dot] cn) from www.phpletter.com
function selectFile()
{
	var selectedFileRowNum = $('#selectedFileRowNum').val();
  if(selectedFileRowNum != '' && $('#row' + selectedFileRowNum))
  {

	  // insert information now
	  var url = $('#fileUrl'+selectedFileRowNum).val();  	
		window.opener.SetUrl( url ) ;
		window.close() ;
		
  }else
  {
  	alert(noFileSelected);
  }
  

}



function cancelSelectFile()
{
  // close popup window
  window.close() ;
}