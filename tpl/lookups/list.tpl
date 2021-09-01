<?elements
class grid
string CNAME lb "Číselník"
link lnedit lb "Editovat" route "lookups/view/lookup:{CNAME}" skip
link lnadd lb "Přidat číselník" route "lookups/addlookup" skip
pager pager pglen "20"
?>
<h2>Číselníky</h2>
<table class="grid strips">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link" onclick="{lnedit.js}">{grid.fields}</tr>
{block else}
  <tr><td colspan=8>Nenalezeny žádné položky.</td></tr>
{/block}
<tr><td>{lnadd}</td></tr>
</table>
<div class="pager">{pager}</div>
