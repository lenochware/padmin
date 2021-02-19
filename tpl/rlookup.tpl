<?elements
class grid
string ID lb "#" sort
string TITLE skip
string SNAME lb "Název" sort
string ANNOT lb "Popis"
pager pager pglen "20"
link lnedit lb "Editovat" route "rlookups/edit/id:{ID}/lookup:{LOOKUP}" skip
link lnadd lb "Přidat" route "rlookups/add/lookup:{LOOKUP}" skip
?>
<h2>{TITLE}</h2>
<table class="grid strips">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link" onclick="{lnedit.js}">{grid.fields}</tr>
{block else}
  <tr><td colspan=8>Nenalezeny žádné položky.</td></tr>
{/block}
<tr><td colspan=8>{lnadd}</td></tr>
</table>
<div class="pager">{pager} &nbsp; {pager.all}</div>
