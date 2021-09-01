<?elements
class form route "locale" html_class "padmin" html5
select TRANSLATOR lb "Překladač" query "select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=1" default "1" required
select LANG lb "Jazyk" query "select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=2" required
input COLUMN lb "Sloupec s textem k překladu" default "1" size "2" required number
text CSV lb "CSV:" required
button import lb "Importovat"
button back lb "Zpět" onclick "history.back()"
?>
<style type="text/css">
#CSV {height:300px}
</style>
<TABLE>
<tr><td><h1>Formulář jazyka</h1></td></tr>
{form.fields}
</TABLE>