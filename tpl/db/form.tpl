<?elements
class form name "copy-form" route "db/pasteCsv" html5
text csv-data html_class "hidden" lb " "
button copy lb "Copy" onclick "copySelected()"
button paste lb "Paste" onclick "pasteSelected()"
?>


<div>
	<input type="text" id="search" onkeyup="filterTable('db-grid')" placeholder="Hledat..">
	{form.fields} <span id="info-box"></span>
</div>
<br><br>

<script>
function copySelected()
{
	let a = [];
	$("tr.sel").each(function() {
		a.push($(this).attr('{pk}'));
	})

	$("#csv-data").load('?r=db/csv&selected=' + a.join(), function() {
		navigator.clipboard.writeText($("#csv-data").val());
		$("#info-box").html(`Zkopírováno ${ a.length} řádků.`);
	});
}

function pasteSelected()
{
	navigator.clipboard
  .readText()
  .then((clipText) => { $("#csv-data").val(clipText); $("#copy-form").submit()});
}
</script>