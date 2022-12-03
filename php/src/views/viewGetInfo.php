

<?php
ini_set('display_errors', 1);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_subject = $_POST['id_subject']; 
        $start_hour = $_POST['start_hour'];
        $end_hour = $_POST['end_hour'];
    
        if (!isset($id_subject)){
            echo "Veuillez entrer une matière";
        }
        if (!isset($start_hour)){
            echo "Veuillez entrer une heure de début";
        }
        if(!isset($end_hour)){
            echo "Veuillez entrer une heure de fin";
        }

        print "La matière est : " . $id_subject . " l'heure de début est : " . $start_hour . " l'heure de fin est : " . $end_hour;
        
    }

    

?>