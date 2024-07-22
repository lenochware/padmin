<?elements
class form name "search" route "logs" html_class "padmin"
select LOGGER query "select id,label from LOGGER_LABELS where category=1"
input USERNAME
listinput ACTIONNAME query "select id,label from LOGGER_LABELS where category=2"
listinput CATEGORY query "select id,label from LOGGER_LABELS where category=4"
input ITEM_ID
input LOGGED_AT
button search lb "Hledat"
button showall lb "Ukaž všechny"
?>
<TABLE width="100%">
<TR>
<TD colspan="2">Logger: {LOGGER} Username: {USERNAME} Akce: {ACTIONNAME} Kategorie: {CATEGORY} Položka: {ITEM_ID} Od: {LOGGED_AT}</TD>
<TD align="right"> {search} {showall}</TD>
</TR>
</TABLE>