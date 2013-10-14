<?php
require_once 'Validation.php';

$validation = new Validation();

$validation->setFlags(array( 'title' => 'alpha', 'category' => 'alpha'));
$passed = $validation->validate($_POST);

if ( !$passed ) {

	print '<pre>';
	var_dump($validation->getMessages());
	print '</pre>';
}
else {
	$validation->setFlags(array('tmp_name' => 'path', 'size' => 'int', 'name' => 'path' ));

	$_files = array(
			"tmp_name" => $_FILES ['file']['tmp_name']
		, "size"     => $_FILES ['file']['size']
		, "name"     => $_FILES ['file']['name']
	);


	$filesPassed = $validation->validate($_files, 'image');

	if ( !$filesPassed ) {
		print '<pre>';
		var_dump($validation->getMessages());
		print '</pre>';
	}
	else {
		print 'GOOD :)';
	}
}