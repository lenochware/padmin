<?elements
class grid
string ID lb "#" sort
string TITLE skip
string SNAME lb "Název" sort
string ANNOT lb "Popis"
pager pager pglen "20" nohide
link lnedit lb "Editovat" route "rights/edit/id:{ID}" skip
link lnadd lb "Přidat" route "rights/add" skip
link lnexport lb "Exportovat" route "rights/export" skip
?>
<h2>{TITLE}</h2>
<input type="text" id="search" onkeyup="filterTable('mainList')" placeholder="Hledat..">
<br><br>

<table id="mainList" class="grid strips">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link" onclick="{lnedit.js}">{grid.fields}</tr>
{block else}
  <tr><td colspan=8>Nenalezeny žádné položky.</td></tr>
{/block}
<tr><td colspan=8>{lnadd} | {lnexport}</td></tr>
</table>
<div class="pager">{pager} &nbsp; {pager.all}</div>
