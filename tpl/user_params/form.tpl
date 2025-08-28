<?elements
class form route "userparams/{GET}" html_class "padmin" html5
input example lb "Ukázkový parametr:" noprint
button update lb "Uložit" noprint
?>
<p class="message">Uživatelské parametry jsou vypnuté.</p>
<p>Libovolné uživatelské parametry můžete nadefinovat nastavením šablony <code>tpl/user_params/form.tpl</code>.</p>
<p>K parametrům uživatele lze přistupovat pomocí funkce <code>$user->getParam('nazev_parametru')</code>.</p>
<table>
	{form.fields}
</table>