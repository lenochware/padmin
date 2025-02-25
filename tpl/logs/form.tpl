<?elements
class form name "logform" html5 html_class "padmin" html_onsubmit "return form_submit()"
select PERIOD list "730,2 roky,365,1 rok" required
button delete lb "Smazat"
?>
<h1>Záznamy logu</h1>
<TABLE>
<tr><td><h1>Smazat staré záznamy</h1></td></tr>
<TR><TD>Starší než: {PERIOD}</TD></TR>
<TR><TD>{delete}</TD></TR>
</TABLE>
<script language="JavaScript">

function form_submit() {
	if (!confirm("Opravdu smazat? (Může trvat několik minut.)")) return false;
	$('#delete').buttonState('loading');
  pclib.redirect('logs/delete/period:{#PERIOD}');
  return false;
}
</script>
