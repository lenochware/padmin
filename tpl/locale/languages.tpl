<?elements
class grid
string ID lb "Id" sort
string LABEL lb "Jazyk" sort
action count route "locale/count/id:{ID}" lb "Počet textů"
link lnedelete route "locale/languageDelete/id:{ID}" lb "Smazat" confirm "Opravdu smazat?"
pager pager pglen "200" nohide
?>
<h2>Jazyky</h2>
<table class="grid">
  <tr>
  	{grid.labels}
  </tr>
{block items}
  <tr class="link">{grid.fields}</tr>
{block else}
  <tr><td colspan="10" align="center">Nenalezeny žádné položky.</td></tr>
{/block}

</table>
<br>
<form method="GET">
	<input type="hidden" name="r" value="locale/languages">
	Nový jazyk: <input type="text" name="add" maxlength="2"> <button type="submit">Přidat</button>
</form>