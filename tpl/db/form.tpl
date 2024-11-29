<?elements
class form name "copy-form" route "db/importCsv" html5
text csv-data
button copy lb "Copy" onclick "copySelected()"
button paste lb "Paste" onclick "pasteSelected()"
?>

{form.fields}

<script>
function copySelected()
{
	let a = [];
	$(".csel:checked").each(function() {
		a.push(this.value);
	})

	$("#csv-data").load('?r=db/csv&selected=' + a.join(), function() {
		navigator.clipboard.writeText($("#csv-data").val());
	});
}

function pasteSelected()
{
	navigator.clipboard
  .readText()
  .then((clipText) => { $("#csv-data").val(clipText); $("#copy-form").submit()});
}
</script>