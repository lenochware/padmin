<?elements
class form route "locale/{GET}" html_class "padmin" html5
input TRANSLATOR lb "Překladač" required noedit
input LANG lb "Jazyk" default "source" required noedit
text TEXTS lb "Texts:" default "<texts></texts>"
button insert lb "Přidat" noprint
button update lb "Uložit" noprint
button delete lb "Smazat" noprint
button back lb "Zpět" onclick "history.back()"
?>
<style type="text/css">
#TEXTS {height:300px}
</style>
<TABLE>
<tr><td><h1>Formulář jazyka</h1></td></tr>
{form.fields}
</TABLE>

<script language="JavaScript">
function deleteprompt() {
  if(!confirm('Opravdu smazat jazyk a všechny texty?')) return false;
}

function init() {
  $('#delete').click(deleteprompt);
}

$(document).ready(init);
</script>