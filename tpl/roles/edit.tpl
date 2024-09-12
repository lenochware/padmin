<?elements
class gridform
primary ID
string TITLE skip
radio RSET lb "Konfigurace" list "1,Povolit,2,Zakázat,3,Dědit" html_class "inline"
sort SRSET lb "Konfigurace" sort "RSET"
string SNAME lb "Název" sort
string ANNOT lb "Popis" sort
string STATUS lb "Status"
input RVAL
string USER_ID
button rupdate lb "Uložit"
button back lb "Zpět" onclick "history.back()"
pager pager pglen "20"
?>
<h2>Přiřazení oprávnění pro {TITLE}</h2>
<table class="grid">
  <tr>
    <th width="200">{SRSET}</th>
    <th>{SNAME.lb}</th>
    <th>{ANNOT.lb}</th>
    <th>{STATUS.lb}</th>
  </tr>
  {block items}
  <tr>
    <td>{ID}{if RBOOL}{RSET}{/if} {if not RBOOL}{RVAL}{/if}</td>
    <td>{SNAME}</td>
    <td>{ANNOT}</td>
    <td>{STATUS}</td>
  </tr>
  {block else}
  <tr><td colspan=8 style="width: auto">Nenalezeny žádné položky.</td></tr>
  {/block}
</table><br>
{rupdate} {back}
<div class="pager">{pager} &nbsp; {pager.all}</div>
