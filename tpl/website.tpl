<?elements
string LAST_LOGIN date
head HEAD scripts "css/padmin.css,css/menu.css,js/jquery.js,{pclib}/www/pclib.js,js/global.js"
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
<html lang="cs">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#5f2c82">
  <title>padmin{if TITLE} | {TITLE}{/if}</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><rect width='16' height='16' rx='3' fill='%235f2c82'/><circle cx='8' cy='8' r='3' fill='none' stroke='%23ffaa00' stroke-width='1.6'/></svg>">
  {HEAD}
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <header id="site-top">
    <i class="fa fa-cogs" style="font-size:50px; text-shadow: 2px 2px 4px #333; padding: 10px" aria-hidden="true"></i>
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
  </header>

  <nav id="menu">{MENU}</nav>

  <div id="site-body">
    <main id="site-content">
      {PRECONTENT}{CONTENT}
    </main>
  </div>

  <footer class="site-footer"></footer>

  <script>
    $(document).ready(init_global);
  </script>
</body>
</html>
