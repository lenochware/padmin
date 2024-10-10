<?php
include_once 'BaseController.php';

class LocaleController extends BaseController {

protected $transId;

function init()
{
  parent::init();
  $search = $this->getSearch();
  $this->transId = (int)$search->values['TRANSLATOR'];
}

function indexAction()
{
  $this->title(1, "Texty");

  $grid = new PCGrid('tpl/locale/texts.tpl', 'locale-grid');
  $search = $this->getSearch();

  $grid->setquery(
    "select T.ID,T.TEXT_ID,T2.LANG,IFNULL(T2.TSTEXT,CONCAT('TRANSLATE: ',T.TSTEXT)) AS TSTEXT2 FROM TRANSLATOR T
    LEFT JOIN TRANSLATOR T2 ON T2.TEXT_ID=T.TEXT_ID AND T2.LANG='{LANG}'
    WHERE 1=1
    AND T.TRANSLATOR='$this->transId'
    AND T.LANG=0
    ~ and T2.TSTEXT like '%{TSTEXT}%'
    ORDER BY T.TRANSLATOR,IFNULL(T2.TSTEXT,T.TSTEXT)"
  );

  if (isset($_GET['export'])) {
    $grid->exportExcel('texty.csv');
  }

  return $search.$grid;
}

function searchAction() {
  $this->setFilter($_POST['data']);
  $this->reload();
}

function showallAction() {
  $this->setFilter(null);
  $this->reload();
}

function setFilter($filter)
{
  $this->app->setsession('locale-grid.filter', $filter);
  $this->app->setsession('locale-search.values', $filter);
}

function getSearch()
{
  $search = new PCForm('tpl/locale/search.tpl', 'locale-search');

  if (empty($search->values['TRANSLATOR'])) $search->values['TRANSLATOR'] = 1;
  if (empty($search->values['LANG'])) $search->values['LANG'] = 0;

  return $search;
}

/** ajax call */
function editAction($textId)
{  
  $form = new PCForm('tpl/locale/form.tpl');
  $data = [];

  if ($textId) {
    $data = $this->db->selectAll('TRANSLATOR', "TEXT_ID='{0}'", $textId);
    $data = array_assoc($data, 'LANG');    
  }

  foreach ($this->getLanguages() as $langId => $lang)
  {
    $s = array_get($data, [$langId, 'TSTEXT'], '');
    if (strpos($s, "\n")) {
      $form->addTag("text text_$langId lb \"Text $lang\" html_class=\"locale-input\"");
    }
    else {
      $form->addTag("input text_$langId lb \"Text $lang\" html_class=\"locale-input\" html_ondblclick=\"dblclick(this)\"");
    }

    $form->values["text_$langId"] = $s;
  }

  $form->values["textId"] = $textId;
  $form->enable($data? ['update', 'delete']:'insert');

  die($form);
}

function updateAction($textId)
{
  $post = $_POST['data'];
  foreach ($this->getLanguages() as $langId => $lang)
  {
    $text = $post['text_'.$langId];
    if (!$text) continue;

    $data = [
      'TRANSLATOR' => $this->transId,
      'LANG' => $langId,
      'TEXT_ID' => $textId,
      'TSTEXT' => $text,
      'DT' => now(),
    ];

    $this->db->insertUpdate('TRANSLATOR', $data, ['TRANSLATOR', 'LANG', 'TEXT_ID']);
  }

  if (!$textId) {
    $textId = $this->db->field('TRANSLATOR:ID', "TEXT_ID=0 and LANG=0");
    $this->db->update('TRANSLATOR', ['TEXT_ID' => $textId], "TEXT_ID=0");
  }

  $this->outputJson(['message' => 'Položka byla aktualizována.']);
}

function deleteAction($textId)
{
  $this->db->delete('TRANSLATOR', "TEXT_ID='{#0}'", $textId);
  $this->app->message('Položka byla smazána.');
  $this->reload();
}

function importAction()
{
  $search = $this->getSearch();

  $form = new PCForm('tpl/locale/import.tpl');
  $form->elements['LANG']['items'] = $this->getLanguages();
  $form->elements['LANG']['default'] = $search->values['LANG'];

  if ($form->submitted) {
    $csv = new CsvFile();
    $csv->fromString($form->getFile('FILE'));
    $cols = $csv->getColumns();

    if (!in_array('Text', $cols) or !in_array('Id', $cols)) {
      $this->app->error('Chybí povinné sloupce!');
    }

    $count = $this->importTexts($form->values['LANG'], $csv->toArray());

    $this->app->message("Import dokončen. $count textů importováno.");
  }

  return $form;
}

function languagesAction()
{
  if (isset($_GET['add'])) {
    $this->db->insertUpdate('TRANSLATOR_LABELS', ['CATEGORY' => 2, 'LABEL' => $_GET['add'], 'DT' => now()], ['LABEL', 'CATEGORY']);
    $this->redirect('locale/languages');
  }

  $grid = new PCGrid('tpl/locale/languages.tpl');
  $grid->setQuery("select ID,LABEL from TRANSLATOR_LABELS where CATEGORY=2 order by ID");
  return $grid;
}

function languageDeleteAction($id)
{
  $this->db->delete('TRANSLATOR_LABELS', ['ID' => $id]);
  $this->db->delete('TRANSLATOR', ['TRANSLATOR' => $this->transId, 'LANG' => $id]);
  $this->app->message('Položka byla smazána.');
  $this->redirect('locale/languages');
}


protected function getLanguages()
{
  $items = $this->db->selectPair('TRANSLATOR_LABELS:ID,LABEL', 'CATEGORY=2 order by ID');
  return [0 => 'source'] + $items;
}

protected function importTexts($langId, $texts)
{
  $source = $this->db->selectOne('TRANSLATOR:TEXT_ID', ['TRANSLATOR' => $this->transId,  'LANG' => 0]);

  foreach ($texts as $i => $text) {
    $data = [
      'TRANSLATOR' => $this->transId,
      'LANG' => $langId,
      'TEXT_ID' => $text['Id'],
      'TSTEXT' => $text['Text'],
      /*'PAGE' => $text['Page'],*/
      'DT' => now(),
    ];

    $this->db->insertUpdate('TRANSLATOR', $data, ['TRANSLATOR', 'LANG', 'TEXT_ID']);

    if ($langId > 0 and !in_array($text['Id'], $source)) {
      $data['LANG'] = 0;
      $data['TSTEXT'] = 'SOURCE: ' . $data['TSTEXT'];
      $this->db->insert('TRANSLATOR', $data);
    }
  }

  return count($texts);
}

function countAction($id)
{
  print $this->db->field("TRANSLATOR:count(*)", ['LANG' => $id]);
}


}

?>