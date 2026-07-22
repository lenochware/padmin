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
  <script>
    (function () {
      try {
        var t = localStorage.getItem('padmin-theme') ||
          (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        document.documentElement.setAttribute('data-theme', t);
      } catch (e) {}
    })();
  </script>
  <title>padmin{if TITLE} | {TITLE}{/if}</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'><rect width='16' height='16' rx='3' fill='%235f2c82'/><circle cx='8' cy='8' r='3' fill='none' stroke='%23ffaa00' stroke-width='1.6'/></svg>">
  {HEAD}
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>
  <div id="site-header">
    <header id="site-top">
      <div class="brand">
        <i class="fa fa-cogs" aria-hidden="true"></i>
        <span class="app-name">pclib admin</span>
      </div>
      <div class="header-actions">
        {block user}
        <div class="user-info">
          <i class="fa fa-user" aria-hidden="true"></i>
          Uživatel: {UNAME} | <a href="#" onclick="if (confirm('Odhlásit se?')) pclib.redirect('account/logout')">odhlásit</a>
        </div>
        {/block}
        <a href="#" class="theme-toggle" role="button" title="Přepnout motiv" aria-label="Přepnout světlý/tmavý motiv" onclick="padminToggleTheme(); return false;"><i class="fa fa-moon-o" aria-hidden="true"></i></a>
      </div>
    </header>

    <nav id="menu">
      {MENU}
      <div class="menu-version">{if VERSION}padmin v{VERSION}{/if}</div>
    </nav>
  </div>

  <div id="site-body">
    <main id="site-content">
      <div class="breadcrumb">» {NAVIG}</div>
      {PRECONTENT}{CONTENT}
    </main>
  </div>

  <footer class="site-footer"></footer>

  <script>
    $(document).ready(init_global);

    function padminToggleTheme() {
      var next = (document.documentElement.getAttribute('data-theme') === 'dark') ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      try { localStorage.setItem('padmin-theme', next); } catch (e) {}
      padminSyncThemeIcon();
    }
    function padminSyncThemeIcon() {
      var i = document.querySelector('#site-top .theme-toggle i');
      if (!i) return;
      var dark = document.documentElement.getAttribute('data-theme') === 'dark';
      i.className = 'fa ' + (dark ? 'fa-sun-o' : 'fa-moon-o');
    }
    padminSyncThemeIcon();
  </script>
</body>
</html>
