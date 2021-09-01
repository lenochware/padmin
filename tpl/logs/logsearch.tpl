<?elements
class form name "search" route "logs" html_class "padmin"
select LOGGER query "select id,label from LOGGER_LABELS where category=1"
input USERNAME
input ACTIONNAME
select CATEGORY query "select id,label from LOGGER_LABELS where category=4"
button search lb "Hledat"
button showall lb "Ukaž všechny"
?>
<TABLE width="100%">
<TR>
<TD colspan="2">Logger: {LOGGER} Username: {USERNAME} Akce: {ACTIONNAME} Kategorie: {CATEGORY}</TD>
<TD align="right"> {search} {showall}</TD>
</TR>
</TABLE>