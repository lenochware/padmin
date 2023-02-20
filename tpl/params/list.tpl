<?elements
class grid name "params" singlepage
string PARAM_NAME lb "Systémový název" sort
string TITLE lb "Titulek" size "50" tooltip sort
string PARAM_VALUE lb "Hodnota"

link lnadd route "params/add" lb "Přidat nový" skip
link lnedit route "params/edit/id:{ID}" skip
pager pager pglen "20"
?>
<h2>Parametry aplikace</h2>
<table class="grid">
  <tr>{grid.labels}</tr>
{BLOCK items}
  <tr class="link" onclick="{lnedit.js}">{grid.fields}</tr>
{BLOCK else}  
  <tr><td colspan="10" align="center">Nenalezeny žádné výsledky.</td></tr>
{/BLOCK}
</table>
<ul class="pagination">{pager}</ul>