<?elements
class form name "pagerform"
select pglen list "20,20,100,100,200,200,500,500" noemptylb
string pages noescape
string first noescape
string last noescape
?>
{pglen} {first} {last} | {pages} | {total} záznamů
<script>
	document.getElementById('pglen').onchange = function() { this.form.submit() };
</script>
