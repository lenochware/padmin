<?elements
class grid
string table lb "Tabulky"
link lntable href "?r=db/table&table={table}" lb "Detail" skip
pager pager
?>
<h2>Tabulky</h2>
<table class="grid" id="AUTH_USERS">
  <tr>{grid.labels}</tr>
{block items}
<tr class="link" onclick="{lntable.js}" title="{lntable.lb}">{grid.fields}</td>
</tr>
{block else}
<tr>
  <td colspan="10" align="center">Nenalezeny žádné položky.</td>
</tr>
{/block}