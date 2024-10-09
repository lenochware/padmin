<?elements
class form html_class "padmin locale-form" route "locale/textId:{textId}" html5
button insert lb "Přidat" onclick "updateText(0)" noprint
button update lb "Uložit" onclick "updateText({textId})" noprint
button delete lb "Smazat" confirm "Opravdu smazat?" noprint
?>
<TABLE width="600">
<tr><td colspan="2"><h1>Formulář jazyka</h1></td></tr>
<tr><td width="80"></td></tr>
{form.fields}
<tr><td colspan="2"><div class="result"></div></td></tr>
</TABLE>