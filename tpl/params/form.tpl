<?elements
class form name "params_form" html_class "padmin" jsvalid
input PARAM_NAME lb "Systémový název" size "50" noedit required
input TITLE lb "Titulek" size "100" noedit
input PARAM_VALUE lb "Hodnota"
button insert lb "Přidat" noprint skip
button update lb "Uložit" noprint skip
button back lb "Zpět" route "params" skip
?>
<table class="form" width="100%">
<td colspan="2">
<h1>{TITLE.value}</h1>
</td>
{form.fields}
<tr><td colspan="3">{insert} {update} {back}</td></tr>
<tr><td colspan="3">Položky označené (*) jsou povinné.</td></tr>
</table>