<?elements
class templatefactory create "grid" sort
string elements noescape
?>
<:?elements
class grid
block b_update noprint
{elements}
pager pager pglen "20"
?:>
<style>
	form.padmin table.grid {
		background-color: initial;
		padding: initial;
		border: initial;
		margin: initial;
		box-shadow: initial;
	}

	td.value { 
		max-width: 150px;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
	}

	.ins {
		background-color: #cfc;
	}

	.del {
		background-color: #fcc;
	}

	.sel {
		background-color: #99ccff66;
		border-top: 1px solid #ccc;
		border-bottom: 1px solid #ccc;
	}

	TABLE.grid TR.sel:hover {
		background-color: #99ccff99;
	}



</style>
<h3>{:htitle:}</h3>

{:copy_form:}

<table class="grid no-strips" id="db-grid">
	<tr>
	{block head}<th>{:{name}.lb:}</th>{/block}
	</tr>
{:block items:}
	<tr class="{:__status:}" id="{:__primary:}">
	{block columns}<td class="value {:__{name}_status:}" title="{:{name}:}" data-old="{:__{name}_old:}">{:{name}:}</td>{/block}
	</tr>
{:block else:}
<tr><td colspan="20" align="center">Žádné změny.</td></tr>

{:/block:}
</table>
<div class="pager">{:pager:}</div>

{:block b_update:}
<button onclick="document.location='?r=db/update'">Update</button>
{:/block:}

<script>
$(document).ready(function () {
  dataGrid("#db-grid");
});
</script>