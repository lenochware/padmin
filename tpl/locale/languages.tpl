<?elements
class grid
string ID lb "Id" sort
string LABEL lb "Jazyk" sort
link lnedelete route "locale/languageDelete" lb "Smazat" confirm "Opravdu smazat?"
pager pager pglen "20" nohide
?>
<h2>Jazyky</h2>
<table class="grid">
  <tr>
  	{grid.labels}
  </tr>
{block items}
  <tr class="link">{grid.fields}</tr>
{block else}
  <tr><td colspan=9>Nenalezeny žádné položky.</td></tr>
{/block}

<tr><td colspan=9>
  <a href="javascript:void(0)" onclick="editText(0)">Přidat</a>
</td></tr>
</table>

<div class="pager">{pager}</div>

<script>
</script>