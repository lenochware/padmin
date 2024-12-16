<?elements
class form name "content_form" html_class "padmin" jsvalid
input NAME lb "Systémový název" size "50" noedit required
input TITLE lb "Titulek" size "100" noedit
text BODY lb "Šablona" html_style "width:100%;height:300px"
button insert lb "Přidat" noprint skip
button update lb "Uložit" noprint skip
button back lb "Zpět" route "content" skip
?>
<table class="form" width="100%">
<td colspan="2">
<h1>{TITLE.value}</h1>
</td>
{form.fields}
<tr><td colspan="3">{insert} {update} {back}</td></tr>
<tr><td colspan="3">Položky označené (*) jsou povinné.</td></tr>
</table>