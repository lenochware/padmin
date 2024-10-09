<?elements
class form name "search" html_class "padmin"
select TRANSLATOR query "select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=1" default "1" noemptylb
select LANG query "select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=2" emptylb "source"
input TSTEXT
button search lb "Hledat"
button showall lb "Zrušit"
?>
<TABLE width="100%">
<TR>
<TD colspan="2">Překladač: {TRANSLATOR} Jazyk: {LANG} Text: {TSTEXT}</TD>
<TD align="right"> {search} {showall}</TD>
</TR>
</TABLE>