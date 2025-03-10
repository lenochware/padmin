<?elements
class grid
link ID route "logs/id:{ID}" skip
string DT lb "Čas"
string USERNAME lb "Uživatel"
string LOGGERNAME lb "Logger"
string ACTIONNAME lb "Akce"
string CATEGORY lb "Kategorie"
string UA
string IP
string size_mb skip
string ITEM_ID lb "Položka"
string MESSAGE size "10" tooltip lb "Zpráva"
link lnclean route "logs/cleanup" lb "Vyčistit" skip
pager pager pglen "40" nohide
?>
<h2>Záznamy logu</h2>
<table class="grid strips">
  <tr>
  	<th>#</th>
  	{grid.labels}
  </tr>
{block items}
  <tr id="id-{ID.value}">
  	<td>{ID} </td>
  	{grid.fields}
  </tr>
{block else}
  <tr><td colspan="20" align="center">Nenalezeny žádné položky.</td></tr>
{/block}
</table>
<div class="pager">{pager} | Prvních {pager.total} záznamů. Velikost logu: {size_mb} MB  &nbsp; {lnclean}</div>

<script>
	if('{SEL_ID}') $('#id-{SEL_ID}').css("background-color", "#9f9");
</script>