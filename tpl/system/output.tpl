<?elements
string TITLE
string KEY noescape
string VALUE
string TOP_INFO
?>
<h2>{TITLE}</h2>

<input type="text" id="search" onkeyup="filterTable()" placeholder="Hledat..">

<p>{TOP_INFO}</p>

<table class="grid strips" id="infoTable">
<tr><th>Key</th><th>Value</th></tr>
{block items}
  <tr><td>{KEY}</td><td>{VALUE}</td></tr>
{/block}
</table>

<script>
	function filterTable()
	{
	  let input = document.getElementById("search");
	  let filter = input.value.toUpperCase();
	  let table = document.getElementById("infoTable");
	  let tr = table.getElementsByTagName("tr");

	  $('#infoTable').toggleClass('strips', filter.length == 0);

	  for (let i = 1; i < tr.length; i++) {
      let txtValue = tr[i].textContent;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
	  }
	}	
</script>
