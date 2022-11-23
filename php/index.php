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
    else{
        echo "L'action n'est pas connue";
        die;
	}
} else {
	error404(); //Eventually redirect to the home/login page
}



?>