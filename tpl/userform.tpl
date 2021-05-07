<?elements
class form route "users/{GET}" html_class "padmin" html5
input USERNAME required lb "Uživ. jméno:"
input PASSWORD lb "Heslo:" required
input DPASSW hidden
check HASDPASSW lb "Implicitní heslo" default "1"
input FULLNAME  size "50" lb "Celé jméno:"
input EMAIL size "40" lb "Email:" email
string ID
string RINDIV noescape
select ROLE1 required lb "Role:"
select ROLE2
select ROLE3
select ROLE4
select ROLE5
input ANNOT size "50" lb "Anotace:"
check ACTIVE lb "Aktivní" default "1"
link lnrights lb "Práva a proměnné" route "rlookups/rlist/id:{ID}/lookup:right"
button insert lb "Přidat" noprint
button update lb "Uložit" noprint
button delete lb "Smazat" noprint
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
<tr><td>{PASSWORD.lb}</td><td>{PASSWORD} {HASDPASSW}{HASDPASSW.lb}
  <br><a href="#" onclick="dpassw_create()">nové</a></td></tr>
<tr><td>{FULLNAME.lb}</td><td>{FULLNAME}</td></tr>
<tr>
  <td>{EMAIL.lb}</td>
  <td>{EMAIL} {if EMAIL}<a href="mailto:{EMAIL.value}" style="text-decoration: none">@</a>{/if}</td>
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
&nbsp;{insert}{update} {delete} {impersonate} {back}<br><br>
</td></tr>
</table>

<br>Pole označená (*) jsou povinná.

<script language="JavaScript">
//set confirm dialog on delete button
function deleteprompt() {
  if(!confirm('Opravdu smazat?')) return false;
}
function dpassw_toggle() {
  if ($(this).is(':checked')) {
    $('#PASSWORD').val($('#DPASSW').val());
    document.getElementById('PASSWORD').type = 'text';
  }
  else {
    $('#PASSWORD').val('{PASSWORD.value}');
    document.getElementById('PASSWORD').type = 'password';
  }
}

function dpassw_change() {
  if ($('#HASDPASSW').is(':checked')) {
    $('#DPASSW').val($('#PASSWORD').val());
  }
}

function dpassw_create()
{
  $.get('?r=users/genPassword', function(data) {
    $('#PASSWORD').val(data);
    $('#DPASSW').val(data);
  });
}

function init() {
  $('#delete').click(deleteprompt);
  $('#HASDPASSW').change(dpassw_toggle);
  $('#HASDPASSW').trigger('change');
  $('#PASSWORD').change(dpassw_change);
}

$(document).ready(init);
</script>

