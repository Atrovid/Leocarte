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
	$db->addStudent(2,"Alix","Baptiste",2); //student_id=2
	$db->addLecturer(1,"Martin","Arnaud"); //lecturer_id = 1
	$db->addRoom(103,"A103"); //room_id = 103
	$db->addSubject(1,"Electronique"); //subject_id = 1
	$db->addClass(1,1,103,1,"8:15","10:30"); // class_id, lecturer_id, room_id, subject_id, start_hour, end_hour
	$db->addAttendance(1,2,FALSE); //class_id, student_id
	require('src/views/viewAddInfo.php');
}

function deleteInfoIntoDatabase(){
	$db = (new Model());
	//$db->deleteIntoStudentDB(2);
	require('src/views/viewDelete.php');
}

function displayDatabase(){
	$db = (new Model());
	$db->displayStudent();
	require('src/views/viewInfo.php');
}

function checkStudentInClass(){
	$db = (new Model());
	$db->checkStudentInClass(2,"A103","9:00")[0];
	require('src/views/viewSearchStudentInClass.php');
}

function setStudentPresence($student_number, $room_name){
	$db = new Model();
	$db->setAttendance($student_number, $room_name,'true');
	require('src/views/viewError404.php');
}

function error404(){
	require('src/views/viewError404.php');
}

function attendance() {
	$db = new Model();
	$results = $db->getAttendanceList("A103");
	require('src/views/viewAttendance.php');
}

function getInfoFromFormular(){
	$db= new Model();
	if($_SERVER["REQUEST_METHOD"]=="GET"){
		require('src/views/viewFormular.php');
	}
	if($_SERVER["REQUEST_METHOD"]=="POST"){
		$subject = $_POST['subject']; 
        $begin_hour = $_POST['begin_hour'];
        $end_course = $_POST['end_course'];
		$db->addClassBySubjectAndHour($subject,$begin_hour,$end_course);
	}
}








?>