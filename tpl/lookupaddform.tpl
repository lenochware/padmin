<?elements
class form html_class "padmin" html5 html_onsubmit "return form_submit()"
input CNAME lb "Název číselníku" required
button add lb "Přidat"
?>
<TABLE>
<tr><td><h1>Nový číselník</h1></td></tr>
{form.fields}
</TABLE>
<script language="JavaScript">

function form_submit() {
  pclib.redirect('lookups/add/lookup:{#CNAME}');
  return false;
}
</script>
