<?php


require_once('src/controllers/controller.php');


if (isset($_GET['action']) && $_GET['action'] !== '') {
	if ($_GET['action'] === 'create') {
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
    else if($_GET['action'] == 'formular' ) {
        getInfoFromFormular(); 
    } 
    else{
        echo "L'action n'est pas connue";
        die;
	}

} else {
	error404(); //Eventually redirect to the home/login page
}



?>