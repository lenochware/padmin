<?elements
class grid
string ID lb "#"
string DT lb "Čas"
string USERNAME lb "Uživatel"
string LOGGERNAME lb "Logger"
string ACTIONNAME lb "Akce"
string CATEGORY lb "Kategorie"
string UA
string IP
string size_mb skip
string ITEM_ID lb "Položka"
string MESSAGE size "10" tooltip lb "Zpráva" noescape
pager pager pglen "20" nohide
?>
<h2>Záznamy logu</h2>
<table class="grid strips">
  <tr>{grid.labels}</tr>
{block items}
  <tr>{grid.fields}</tr>
{block else}
  <tr><td colspan="20" align="center">Nenalezeny žádné položky.</td></tr>
{/block}
</table>
<div class="pager">{pager} | Prvních {pager.total} záznamů. Velikost logu: {size_mb} MB</div>
