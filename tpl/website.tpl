<?elements
string LAST_LOGIN date
meta HEAD scripts "css/padmin.css,css/menu.css,js/jquery.js,libs/pclib/assets/pclib.js"
block user noprint
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{APPNAME}{if TITLE} | {TITLE}{/if}</title>
  {HEAD}
</head>
<body>
<div id="site-top">
<h1>{APPNAME}</h1>
{block user}
<div style="position: absolute; top: 80px; right: 10px;">
  <img src="images/user.gif" class="bullet">
  Uživatel: {UNAME} | <a href="#" onclick="if (confirm('Odhlásit se?')) pclib.redirect('account/logout')">odhlásit</a>
</div>
{/block}
<div style="position: absolute; top: 80px; left: 10px;">» {NAVIG}</div>
<div style="position: absolute; top: 108px; right: 10px;">v{VERSION}</div>
</div>
<div id="menu">{MENU}</div>

<div id="site-content">
{PRECONTENT}{CONTENT}
</div>
<div class="site-footer"></div>
</body>
</html>
