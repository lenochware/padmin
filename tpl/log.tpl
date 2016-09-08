<?elements
class grid
string DT lb "Čas"
string USERNAME lb "Uživatel"
string LOGGERNAME lb "Logger"
string ACTIONNAME lb "Akce"
string CATEGORY lb "Kategorie"
string UA
string IP
string ITEM_ID lb "Položka"
string MESSAGE size "10" tooltip lb "Zpráva"
pager pager pglen "20" nohide
?>
<h2>Záznamy logu</h2>
<table class="grid strips">
  <tr>{grid.labels}</tr>
{block items}
  <tr>{grid.fields}</tr>
{block else}
  <tr><td colspan=9>Nenalezeny žádné položky.</td></tr>
{/block}
</table>
<div class="pager">{pager} | Prvních {pager.total} záznamů.</div>
