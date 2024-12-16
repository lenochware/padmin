<?elements
class grid name "mails" singlepage
string TO lb "Komu" sort
string SUBJECT lb "Název" size "50" tooltip sort
bind STATUS lb "Stav" list "0,Nový,1,Naplánovaný,2,Odeslaný,9,Chyba"
string SEND_AT lb "Odesláno" date
string CREATED_AT lb "Vytvořeno" date
link lnadd route "mails/add" lb "Přidat nový" skip
link lnedit route "mails/edit/id:{ID}" skip
pager pager pglen "20"
?>
<h2>Odeslané emaily</h2>

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