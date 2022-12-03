

<?php
ini_set('display_errors', 1);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $subject = $_POST['subject']; 
        $begin_hour = $_POST['begin_hour'];
        $end_course = $_POST['end_course'];
    
        if (!isset($subject)){
            echo "Veuillez entrer une matière";
        }
        if (!isset($begin_hour)){
            echo "Veuillez entrer une heure de début";
        }
        if(!isset($end_course)){
            echo "Veuillez entrer une heure de fin";
        }

        print "La matière est : " . $subject . " l'heure de début est : " . $begin_hour . " l'heure de fin est : " . $end_course;
        
    }

    

?>