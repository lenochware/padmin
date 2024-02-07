<?elements
class form route "roles/{GET}" html_class "padmin" html5
string TITLE skip
input SNAME lb "Název:" required size "50/100"
input ANNOT lb "Popisek:" required size "50/100"
button insert lb "Přidat" noprint
button update lb "Uložit" noprint
button delete lb "Smazat" noprint confirm "Opravdu smazat?"
?>
<TABLE>
<tr><td><h1>Položka číselníku {TITLE}</h1></td></tr>
{form.fields}
</TABLE>
