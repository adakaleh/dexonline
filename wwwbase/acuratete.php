<?php
require_once("../phplib/util.php");
util_assertModerator(PRIV_ADMIN);

$submitButton = util_getRequestParameter('submitButton');
$deleteButton = util_getRequestParameter('deleteButton');
$id = util_getRequestParameter('id');

$user = session_getUser();

if ($deleteButton) {
  $ap = AccuracyProject::get_by_id($id);
  if ($id) {
    $ap->delete();
  }
  FlashMessage::add('Am șters proiectul.', 'success');
  util_redirect('acuratete');
}

if ($id) {
  // open this project
  util_redirect("acuratete-eval?id={$id}");
}

$p = Model::factory('AccuracyProject')->create(); // new project
$p->ownerId = $user->id;

if ($submitButton) {
  $p->name = util_getRequestParameter('name');
  $p->userId = util_getRequestParameter('userId');
  $p->sourceId = util_getRequestParameter('sourceId');
  $p->startDate = util_getRequestParameter('startDate');
  $p->endDate = util_getRequestParameter('endDate');
  $p->method = util_getRequestParameter('method');

  if ($p->validate()) {
    $p->save();
    util_redirect("acuratete-eval?id={$p->id}");
  }
}

$aps = Model::factory('AccuracyProject')
  ->where('ownerId', $user->id)
  ->order_by_asc('name')
  ->find_many();

// build a map of project ID => project
$projects = [];
foreach ($aps as $ap) {
  $projects[$ap->id] = $ap;
}

SmartyWrap::assign('projects', $projects);
SmartyWrap::assign('p', $p);
SmartyWrap::assign('suggestNoBanner', true);
SmartyWrap::assign('suggestHiddenSearchForm', true);
SmartyWrap::addCss('select2');
SmartyWrap::addJs('select2', 'select2Dev');
SmartyWrap::display('acuratete.tpl');

?>