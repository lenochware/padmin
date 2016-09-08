<?elements
class form route "rlookups/{GET}" html_class "padmin" html5
input SNAME lb "Název:" required
input ANNOT lb "Popisek:" required
button insert lb "Přidat" noprint
button update lb "Uložit" noprint
button delete lb "Smazat" noprint
?>
<TABLE>
<tr><td><h1>Položka číselníku {TITLE}</h1></td></tr>
{form.fields}
</TABLE>
