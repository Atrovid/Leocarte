<?php
    require_once('src/models/model_api.php');

    function getTeacherInformationFromForm(){
        $model = new ModelApi();
        if($_SERVER["REQUEST_METHOD"]=="GET"){
            require('src/views/viewFormAPI.html');
        }
        if($_SERVER["REQUEST_METHOD"]=="POST"){
            $name = $_POST['idNameTeacher'];
            $firstname = $_POST['idFirstnameTeacher'];
            $teacherId = $model->getTeacherId($name, $firstname);
            $responseJSON = $model->getTeacherTaught($teacherId);
            $response = $model->filterTopicName($responseJSON);
            $coursId = $model->filterTopicId($responseJSON);
            require('src/views/viewAttendanceAPI.php');
            echo "Your name is".$name."\n";
            echo "Your firstname is".$firstname;  
            echo "Your id is : " . $teacherId;
        }
    }
?>
