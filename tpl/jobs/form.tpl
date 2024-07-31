<?elements
class form route "jobs/{GET}" html_class "padmin" html5
input name lb "Název" required
text annotation lb "Popis" html_class "w-100"
input job_command lb "Příkaz" required html_class "w-100"
input job_params lb "Parametry" html_class "w-100"
input first_run_at lb "První spuštění" html_class "calendar" html_data-format "d. m. Y H:i" date "d.m.Y H:i" required html5 "0"
select period lb "Perioda spuštění" lookup "job-period" default "86400" noemptylb
input last_run_at lb "Poslední spuštění" date "d.m.Y H:i" noedit html5 "0"
text last_run_result lb "Výsledek" noedit
check active lb "Aktivní" default "1"

button insert lb "Přidat" noprint
button update lb "Uložit" noprint
button delete lb "Smazat" noprint confirm "Opravdu smazat?"
button runJob lb "Spustit »" noprint confirm "Opravdu spustit?"
button back lb "Zpět" onclick "history.back()"
?>
<style>
    #runJob {
        color:green;
    }
</style>
<TABLE>
<tr><td><h1>Vytvoření úlohy</h1></td></tr>
{form.fields}
</TABLE>

<br>Pole označená (*) jsou povinná.

<script language="JavaScript">
  $(document).ready(init_global);
</script>