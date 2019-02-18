<?php
require_once '../../lib/Core.php';
User::mustHave(User::PRIV_EDIT);

$lexemes = Lexeme::loadAmbiguous();

Smart::assign('lexemes', $lexemes);
Smart::addResources('admin');
Smart::display('admin/viewAmbiguousLexemes.tpl');