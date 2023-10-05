<?elements
string LAST_LOGIN date
head HEAD scripts "css/padmin.css,css/menu.css,js/jquery.js,vendor/lenochware/pclib/pclib/assets/pclib.js,js/global.js"
messages PRECONTENT
string TITLE
string UNAME
string MENU
string VERSION
string CONTENT noescape
navigator NAVIG
block user noprint
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>padmin{if TITLE} | {TITLE}{/if}</title>
  {HEAD}
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
<div id="site-top">
<i class="fa fa-cogs" style="font-size:50px;  text-shadow: 2px 2px 4px #333; padding: 10px" aria-hidden="true"></i>
{block user}
<div style="position: absolute; top: 80px; right: 10px;">
  <i class="fa fa-user" style="color:orange" aria-hidden="true"></i>
  Uživatel: {UNAME} | <a href="#" onclick="if (confirm('Odhlásit se?')) pclib.redirect('account/logout')">odhlásit</a>
</div>
{/block}
<div style="position: absolute; top: 80px; left: 10px;">» {NAVIG}</div>
<div style="position: absolute; top: 108px; right: 10px;">
padmin {if VERSION}v{VERSION}{/if}

</div>
</div>
<div id="menu">{MENU}</div>

<div id="site-content">
{PRECONTENT}{CONTENT}
</div>
<div class="site-footer"></div>
<script>
$(document).ready(init_global);
</script>
</body>
</html>
