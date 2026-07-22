<?elements
class form route "console"
string TERM noescape
string CMDHIST noescape
input CMDLINE html_autocomplete "off" size "50/200"
button submit lb "Ok"
?>
<style type="text/css">
div.console {
  width: 100%;
  box-sizing: border-box;
  border: 1px solid var(--border-color);
  border-radius: 0.25rem;
  background-color: #1e1e1e;
  color: #e6e6e6;
  padding: 0.5rem 0.75rem;
  font: 13px/1.5 ui-monospace, "Cascadia Code", Consolas, "Liberation Mono", monospace;
}

div.console pre {
  margin: 0;
}

div.term {
  width: 100%;
  height: 320px;
  overflow: auto;
}

div.console table.console {
  margin-top: 0.5rem;
  border-top: 1px solid #3a3f44;
}

div.console td {
  font: inherit;
  padding-top: 0.35rem;
}

div.console input {
  font: inherit;
  width: 100%;
  border: 0;
  padding: 0;
  background-color: transparent;
  color: #e6e6e6;
  outline: none;
}

.console-value { color: #4ec9b0; }
.console-cmd   { color: #569cd6; }
.console-error { color: #f14c4c; }
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
