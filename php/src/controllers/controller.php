<?php


require_once('src/models/model.php');


$db = (new Model());


function configDatabase() {
    $db = (new Model());
	$db->initDatabase();

	require('src/views/viewConfig.php');
}

function error404(){
	require('src/views/viewError404.php');
}









?>