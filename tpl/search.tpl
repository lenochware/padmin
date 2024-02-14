<?elements
class form  html_class "padmin" default_print "div"
string FOUND noescape skip
button search lb "Hledat" default
button showall lb "Ukaž všechny" default
?>
<div class="form searchform">{form.fields}</div>
<!-- {FOUND} -->
<style>
	.searchform div {display: inline-block;}
	.searchform div.buttons {display: block;padding-top: 5px;}
	.searchform div label {padding: 5px;}

.searchform select { max-width: 300px; }
</style>