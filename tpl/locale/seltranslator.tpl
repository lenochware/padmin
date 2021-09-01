<?elements
class form html_class "padmin" html5
input NAME hidden
select ID query "select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=1 order by LABEL" lb "Vyberte skupinu" required
button seltranslator lb "Vybrat"
?>
<table>
	<tr>
		<td>
		{ID.lb} 
		{ID}<br>
		{seltranslator}
</td>
	</tr>
</table>
<script language="JavaScript">
function init() {
	$('#ID').change(function(){ $('#NAME').val($('#ID :selected').text()); });
	$('#ID').trigger('change');
}
$(document).ready(init);
</script>