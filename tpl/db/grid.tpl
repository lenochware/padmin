<?elements
class templatefactory create "grid"
string elements noescape
?>
<:?elements
class grid
block b_update noprint
{elements}
pager pager pglen "1000"
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

	.upd {
		background-color: #ffc;
	}

</style>
<h3>{:htitle:}</h3>

{:copy_form:}

<table class="grid">
  <tr>
  	<th width="30"><input type="checkbox" id="check-all" checked="checked" value="1"></th>
  {block head}<th>{:{name}.lb:}</th>{/block}
  </tr>
{:block items:}
  <tr class="{:__status:}">
  	<td><input type="checkbox" class="csel" name="data[__primary][]" value="{:__primary:}" checked></td>
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
	function init()
	{
		$("#check-all").change(function() {
	    $(".csel").prop('checked', $(this).prop('checked'));
		});
	}

	$(document).ready(init);
</script>