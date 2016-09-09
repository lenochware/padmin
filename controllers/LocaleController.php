<?php
include_once 'BaseController.php';

class LocaleController extends BaseController {

function indexAction() {
  return $this->textsAction();
}

function textsAction() {
  $this->title(1, "Texty");
  $grid = new PCGrid('tpl/texts.tpl');  

  $grid->setquery(
    "select A.ID TRANSLATOR,B.ID LANG FROM 
    (SELECT ID FROM TRANSLATOR_LABELS WHERE CATEGORY=1) A,
    (SELECT 0 ID UNION SELECT ID FROM TRANSLATOR_LABELS WHERE CATEGORY=2) B
     ORDER BY A.ID,B.ID"
  );
  return $grid;
}

function addAction() {
  $this->title(2, 'Formulář jazyka');
  $form = new PCForm('tpl/langform.tpl');
  $form->_TRANSLATOR->noedit = 0;
  $form->_LANG->noedit = 0;
  $form->enable('insert');

  return $form;  
}

function insertAction() {
  $form = new PCForm('tpl/langform.tpl');
  $translator = new PCTranslator($form->values['TRANSLATOR']);
  $lang = $translator->createLanguage($form->values['LANG']);
  $tr = $this->getLabelId($form->values['TRANSLATOR'], 1);
  $n = $this->savetexts($tr, $lang, $form->values['TEXTS']);
  $this->app->message("Položka byla přidána.");
  $this->reload();
}

function editAction($tr, $lang) {
  $this->title(2, 'Formulář jazyka');
  $form = $this->getform($tr, $lang);
  $form->enable('update', 'delete');
  return $form;  
}

function updateAction($tr, $lang) {
  $form = new PCForm('tpl/langform.tpl');
  $n = $this->savetexts($tr, $lang, $form->values['TEXTS']);
  $this->app->message("Uloženo $n záznamů.");
  $this->redirect("locale/edit/tr:$tr/lang:$lang");
}

function deleteAction($tr, $lang) {
  $form = new PCForm('tpl/langform.tpl');
  $translator = new PCTranslator($form->values['TRANSLATOR']);
  $translator->deleteLanguage($form->values['LANG']);
  $this->app->message('Položka byla smazána.');
  $this->reload();
}

protected function getform($tr = null, $lang = null) {
  $form = new PCForm('tpl/langform.tpl');
  $form->_TRANSLATOR = $this->db->field('TRANSLATOR_LABELS:LABEL', pri($tr));
  $form->_LANG =  $this->db->field('TRANSLATOR_LABELS:LABEL', pri($lang));
  $form->_TEXTS = $this->gettextsxml($tr, $lang);
  return $form;
}

protected function gettextsxml($tr, $lang, $all = true) {

  $texts = $this->gettexts($tr, $lang);
  
  $s = '';
  foreach ($texts as $id => $text) {
    if (!$text[1]) {
      if (!$all) continue; else $text[1] = $text[0];
    }
    $s .= "<text id=\"$id\">".$text[1]."</text>\n";
  }
  return "<texts>\n".$s.'</texts>';
}

protected function gettexts($tr, $lang) {
  $texts = $this->db->select_pair(
    "select T0.ID,T0.TSTEXT,T.TSTEXT,T.ID FROM TRANSLATOR T0
    LEFT JOIN TRANSLATOR T ON T.TEXT_ID=T0.ID AND T.LANG='{1}'
    WHERE T0.TRANSLATOR='{0}' AND T0.LANG=0 order by T.ID,T0.ID",
    $tr, $lang
  );
  return $texts;
}

/**
 * Update table TRANSLATOR with texts stored in XML.
 * - You can add new source text with syntax <text>New source text</text> (without id)
 * - You can delete text as follows: <text id="11"></text> (empty tag will delete text #11)
 * - TODO: pages?
 */
protected function savetexts($tr, $lang, $xml) {
  $stored = $this->gettexts($tr, $lang);

  $data = array(
    'TRANSLATOR' => $tr,
    'LANG' => $lang,
    'DT' => now(),
  );

  $n = 0;
  foreach ($this->xml2array($xml) as $text) {
    $text_id = (int)$text[1];

    $data['TEXT_ID'] = $text_id;
    $data['TSTEXT'] = $text[2];

    //adding source text
    if (!$text_id and $lang == 0) {
      $data['PAGE'] = $this->getLabelId('default', 3);
      $text_id = $this->db->insert('TRANSLATOR', $data);
      $this->db->update('TRANSLATOR', "TEXT_ID='$text_id'", pri($text_id));
      $n++;
      continue;
    }

    $stored_text = $stored[$text_id];
    if (!$stored_text) {
      $this->app->message("Text #%d nenalezen.", 'warning', $text_id);
      continue;
    }    

    //do not rewrite the same
    if ($stored_text[1] == $text[2] or (!$stored_text[1] and $stored_text[0] == $text[2])) {
      continue;
    }

    $id = $stored_text[2];
    if ($data['TSTEXT'] === '' and $id) {
      //check the dependencies
      if ($lang == 0 and $this->db->exists('TRANSLATOR', "TEXT_ID='{0}' AND LANG<>0", $id)) {
        $this->app->message("Text #%d se používá - nelze smazat.", 'warning', $id);
        continue;
      }
      $this->db->delete('TRANSLATOR', pri($id)); 
    }
    elseif ($id) {
      $this->db->update('TRANSLATOR', $data, pri($id)); 
    }
    else {
      $data['PAGE'] = null;
      $this->db->insert('TRANSLATOR', $data);      
    }

    $n++;
  }
  return $n;
}

protected function xml2array($s) {
  $TEXTTAG_PATTERN = "/<text\s+id\s*=\s*\"(\d+)\"\s*>(.*?)<\/text>/si";
  $TEXTTAG_PATTERN_ADD = "/<text>()(.+?)<\/text>/si"; 
  preg_match_all($TEXTTAG_PATTERN, $s, $texts, PREG_SET_ORDER);
  preg_match_all($TEXTTAG_PATTERN_ADD, $s, $texts2, PREG_SET_ORDER);
  return array_merge($texts, $texts2);
}

protected function getLabelId($label, $category) {
  if (!$label) return -1;
  if ($label == 'source' and $category == 2) return 0;
  $id = $this->db->field('TRANSLATOR_LABELS:ID',
    "LABEL='{0}' AND CATEGORY='{1}'", $label, $category
  );
  if (!$id) {
    $label = array('LABEL'=>$label,'CATEGORY'=>$category,'DT'=>date('Y-m-d H:i:s'));
    $id = $this->db->insert($this->LABELS_TAB, $label);
  }
  return $id;
}

}

?>