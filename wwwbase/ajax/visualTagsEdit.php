<?php
require_once("../../phplib/util.php");

$oper = util_getRequestParameter('oper');
$id = util_getRequestParameter('id');

switch ($oper) {
  case 'add':
    $line = VisualTag::factory('VisualTag')->create();
    
    $line->imageId = util_getRequestParameter('imageId');
    $line->lexemeId = util_getRequestParameter('lexemeId');
    $line->label = util_getRequestParameter('label');
    $line->textXCoord = util_getRequestParameter('xTag');
    $line->textYCoord = util_getRequestParameter('yTag');;
    $line->imgXCoord = util_getRequestParameter('xImg');
    $line->imgYCoord = util_getRequestParameter('yImg');
    $line->save();
    Log::info("Added visual tag {$line->id} ({$line->label}) for image {$line->imageId}");
    break;

  case 'del':
    $line = VisualTag::get_by_id($id);
    
    if(!empty($line)){
      Log::notice("Deleted visual tag {$line->id} ({$line->label}) for image {$line->imageId}");
      $line->delete();
    }   
    break;
  
  case 'edit':
    $line = VisualTag::get_by_id($id);
  	
    if(!empty($line)){
      $line->textXCoord = util_getRequestParameter('textX');
      $line->textYCoord = util_getRequestParameter('textY');;
      $line->imgXCoord = util_getRequestParameter('imgX');
      $line->imgYCoord = util_getRequestParameter('imgY');
      $line->label = util_getRequestParameter('label');
      $line->save();
      Log::notice("Edited visual tag {$line->id} ({$line->label}) for image {$line->imageId}");
  	}
    break;

  default:
    break;
}
?>
