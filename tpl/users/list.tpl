<?elements
class grid
string ID lb "#"
string USERNAME lb "Uživatelské jméno" sort
string FULLNAME lb "Jméno a příjmení"
string USERROLES lb "Role" size "20" tooltip
string EMAIL lb "Email" size "20" tooltip
string ANNOT lb "Poznámka" size "20" tooltip
string LAST_LOGIN lb "Posl. přihlášení" sort date "d.m.Y H:i"
string DT lb "Dat.vytvoření" sort date
string ACTIVE lb "Aktivní" sort skip
bind inactive list "0,inactive,1," skip field "ACTIVE"
link lnedit lb "Editovat" route "users/edit/id:{ID}" skip
link lnadd lb "Přidat uživatele" route "users/add" skip
link lnexport lb "Exportovat" route "users/export" skip
pager pager pglen "20" nohide
?>
<h2>Seznam uživatelů</h2>
<table class="grid" id="AUTH_USERS">
  <tr>{grid.labels}<th>{ACTIVE.lb}</th></tr>
{block items}
<tr class="link {inactive}" onclick="{lnedit.js}" title="{lnedit.lb}">{grid.fields}
  <td>{if ACTIVE}<i class="fa fa-check-square-o"></i>{/if}{if not ACTIVE}<i class="fa fa-square-o"></i>{/if}</td>
</tr>
{block else}
<tr>
  <td colspan="10" align="center">Nenalezeny žádné položky.</td>
</tr>
{/block}
<tr><td colspan="9">{lnadd} | {lnexport}</td></tr>
</table>
<div class="pager">{pager} | {pager.total} záznamů</div>
