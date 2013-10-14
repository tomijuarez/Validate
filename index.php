<?php
require_once 'Validation.php';

$validation = new Validation();

$validation->setFlags(array( 'page' => 'int', 'user' => 'nick', 'tab' => 'alpha'));
$passed = $validation->validate($_GET);

if ( !$passed ) {
	print '<pre>';
	var_dump($validation->getMessages());
	print '</pre>';
}