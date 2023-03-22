<?php


require_once('src/models/model.php');
require_once('src/models/model_api.php');


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

function getInfoFromForm(){
	$db= new Model();
	if($_SERVER["REQUEST_METHOD"]=="GET"){
		require('src/views/viewForm.php');
		
	}
	if($_SERVER["REQUEST_METHOD"]=="POST"){
		$id_subject = $_POST['id_subject']; 
        $start_hour = $_POST['start_hour'];
        $end_hour = $_POST['end_hour'];
		$db->addClass(3,1,103,$id_subject,$start_hour,$end_hour);

	}
}

function requestCurlGetTagLogID($csn){
	$body = "{\"numeroId\":\"keyboard-secondary-id\",\"csn\":\"$csn\"}";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://nfc-tag.ensicaen.fr/csn-ws");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
	$resultJson = curl_exec($ch);
	echo $resultJson; //retourne les info dans un objet de type json
	$resultPHP=json_decode($resultJson);
	//print "Le taglogId est : \n";
	//print $resultPHP->{'taglogId'};
	return $resultPHP->{'taglogId'};
}

function requestCurlGetStudentID($tagLogId){
	$result = file_get_contents("https://nfc-tag.ensicaen.fr/nfc-ws/display?numeroId=keyboard-secondary-id&id=".$tagLogId);
	return $result;
}

function getResultFromAimaira(){
	$db = new Model();
	$resultAPI = $db->getStudentFromAimaira();
	$arrayOfCode = $db->FilterCode($resultAPI);
	//$code1 = $db->getACode($arrayOfCode, 'A00081');
	//echo $code1;
	$code2 = $db->getACode($arrayOfCode, 'A0008999');
	echo $code2;
}

function putPresent($codeStudent, $nameRoom){
	$modelAPI = new ModelApi();
	$IdRoom = $modelAPI->getIDRoomFromNameOfRoom($codeStudent, $nameRoom);
	echo "Id room is  : ".$IdRoom;
	$planifications = $modelAPI->getPlanificationsForTheRoom($IdRoom);
	echo $planifications;
	$idCurrentPlannification = $modelAPI->filterCurrentPlanification($planifications);
	$dateStartSeance = $modelAPI->filterStartDate($planifications);
	echo "Date start : ".$dateStartSeance;
	session_start();
	echo "Current id planification is : ".$idCurrentPlannification;
	if (isset($_SESSION["currentPlanification"])){
		echo "session current planification :".$_SESSION["currentPlanification"];
	}
	if (((!isset($_SESSION["currentPlanification"])) || ($idCurrentPlannification != $_SESSION["currentPlanification"]))==1){
		echo "IN IF !";
		$_SESSION["currentPlanification"]=$idCurrentPlannification;
		$_SESSION["isFirst"] = false;
	}
	
	echo "Id of current planification : ".$idCurrentPlannification;
	$codeStudent = "A00082";// TODO is for the demonstration : special person for the db
	$requestStudentInPlanification = $modelAPI->getStudentInPlanification($idCurrentPlannification, $codeStudent);
	echo $requestStudentInPlanification;
	$IdPlanificationRessource = $modelAPI->filterPlanificationRessourceFromTheStudent($requestStudentInPlanification);
	echo "Id of Planification Ressource is : ".$IdPlanificationRessource;
	$informationToPushPresent = $modelAPI->getInformationToPushPresent($IdPlanificationRessource);
	echo "InformationToPushPresent : ".$informationToPushPresent;
	$controlePresence = $modelAPI->getControlePresence($informationToPushPresent);

	if ($_SESSION["isFirst"] == false){
		$codesOfStudent = $modelAPI->getStudents($idCurrentPlannification);
		$array = json_decode($codesOfStudent, true);
		print_r($array);
		foreach ($array['value'] as $student){
			echo "In foreach";
			echo $student['Code'];
			$identificationStudent = $student['Code'];
			$studentInPlanification = $modelAPI->getStudentInPlanification($idCurrentPlannification, $identificationStudent);
			$identificationPlanificationRessource = $modelAPI->filterPlanificationRessourceFromTheStudent($studentInPlanification);
			$informationToPushAbsent = $modelAPI->getInformationToPushPresent($identificationPlanificationRessource);
			$modelAPI->pushAbsent($informationToPushAbsent, $identificationPlanificationRessource, $dateStartSeance);
		}
		$_SESSION["isFirst"] = true;
	} else {
		echo "Controle Presence is not null";
	}

	$check = $modelAPI->pushPresent($informationToPushPresent,$IdPlanificationRessource);
	if ($check == true){
		echo "Opération réussie";
	} else {
		echo "KO";
	}
	return $check;
}

?>
