<?php


require_once('src/controllers/controller.php');
require_once('src/controllers/controller_api.php');


if (isset($_GET['action']) && $_GET['action'] !== '') {
	/*if ($_GET['action'] === 'create') {
        createDatabase();
    } 
    else if ($_GET['action'] === 'destroy') {
        dropTable();
    } 
    else if($_GET['action'] === 'add') {
        addInfoIntoDatabase();
    }
    else if($_GET['action'] === 'delete') {
        deleteInfoIntoDatabase();
    }
    else if($_GET['action'] === 'display') {
        displayDatabase();
    } 
    else if($_GET['action'] == 'check' ) {
        checkStudentInClass();
    } 
    else if($_GET['action'] == 'confirm' ) {
        setStudentPresence($_GET['number'], $_GET['room']);
    } 
    else if ($_GET['action'] === 'attendance') {
        attendance();
    }
    else */
    if($_GET['action'] == 'form' ) {
        getTeacherInformationFromForm(); 
    } 
    else if($_GET['action'] == 'attendance') {
        display();
    } else if ($_GET['action'] == 'export_csv') {
        echo "coucou";
        convertInCSV();
    }
    
    else if($_GET['action'] == 'curl' ) {
        $csnDavid = "041818AA7E6780";
        $tagLogID = requestCurlGetTagLogID($csnDavid); 
        $studentID = requestCurlGetStudentID($tagLogID);        
        $codeStudent = substr($studentID, -6);
        echo "The code of student is : ".$codeStudent;
        $salle="C-301";
    }
     
    
    /*else if($_GET['action'] == 'api'){
        getResultFromAimaira();
    }*/
    else{
        echo "L'action n'est pas connue";
        die;
	}

} else {
	error404(); //Eventually redirect to the home/login page
}



?>