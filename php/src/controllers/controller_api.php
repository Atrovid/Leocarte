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
            echo "Your name is".$name."\n";
            echo "Your firstname is".$firstname;     
            $result = $model->getTeacherId($name, $firstname);
        }
    }

?>
