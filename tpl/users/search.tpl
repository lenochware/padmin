<?elements
class form route "users" html_class "padmin"
input USERNAME
input ANNOT
select ROLE query "select ID,SNAME from AUTH_ROLES order by SNAME"
select ACTIVE list "1,Ano,0,Ne"
check new_users lb "Noví" html_title "za poslední měsíc"
check inactive lb "Nepřihlášení 1y"
button search lb "Hledat"
button showall lb "Ukaž všechny"
?>
<TABLE width="100%">
<TR>
<TD colspan="2">
	Uživ. jméno: {USERNAME} Anotace: {ANNOT} Role: {ROLE} Aktivní: {ACTIVE} {new_users} {new_users.lb} {inactive} {inactive.lb}

</TD>
<TD align="right"> {search} {showall}</TD>
</TR>
</TABLE>
