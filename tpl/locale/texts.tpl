<?elements
class grid
bind TRANSLATOR lb "Překladač" query "select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=1" sort
bind LANG lb "Jazyk" query "select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=2" emptylb "source" sort
link lnedit route "locale/edit/tr:{TRANSLATOR}/lang:{LANG}" skip
link lnadd route "locale/add" lb "Přidat nový jazyk" skip
pager pager pglen "20" nohide
?>
<h2>Texty</h2>
<table class="grid">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link" onclick="{lnedit.js}">{grid.fields}</tr>
{block else}
  <tr><td colspan=9>Nenalezeny žádné položky.</td></tr>
{/block}
<tr><td colspan=9>{lnadd}</td></tr>
</table>
<div class="pager">{pager}</div>