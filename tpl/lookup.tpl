<?elements
class grid
string ID lb "#" sort
string CNAME skip
string LABEL lb "Popisek" sort
string POSITION lb "Pozice" sort
link lnedit lb "Editovat" route "lookups/edit/lookup:{CNAME}/id:{GUID}" skip
link lnadd lb "Přidat" route "lookups/add/lookup:{CNAME}" skip
pager pager pglen "20"
?>
<h2>Číselník {CNAME}</h2>
<table class="grid strips">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link" onclick="{lnedit.js}">{grid.fields}</tr>
{block else}
  <tr><td colspan=8>Nenalezeny žádné položky.</td></tr>
{/block}
<tr><td colspan="3">{lnadd}</td></tr>
</table>
<div class="pager">{pager}</div>
