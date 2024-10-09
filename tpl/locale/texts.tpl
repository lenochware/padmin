<?elements
class grid singlepage
string TEXT_ID lb "Id" sort
string TSTEXT2 lb "Text" sort
bind LANG query "select id,label from TRANSLATOR_LABELS where CATEGORY=2" skip
link lnexport route "locale/export:1" lb "Export" skip
pager pager pglen "20" nohide
?>
<style> 
.locale-input {
  width: 100%;
}
</style>
<h2>Texty</h2>
<table class="grid">
  <tr>
  	<th>{TEXT_ID.lb}</th>
  	<th>{TSTEXT2.lb}</th>
  	<th></th>
  </tr>
{block items}
  <tr class="link" onclick="editText({TEXT_ID})">
  	<td width="50">{TEXT_ID}</td>
  	<td><div id="t{TEXT_ID}" class="text-long">{TSTEXT2}</div></td>
  	<td align="right" style="color:#999">{LANG}</td>
  </tr>
{block else}
  <tr><td colspan=9>Nenalezeny žádné položky.</td></tr>
{/block}

<tr><td colspan=9>
  <span id="inserted"></span><a href="javascript:void(0)" onclick="editText(0)">Přidat</a>
</td></tr>
</table>

<div id="form"></div>

<div class="pager">{pager} | {pager.total} záznamů | {lnexport}</div>

<p class="text-muted">Pro automatické naplnění databáze textů nastavte v index.php <code>$app->language="source";</code></p>

<script>
	function editText(id)
	{
		$('#form').load('?r=locale/edit&textId=' + id);
	}

  function updateText(id)
  {
    if (!$('#text_0').val()) {
      alert("Pole 'Text source' je povinné!");
      return;
    }

    $.post('?r=locale/update&textId='+id,  $('.locale-form').serialize(), function(data) {
      $("#t" + id).html($("#text_" + Number($("#LANG").val())).val());
      if (!id) $('#inserted').html("* ");
      $(".result").html(data.message);
    });
  }

  function dblclick(input)
  {
    let textbox = $(document.createElement('textarea'))
      .attr('id', input.id)
      .attr('name', input.name)
      .attr('cols', 60)
      .val(input.value);

    $(input).replaceWith(textbox);
  }
</script>