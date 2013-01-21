function selectFile(url)
{
      window.opener.document.getElementById(elementId).value = url;
      window.close() ;
 

}



function cancelSelectFile()
{
  // close popup window
  window.close() ;
}

