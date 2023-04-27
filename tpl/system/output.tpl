<?elements
string TITLE
string KEY noescape
string VALUE
string TOP_INFO
?>
<h2>{TITLE}</h2>

<input type="text" id="search" onkeyup="filterTable('infoTable')" placeholder="Hledat..">

<p>{TOP_INFO}</p>

<table class="grid strips" id="infoTable">
<tr><th>Key</th><th>Value</th></tr>
{block items}
  <tr><td>{KEY}</td><td>{VALUE}</td></tr>
{/block}
</table>