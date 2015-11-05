if(strPdf != ""){
	document.write('<object classid="clsid:CA8A9780-280D-11CF-A24D-444553540000" width="100%" height="100%" id=Pdf1>');
	document.write('  <param name="src" value="'+strPdf+'" />');
	document.write('</object>');
} else {
	document.write('PDF cannot be displayed.');
}
