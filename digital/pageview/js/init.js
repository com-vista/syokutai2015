/**
 * The kind of a browser is acquired. 
 */
function getBrowser(ua){
  var res = {};
  var num = -1
  var browser = "";
  
  if (ua.indexOf("msie") > -1 && ua.indexOf("opera") <= -1) {
    // 'Msie' is contained, and it doesn't contain 'Opera'. 
    browser = "IE";
    num = ua.match(new RegExp("msie [0-9]{1,2}\.[0-9]{1,3}"));
    num = ( num == null ) ? -1 : parseFloat(String(num).replace("msie ",""));
  }
  else if (ua.indexOf("firefox") > -1) {
    // 'Firefox' is contained. 
    browser = "Firefox";
    num= ua.match(new RegExp("firefox/[0-9]{1,2}\.[0-9]{1,2}"));
    num = ( num == null ) ? -1 : parseFloat(String(num).replace("firefox/",""));
  }
  else if (ua.indexOf("chrome") > -1) {
    // 'chrome' is contained, and it doesn't contain 'sarafi'. 
    browser = "Chrome";
    num = ua.match(new RegExp("chrome[/ ][0-9]{1,2}\.[0-9]{1,2}"));
    num = ( num == null ) ? -1 : parseFloat(String(num).replace("chrome/",""));
  }
  else if (ua.indexOf("safari") > -1) {
    // 'safari' is contained. 
    // *It is since three that the version is contained. 
    browser = "Safari";
    num = ua.match(new RegExp("version/[0-9]{1,2}\.[0-9]{1,2}"));
    num = ( num == null ) ? -1 : parseFloat(String(num).replace("version/",""));
  }
  if (ua.indexOf("opera") > -1) {
    // 'opera' is contained. 
    // *Please note that 'Msie' is included in 'userAgent' according to the version. 
    browser = "Opera";
    num = ua.match(new RegExp("opera[/ ][0-9]{1,2}\.[0-9]{1,2}"));
    num = ( num == null ) ? -1 : parseFloat(String(num).substr(6));
  }
  else if (ua.indexOf("netscape") > -1) {
    // 'Netscape' is contained. 
    // *It is since version 6 that 'Netscape' is included in 'UserAgent'. 
    browser = "Netscape";
    num = ua.match(new RegExp("netscape[0-9]?/[0-9]{1,2}\.[0-9]{1,3}"));
    num = ( num == null ) ? -1 : parseFloat(String(num).replace(new RegExp("netscape[0-9]?/"),""));
  }
  /*
  else if (ua.indexOf("webkit") > -1) {
    browser = "WebKit";
    var num = this.ua.match(new RegExp("webkit/[0-9]{1,4}(\.[0-9]{1,2})?"));
    return ( num == null ) ? -1 : parseFloat(String(num).replace("webkit/",""));
  }*/

  res.browser = browser;
  res.version = num;
  return res;
}

/**
 * os
 */
function getOSType(ua) {
  //var uAgent = navigator.userAgent.toLowerCase(); 
  if (ua.indexOf("mac") >= 0) return "Mac";
  if (ua.indexOf("win") >= 0) return "Windows"; 
  return ""; 
} 

//-------------------------------------------------------------------------------------------------

// local or web
var isLocal = false;
if(document.location.protocol == "file:"){
  isLocal = true;
}

// make parameter.
var param="";
var page_val="";
var keyword_val="";
var tmp_param = "/";
var url_pram;
var usedGetparam = false;

if(location.search.substr(1) != ""){
  tmp_param = location.search.substr(1);
  usedGetparam = true;
} else if(typeof(SWFAddress) != "undefined"){
  tmp_param = SWFAddress.getValue();
} else {
}

if(tmp_param != "/"){
  url_pram =tmp_param.split("&");
}
for (idx in url_pram) {
    ws = url_pram[idx].split("=");
    if(ws[0]=="page_num"){
        page_val = ws[1];
    } else if(ws[0]=="keyword"){
        keyword_val = ws[1];
  }
}

var long_push_val = 1; // use
var open_pdf_val = 0; // Unused
var download_val = 1; // target is _blank
var swf_address_val = 1; //use
var userAgent = navigator.userAgent.toLowerCase();
// 
var br = new getBrowser(userAgent);
var os = getOSType(userAgent);

var browser = br.browser;
var version = br.version;

// Page movement function by a button long push is blockaded, except for 'Windows'. 
if(os != "Windows"){
  long_push_val = 0;
}

// PDF is displayed in HTML for 'Windows IE'. 
if( browser == "IE"){
  open_pdf_val = 1;
}

// The target when the file is downloaded when a browser is 'Windows IE' is made '_ self'. 
if(os == "Windows"){
  if( browser == "IE"){
    download_val = 0;
  }
}

// IE6
if( browser == "IE" && version <= 6){
  // When the get parameter is used in a local environment, 'SWFAddress' is not used. 
  if(isLocal==true && usedGetparam == true){
    swf_address_val = 0;
  }
}
//-------------------------------------------------------------------------------------------------
// window size
function window_resize(width,height){
  try{
    if(!arguments[2])oj=self

    // var os = navigator.userAgent;
    if( (os == "Mac") && (os == "Safari") ){
      resizeTo(width,height);
    }

    // An inside size is examined. 
    if(window.opera||document.layers){       //n4 o6
      var w = oj.innerWidth  
      var h = oj.innerHeight  
    } else if(document.all){                 //e
      var w = oj.document.body.clientWidth 
      var h = oj.document.body.clientHeight
    } else if(document.getElementById){      //n6,n7,m1
      var w = oj.innerWidth   
      var h = oj.innerHeight 
    }
    // If the result of 'ResizeTo' is correct, the size is left just as it is. 
    // The difference is added if differing. 
    if(width!=w||height!=h){
      oj.resizeBy((width-w),(height-h))
      if(document.layers)
        oj.location.reload(0) // The bug of 'Resize' is evaded by using 'Reload' at 'N4'. 
    }
  }catch(e){
    // When the error of "The access was refused" goes out, nothing is done. 
    if(e.number == 2147024891){
      throw e;
    }
  }
}

// MPV view
function openPVWindow(theURL,pageNum,winName,features) {
  win = window.open(theURL+"?page_num="+pageNum,winName,features);
  win.focus();
}

// MPV view2
function openWin(url){
    w = open(url,"_blank","toolbar=0,location=0,status=0,menubar=0,scrollbars=0,resizable=1,width=974,height=630,top=0,left=0");
    w.focus();
}


// PDF viwe
function openAX(theURL,winName,features,pdf) {
  win = window.open(theURL+"?pdf="+pdf,winName,features);
  win.focus();
}

// Google Analytics
function sendGA(send_url){
  if( typeof pageTracker != "undefined" ){
    try {
      pageTracker._trackPageview(send_url);
    } catch(err) {}
  } else if( typeof _gaq != "undefined" ){
    try {
      _gaq.push(['_trackPageview', send_url]);
    } catch(err) {}
  }
}
