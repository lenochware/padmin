<?elements
class grid
string name
link lncreate lb "Vytvořit migraci" route "migrations/create" skip
pager pager pglen "100"
?>
<style type="text/css">
.selected { background-color: #cfc;}
</style>
<h2>{TITLE}</h2>
<table class="grid">
  <tr>{grid.labels}</tr>
{block items}
  <tr class="link status-{status}" onclick="selitem(this,'{name}')">{grid.fields}</tr>
{block else}
  <tr><td colspan=9>Nenalezeny žádné položky.</td></tr>
{/block}
</table>
<div class="pager">{pager}</div>
{lncreate}

<script language="JavaScript">
var selected;
function selitem(tr,name) {
  if (selected) {
    if (selected == name) {
      $(tr).removeClass('selected');
      selected = null;
      return;
    }
    document.location = '?r=migrations/show_migration&fromFile='+selected+'&toFile='+name;
  }
  else {
    selected = name;
    $(tr).addClass('selected');
  }
  
}
</script>