<?php
require_once("../phplib/Core.php");
User::mustHave(User::PRIV_EDIT);

$DEF_LIMIT = 20;
$LEXEM_LIMIT = 100;
$MEANING_LIMIT = 50;

$id = Request::get('id');
$saveButton = Request::has('saveButton');

$tag = Tag::get_by_id($id);

if ($saveButton) {
  $tag->value = Request::get('value');
  $tag->parentId = Request::get('parentId');
  $tag->setColor(Request::get('color'));
  $tag->setBackground(Request::get('background'));

  $errors = $tag->validate();
  if ($errors) {
    SmartyWrap::assign('errors', $errors);
  } else {
    $tag->save();

    FlashMessage::add('Am salvat eticheta.', 'success');
    Util::redirect("?id={$tag->id}");
  }
}

$frequentColors = [
  'color' => Tag::getFrequentValues('color', Tag::DEFAULT_COLOR),
  'background' => Tag::getFrequentValues('background', Tag::DEFAULT_BACKGROUND),
];

$children = Model::factory('Tag')
          ->where('parentId', $tag->id)
          ->order_by_asc('value')
          ->find_many();

$homonyms = Model::factory('Tag')
          ->where('value', $tag->value)
          ->where_not_equal('id', $tag->id)
          ->find_many();

$defCount = Model::factory('ObjectTag')
          ->where('objectType', ObjectTag::TYPE_DEFINITION)
          ->where('tagId', $tag->id)
          ->count();
$defs = Model::factory('Definition')
      ->table_alias('d')
      ->select('d.*')
      ->join('ObjectTag', ['ot.objectId', '=', 'd.id'], 'ot')
      ->where('ot.objectType', ObjectTag::TYPE_DEFINITION)
      ->where('ot.tagId', $tag->id)
      ->limit($DEF_LIMIT)
      ->find_many();
$searchResults = SearchResult::mapDefinitionArray($defs);

$lexemCount = Model::factory('ObjectTag')
            ->where('objectType', ObjectTag::TYPE_LEXEM)
            ->where('tagId', $tag->id)
            ->count();
$lexems = Model::factory('Lexem')
        ->table_alias('l')
        ->select('l.*')
        ->join('ObjectTag', ['ot.objectId', '=', 'l.id'], 'ot')
        ->where('ot.objectType', ObjectTag::TYPE_LEXEM)
        ->where('ot.tagId', $tag->id)
        ->limit($LEXEM_LIMIT)
        ->find_many();

$meaningCount = Model::factory('ObjectTag')
            ->where('objectType', ObjectTag::TYPE_MEANING)
            ->where('tagId', $tag->id)
            ->count();
$meanings = Model::factory('Meaning')
          ->table_alias('m')
          ->select('m.*')
          ->join('ObjectTag', ['ot.objectId', '=', 'm.id'], 'ot')
          ->where('ot.objectType', ObjectTag::TYPE_MEANING)
          ->where('ot.tagId', $tag->id)
          ->limit($MEANING_LIMIT)
          ->find_many();

SmartyWrap::assign('t', $tag);
SmartyWrap::assign('children', $children);
SmartyWrap::assign('homonyms', $homonyms);
SmartyWrap::assign('defCount', $defCount);
SmartyWrap::assign('searchResults', $searchResults);
SmartyWrap::assign('lexemCount', $lexemCount);
SmartyWrap::assign('lexems', $lexems);
SmartyWrap::assign('meaningCount', $meaningCount);
SmartyWrap::assign('meanings', $meanings);
SmartyWrap::assign('frequentColors', $frequentColors);
SmartyWrap::addCss('admin', 'colorpicker');
SmartyWrap::addJs('select2Dev', 'colorpicker');
SmartyWrap::display('eticheta.tpl');

?>
