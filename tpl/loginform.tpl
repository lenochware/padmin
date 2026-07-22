<?elements
class form route "account" html_class "padmin" html5
input username lb "Uživatelské jméno:" required
input password lb "Heslo:" password required
button login lb "Přihlásit"
?>

<TABLE class="loginform">
<tr><td colspan="3"><h1>Přihlášení</h1></td></tr>
{form.fields}
</TABLE>
<script language="JavaScript">
document.getElementById('username').focus();
</script>
