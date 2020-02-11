<?php 
session_start();
include("common/functions.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="css/main.css" />
<title>Test Your Stre</title>
<script language="javascript">
function play(tgt) {
    var uri = "<div style="border:1px solid #990000;padding-left:20px;margin:0 0 10px 0;">

<h4>A PHP Error was encountered</h4>

<p>Severity: Notice</p>
<p>Message:  Undefined variable: saddress</p>
<p>Filename: views/vlctester_view.php</p>
<p>Line Number: 9</p>

</div>";
    if (document.all) tgt += "_IE"
    var tgt = document.getElementById(tgt);
      if (document.all) tgt.playlist.add(uri,uri, new Array());
    else     tgt.playlist.add(uri,uri, "");
    tgt.playlist.play(); 
}
function reload() {
    document.body.innerHTML="";
    setTimeout("document.location.reload();", 500);
}
</script>
<script>
if (window!= top)
top.location.href=location.href
</script>
</head>

<body onload="play('vlc1')">
<!-- main conteiner-->
<div id="content">
<div id="menu">
</div>
<div id="gadc"> 
<div id="forma">
<h1>VLC stream tester</h1>

<form action="http://www.vlc.eu.pn/index.php" method="post" accept-charset="utf-8"><input type="text" name="saddress" value="" size="45"  /><br/><input type="submit" name="submit" value="Enter stream"  /><br/></form><br/ > <table width="468" border="0" cellspacing="0" cellpadding="0"> <tr> <td>
<td></tr></table>
<br/>
<OBJECT id='vlc1_IE'  codeBase=http://downloads.videolan.org/pub/videolan/vlc/latest/win32/axvlc.cab height=343 width=645 classid=clsid:9BE31822-FDAD-461B-AD51-BE1D1C159921>
<embed type="application/x-vlc-plugin" pluginspage="http://www.videolan.org" version="VideoLAN.VLCPlugin.2"
    width="640"
    height="340"
    id="vlc1">
</embed>
</OBJECT> 


<br/>
 
  <div id="gadb"> <table width="336" border="0"><tr><td> 
    </td></tr></table>

 </div>
</div>
  <br/>
  Copyright &copy;<a href="http://www.vlc.eu.pn" style="color:#0066cc;"> www.iptv-admin.com Test Stream</a> 2012 - 2014
</div>

<div id="footer">
</div>

<!--end main container-->
</div>
</body>
</html>