<?elements
class grid name "content" singlepage
string NAME lb "Systémový název" sort
string TITLE lb "Titulek" size "50" tooltip sort
string UPDATED_AT lb "Akualizováno" date
link lnadd route "content/add" lb "Přidat nový" skip
link lnedit route "content/edit/id:{ID}" skip
pager pager pglen "20"
?>
<h2>Šablony stránek</h2>

<input type="text" id="search" onkeyup="filterTable('mainList')" placeholder="Hledat..">
<br><br>

<table id="mainList" class="grid strips">
	<tr>{grid.labels}</tr>
{BLOCK items}
  <tr class="link" onclick="{lnedit.js}">{grid.fields}</tr>
{BLOCK else}  
  <tr><td colspan="10" align="center">Nenalezeny žádné výsledky.</td></tr>
{/BLOCK}
</table>
<div class="pager">{pager} | {pager.all}</div>