<?elements
class form name "logform" html5 html_class "padmin" html_onsubmit "return form_submit()"
select PERIOD list "24,2 roky,12,1 rok,6,půl roku,3,3 měsíce" required
button delete lb "Smazat"
?>
<TABLE>
<tr><td><h1>Smazat staré záznamy</h1></td></tr>
<TR><TD>Starší než: {PERIOD}</TD></TR>
<TR><TD>{delete}</TD></TR>
</TABLE>
<script language="JavaScript">

function form_submit() {
  pclib.redirect('logs/delete/period:{#PERIOD}');
  return false;
}
</script>
