<?elements
class form route "users" html_class "padmin"
input USERNAME
input ANNOT
select ROLE query "select ID,SNAME from AUTH_ROLES order by SNAME"
button search lb "Hledat"
button showall lb "Ukaž všechny"
?>
<TABLE width="100%">
<TR>
<TD colspan="2">Uživ. jméno: {USERNAME} Anotace: {ANNOT} Role: {ROLE}</TD>
<TD align="right"> {search} {showall}</TD>
</TR>
</TABLE>
