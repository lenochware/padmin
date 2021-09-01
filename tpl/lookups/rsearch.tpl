<?elements
class form html_class "padmin"
input SNAME
check ALLOWED
button search lb "Hledat"
button showall lb "Ukaž všechny"
?>
<TABLE width="100%">
<TR>
<TD colspan="2">Oprávnění: {SNAME} Povolen: {ALLOWED}</TD>
<TD align="right"> {search} {showall}</TD>
</TR>
</TABLE>
