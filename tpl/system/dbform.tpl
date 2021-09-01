<?elements
class form route "db/{GET}" html_class "padmin" html5
select table lb "Tabulka" required
string grid skip noescape
button show lb "Zobrazit"
button sync lb "Synchronizovat" confirm "Opravdu synchronizovat?"
?>

<TABLE>
<tr><td><h1>Synchronizace databáze</h1></td></tr>
{form.fields}
</TABLE>

{grid}
