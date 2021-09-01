<?elements
class form route "menu" html_class "padmin" html5
select TREE_ID lookup "tree" emptylb "- Nový -" lb "Strom"
input TITLE lb "Název" required
text MENU default "PATH|ROUTE" lb "Definice"
button submit lb "Uložit"
button delete lb "Smazat"
?>

<TABLE>
<tr><td><h1>Importovat menu</h1></td></tr>
{form.fields}
</TABLE>
<script language="JavaScript">
function setform() {
  var id = $("#TREE_ID").val();
  if (!id) return;
  $("#MENU").load('?r=menu/ajax_load&id='+ id);
  $("#TITLE").val($('#TREE_ID option:selected').text());
}

function init() {
  $("#TREE_ID").change(setform);
}

$(document).ready(init);
</script>
