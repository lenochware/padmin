<?elements
class form route "users/{GET}" html_class "padmin" html5
input USERNAME required lb "Uživ. jméno:"
input PASSWORD lb "Heslo:" required html_autocomplete "new-password"
input FULLNAME  size "50" lb "Celé jméno:"
input EMAIL size "40" lb "Email:" email
string ID
string RINDIV noescape
select ROLE1 required lb "Role:"
select ROLE2
select ROLE3
select ROLE4
select ROLE5
text ANNOT lb "Anotace:" maxlength "255" size "60x2"
check ACTIVE lb "Aktivní" default "1"
string LAST_LOGIN lb "Posl. přihlášení" sort date "d.m.Y H:i"
string DT lb "Dat.vytvoření" sort date
string AUTHOR
link lnrights lb "Práva a proměnné" route "rights/user/id:{ID}"
button insert lb "Přidat" noprint
button copy lb "Kopírovat" noprint confirm "Kopírovat uživatele?"
button update lb "Uložit" noprint
button delete lb "Smazat" noprint confirm "Opravdu smazat?" html_formnovalidate
button impersonate lb "Přihlásit se" confirm "Přihlásit se jako tento uživatel?" noprint
button back lb "Zpět" onclick "history.back()"
?>
<table>
<tr>
  <td colspan="2">
    <h1>Uživatel {FULLNAME.value}</h1>
  </td>
</tr>
<tr><td>{USERNAME.lb}</td><td>{USERNAME}</td></tr>
<tr>
  <td>{PASSWORD.lb}</td><td>{PASSWORD} 
    <a href="#" onclick="passwordGenerate()">Vygenerovat heslo</a>
    | <a href="#" onclick="passwordToClipboard()">Kopírovat</a>
  </td>
</tr>
<tr><td>{FULLNAME.lb}</td><td>{FULLNAME}</td></tr>
<tr>
  <td>{EMAIL.lb}</td>
  <td>{EMAIL} {if EMAIL}<a href="mailto:{EMAIL.value}">Otevřít</a>{/if}</td>
</tr>
<tr><td>{ROLE1.lb}</td>
<td>
&nbsp;{ROLE1} <a  href="#" onclick="$('#roleplus').toggle()">další...</a>
  <div id="roleplus" style="display:none">
  <table style="border-width:0px;">
  <tr><td>{ROLE2} {ROLE3}</td></tr>
  <tr><td>{ROLE4} {ROLE5}</td></tr>
  </table>
  </div>
</td></tr>
<tr><td>{ANNOT.lb}</td><td>{ANNOT}</td></tr>
<tr><td>{ACTIVE.lb}</td><td>{ACTIVE}</td></tr>
<tr><td colspan="2">
Individiální práva:<br>
<div style="font-weight:bold; color:red">{RINDIV}</div><br>
&nbsp;{lnrights}<br><br>
&nbsp;{insert}{update} {copy} {delete} {impersonate} {back}<br><br>
</td></tr>

<tr><td colspan="2" style="color:gray">
  Vytvořeno: {DT} {AUTHOR}, Poslední přihlášení: {LAST_LOGIN}
</td></tr>
</table>

<br>Pole označená (*) jsou povinná.

<script language="JavaScript">

function passwordGenerate()
{
  document.getElementById('PASSWORD').type = 'text';
  $.get('?r=users/genPassword', function(data) {
    $('#PASSWORD').val(data);
  });
}

function passwordToClipboard()
{
  let input = document.getElementById("PASSWORD");

  input.select();
  input.setSelectionRange(0, 99999);

   // Copy the text inside the text field
  navigator.clipboard.writeText(input.value);
} 

function init()
{
  document.getElementById('PASSWORD').type = 'password';
}

$(document).ready(init);
</script>

