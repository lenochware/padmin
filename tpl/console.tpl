<?elements
class form route "console"
string TERM
input CMDLINE attr "autocomplete=""off""" size "50/200"
button submit lb "Ok"
?>
<style type="text/css">
div.console td {
  font: 12px courier;
}

div.console input {
  font: 12px courier;
  width:100%;
  border: 0px;
  background-color: #ccc;
}

DIV.term {
  font: 12px courier;
  width: 600px;
  height:300px;
  overflow: auto;
}

.console-value {color:green}
.console-cmd   {color:blue}
.console-error {color:#c00}

DIV.console {
  border: 1px solid gray;
  background-color: #ccc;
  width: 600px;
}
</style>
<h2>Autentizační konzole</h2>
<div class="console" onclick="confoc()">
<pre><div class="term" id="divTerm">{TERM}</div></pre>
<table class="console" width="100%">
<tr><td width="30">authc></td><td>{CMDLINE}</td><td width="20">{submit}</td></tr>
</table>
</div>
<script language="JavaScript">
var isIE = (navigator.appName.indexOf("Microsoft") != -1);
var cmdhist = new Array("{CMDHIST}");
var cmdhist_i = cmdhist.length;

function confoc() {
  document.getElementById("CMDLINE").focus();
}

function scrolldown() {
  var objDiv = document.getElementById("divTerm");
  objDiv.scrollTop = objDiv.scrollHeight;
}
function cmdkeyup(event) {
  var cmdline = document.getElementById("CMDLINE");
  var keycode = event.keyCode;
  
  switch (keycode) {
    case 38: if (cmdhist_i) cmdline.value = cmdhist[--cmdhist_i]; break;
    case 40:
      if (cmdhist_i < cmdhist.length)
        cmdline.value = cmdhist[++cmdhist_i];
      if (cmdhist_i == cmdhist.length) cmdline.value = '';
    break;
  }
}



function init() {
  $("#CMDLINE").keyup(cmdkeyup);
  scrolldown();
  confoc();
}

$(document).ready(init);
</script>
