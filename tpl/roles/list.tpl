<?elements
class grid
string ID lb "#" sort
string TITLE skip
string SNAME lb "Název" sort
string ANNOT lb "Popis"
pager pager pglen "20" nohide
link lnedit lb "Editovat" route "roles/edit/id:{ID}"
link lnset lb "Nastavení" route "roles/rights/id:{ID}" skip
link lnadd lb "Přidat" route "roles/add" skip
link lnexport lb "Exportovat" route "roles/export" skip
?>
<h2>{TITLE}</h2>
<input type="text" id="search" onkeyup="filterTable('mainList')" placeholder="Hledat..">
<br><br>

<table id="mainList" class="grid strips">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link" onclick="{lnset.js}">{grid.fields}</tr>
{block else}
  <tr><td colspan=8>Nenalezeny žádné položky.</td></tr>
{/block}
<tr><td colspan=8>{lnadd} | {lnexport}</td></tr>
</table>
<div class="pager">{pager} &nbsp; {pager.all}</div>
