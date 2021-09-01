<?elements
class form route "lookups/{GET}" html_class "padmin" html5
input ID lb "Id:" required
string CNAME skip
input LABEL lb "Popisek:" required
input POSITION lb "Pozice:"
button insert lb "Přidat" noprint
button update lb "Uložit" noprint
button delete lb "Smazat" noprint
?>

<TABLE>
<tr><td><h1>Položka číselníku {CNAME}</h1></td></tr>
{form.fields}
</TABLE>
