<?elements
class grid
string ID lb "#"
string USERNAME lb "Uživatelské jméno" sort
string FULLNAME lb "Jméno a příjmení"
string USERROLES lb "Role"
string EMAIL lb "Email"
string ANNOT lb "Poznámka"
string LAST_LOGIN lb "Posl. přihlášení" sort date "%d.%m.%Y %H:%M"
string DT lb "Dat.vytvoření" sort date
string ACTIVE lb "Aktivní" sort skip
link lnedit lb "Editovat" route "users/edit/id:{ID}" skip
link lnadd lb "Přidat uživatele" route "users/add" skip
pager pager pglen "20" nohide
?>
<h2>Seznam uživatelů</h2>
<table class="grid" id="AUTH_USERS">
  <tr>{grid.labels}<th>{ACTIVE.lb}</th></tr>
{block items}
  <tr class="link" onclick="{lnedit.js}" title="{lnedit.lb}">{grid.fields}<td><img src="images/check{ACTIVE}.gif"></td></tr>
{/block}
<tr><td colspan="9">{lnadd}</td></tr>
</table>
<div class="pager">{pager} | {pager.total} záznamů</div>
