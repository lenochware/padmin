<?elements
class grid
string id lb "#"
string name lb "Název" sort
string annotation lb "Popis" skip
bind job_type lb "Typ úlohy" lookup "job-type"
bind period lb "Perioda" lookup "job-period"
string first_run_at lb "První spuštění" sort date "d.m.Y H:i"
string last_run_at lb "Poslední spuštění" sort date "d.m.Y H:i"
string last_run_result lb "Výsledek" format "h" size "50" tooltip
string created_at lb "Vytvořeno" sort date "d.m.Y H:i" skip
string active lb "Aktivní" sort skip
link lnedit lb "Editovat" route "jobs/edit/id:{id}" skip
link lnadd lb "Přidat úlohu" route "jobs/add" skip
pager pager pglen "20" nohide
?>
<h2>Plánovač úloh</h2>
<table class="grid" id="JOBS">
  <tr>{grid.labels}<th>{active.lb}</th></tr>
{block items}
  <tr class="link" onclick="{lnedit.js}" title="{lnedit.lb}">{grid.fields}
  	<td>{if active}<i class="fa fa-check-square-o"></i>{/if}{if not active}<i class="fa fa-square-o"></i>{/if}</td></tr>
{block else}
  <tr><td colspan=12>Nenalezeny žádné položky.</td></tr>
{/block}
<tr><td colspan="12">{lnadd}</td></tr>
</table>
<div class="pager">{pager} | {pager.total} záznamů</div>
