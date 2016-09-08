<?elements
class grid
string name
string status
link lnedit route "migrations/show_columns/table:{name}/{GET}" skip
pager pager pglen "100"
?>
<style type="text/css">
.status-added { background-color: #cfc;}
.status-removed { background-color:#fcc;}
.status-modified { background-color:#ffc;}
.status-same { background-color:transparent;}
</style>
<h2>{TITLE}</h2>
<table class="grid">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link status-{status}" onclick="{lnedit.js}">{grid.fields}</tr>
{block else}
  <tr><td colspan=9>Nenalezeny žádné položky.</td></tr>
{/block}
</table>
<div class="pager">{pager}</div>
