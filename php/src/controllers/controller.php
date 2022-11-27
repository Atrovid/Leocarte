<?php


require_once('src/models/model.php');


$db = (new Model());


function configDatabase() {
    $db = (new Model());
	$db->initStudentsDB();
	$db->initClassesDB();
	$db->initRoomsDB();
	$db->initLecturersDB();
	$db->initSubjectsDB();
	$db->initAttendancesDB();
	//$db->dropTable('attendances');

	require('src/views/viewConfig.php');
}

function error404(){
	require('src/views/viewError404.php');
}

function attendance() {
	require('src/views/viewAttendance.php');
}







?>