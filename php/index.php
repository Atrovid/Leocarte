<?php


require_once('src/controllers/controller.php');


if (isset($_GET['action']) && $_GET['action'] !== '') {
	if ($_GET['action'] === 'create') {
        configDatabase();
    } else {
        echo "L'action n'est pas connue";
        die;
	}
} else {
	error404(); //Eventually redirect to the home/login page
}



?>