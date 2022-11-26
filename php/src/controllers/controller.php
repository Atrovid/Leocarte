<?php


require_once('src/models/model.php');


$db = (new Model());


function createDatabase() {
    $db = (new Model());
	$db->initLecturersDB();
	$db->initStudentsDB();
	$db->initRoomsDB();
	$db->initSubjectsDB();
	$db->initClassesDB();
	$db->initAttendancesDB();

	require('src/views/viewConfig.php');
}

function dropTable(){
	$db = (new Model());
	$db->dropTable('attendances');
	$db->dropTable('classes');
	$db->dropTable('subjects');
	$db->dropTable('rooms');
	$db->dropTable('lecturers');
	$db->dropTable('students');
	
	require('src/views/viewDelete.php');
}

function addInfoIntoDatabase(){
	$db = (new Model());
	$db->addIntoStudentsDB(2,"Alix","Baptiste",2); 
	$db->addIntoLecturersDB(1,"Martin","Arnaud"); //lecturer_id = 1
	$db->addIntoRoomsDB(103,"A103"); //room_id = 103
	$db->addIntoSubjectsDB(1,"Electronique"); //subject_id = 1
	$db->addIntoClassesDB(1,1,103,1,"8:15","10:30"); // class_id, lecturer_id, room_id, subject_id, start_hour, end_hour
	
	require('src/views/viewAddInfo.php');
}

function deleteInfoIntoDatabase(){
	$db = (new Model());
	//$db->deleteIntoStudentDB(2);
	require('src/views/viewDelete.php');
}

function displayDatabase(){
	$db = (new Model());
	$db->displayStudentDB();
	require('src/views/viewInfo.php');
}

function error404(){
	require('src/views/viewError404.php');
}










?>