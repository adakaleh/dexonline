<?php
require_once("../../phplib/util.php");
util_assertModerator(PRIV_WOTD);
util_assertNotMirror();

$IGNORED_DIRS = array('.', '..');
$EXTENSIONS = array('gif', 'jpeg', 'jpg', 'png');

if (array_key_exists('file', $_FILES)) {
  $rec = $_FILES['file'];
  if ($rec['error'] > 0) {
    FlashMessage::add('Eroare ' . $rec['error'] . ' la încărcarea fișierului.');
  } else if ($rec['size'] > 100000000) {
    FlashMessage::add('Dimensiunea maximă admisă este de 100 MB.');
  } else if ($rec['type'] != 'application/zip') {
    FlashMessage::add('Sunt permise numai fișiere .zip.');
  } else {
    // Create a working dir
    $workDir = tempnam(Config::get('global.tempDir'), 'compress_');
    @unlink($workDir);
    mkdir($workDir);
    chdir($workDir);

    // Move the file there and decompress it
    move_uploaded_file($rec['tmp_name'], "{$workDir}/uncompressed.zip");
    OS::executeAndAssert("unzip {$workDir}/uncompressed.zip");
    @unlink("{$workDir}/uncompressed.zip");

    // Compress all the images inside it
    $logFile = fopen("{$workDir}/raport.txt", 'a');
    $beforeBytes = 0;
    $afterBytes = 0;
    recursiveScan($workDir, $logFile);
    $compression = 100.0 * (1 - $afterBytes/$beforeBytes);
    fprintf($logFile, "\nTotal: %d/%d bytes, %.2f%% saved\n", $afterBytes, $beforeBytes, $compression);
    fclose($logFile);

    // Compress the directory
    OS::executeAndAssert("zip -r0 compressed.zip *");

    // Move the resulting file to another temp location so it lives a little longer
    $outputFile = tempnam(Config::get('global.tempDir'), 'compress_');
    @unlink($outputFile);
    rename("{$workDir}/compressed.zip", $outputFile);
    OS::executeAndAssert("rm -rf {$workDir}");

    header('Content-disposition: attachment; filename=comprimate.zip');
    header('Content-Type: application/zip');
    readfile($outputFile);
    exit;
  }
}

SmartyWrap::displayAdminPage('admin/wotdCompressImages.tpl');

/**************************************************************************/

function recursiveScan($path, $logFile) {
  global $IGNORED_DIRS, $EXTENSIONS, $beforeBytes, $afterBytes;
  $files = scandir($path);
  
  foreach ($files as $file) {
    if (in_array($file, $IGNORED_DIRS)) {
      continue;
    }
    $full = "$path/$file";

    if (is_dir($full)) {
      recursiveScan($full, $logFile);
    } else {
      $extension = pathinfo(strtolower($full), PATHINFO_EXTENSION);
      $fullNoExt = substr($full, 0, strlen($full) - strlen($extension) - 1); // Strip the dot as well
      if (in_array($extension, $EXTENSIONS)) {
        OS::executeAndAssert("convert -strip '{$full}' '" . Config::get('global.tempDir') . "/fileNoExif.{$extension}'");
        OS::executeAndAssert("convert '" . Config::get('global.tempDir') . "/fileNoExif.{$extension}' '/fileNoExifPng.png'");
        OS::executeAndAssert("optipng '" . Config::get('global.tempDir') . "/fileNoExifPng.png'");
        $fs1 = filesize($full);
        $fs2 = filesize(Config::get('global.tempDir') . "/fileNoExif.{$extension}");
        $fs3 = filesize(Config::get('global.tempDir') . '/fileNoExifPng.png');
        $beforeBytes += $fs1;
        if ($fs3 < $fs1 && $fs3 < $fs2) {
          $compression = 100.0 * (1 - $fs3/$fs1);
          $afterBytes += $fs3;
          fprintf($logFile, "%s -- Strip EXIF, convert to PNG and optimize: %d/%d bytes, %.2f%% saved\n", $full, $fs3, $fs1, $compression);
          unlink($full);
          unlink(Config::get('global.tempDir') . "/fileNoExif.{$extension}");
          rename(Config::get('global.tempDir') . '/fileNoExifPng.png', "$fullNoExt.png");
        } else if ($fs2 < $fs1) {
          $compression = 100.0 * (1 - $fs2/$fs1);
          $afterBytes += $fs2;
          fprintf($logFile, "%s -- Strip EXIF: %d/%d bytes, %.2f%% saved\n", $full, $fs2, $fs1, $compression);
          unlink($full);
          rename(Config::get('global.tempDir') . "/fileNoExif.{$extension}", $full);
          unlink(Config::get('global.tempDir') . '/fileNoExifPng.png');
        } else {
          $afterBytes += $fs1;
          fprintf($logFile, "{$full} -- leave unchanged\n");
          unlink(Config::get('global.tempDir') . "/fileNoExif.{$extension}");
          unlink(Config::get('global.tempDir') . '/fileNoExifPng.png');
        }
      }
    }
  }
}


?>
