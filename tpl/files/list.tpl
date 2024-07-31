<?elements
class grid
string ID lb "#"
string ORIGNAME lb "Název"
string ENTITY_TYPE lb "Entita"
string ENTITY_ID  lb "Id entity"
string MIMETYPE lb "Typ"
string SIZE lb "Velikost"
string DT date "d.m.Y" lb "Vytvořeno"
string TOTAL_SIZE skip
link lnedit lb "Detail" route "files/edit/id:{ID}" skip
link lnshow lb "Detail" route "files/show/id:{ID}"
pager pager pglen "20" nohide
?>
<style>
	.SIZE {
		text-align: right;
		padding-right: 1em;
	}
</style>
<h2>Nahrané soubory</h2>
<table class="grid">
  <tr>{grid.labels}</tr>
{block items}
  <tr>{grid.fields}</tr>
{block else}
  <tr><td colspan="12" align="center">Nenalezeny žádné položky.</td></tr>
{/block}
</table>
<div class="pager">{pager} | {pager.total} záznamů | {TOTAL_SIZE}</div>
