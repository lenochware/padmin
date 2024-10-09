<?elements
class form route "locale" html_class "padmin" html5
select LANG lb "Jazyk" required
input FILE lb "Csv text" file required
button import lb "Importovat"
button back lb "Zpět" onclick "history.back()"
?>
<TABLE>
<tr><td><h1>Import textů</h1></td></tr>
{form.fields}
</TABLE>