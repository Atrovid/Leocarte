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
            $fullname = $name." ".$firstname;
            header("Location: ?action=attendance&name=".$name."&firstname=".$firstname);
        }
    }

    function display() {
        $model = new ModelApi();
        $name = ($_GET["name"]);
        $firstname = ($_GET["firstname"]);
        
        $teacherId = $model->getTeacherId($name, $firstname);
        $responseJSON = $model->getTeacherTaught($teacherId);
        $response = $model->filterTopicName($responseJSON);
        $coursIds = $model->filterTopicId($responseJSON);
        foreach($coursIds as $key => $value) {
            $result = $model->searchSeancesFromCoursAndTeacher($value, $name, $firstname);
            $id = $model->filterSeancesNow($result);
            if ($id != 0) {
                displayListStudent($id, $value);
                break;
            }
        }
    }

    function displayListStudent($idSeance, $coursId) {
        $model = new ModelApi();
        $responseJSON = $model->searchRoomAndPlanificationIdFromSeances($idSeance);
        echo $responseJSON;
        $responseJSON = $model->takeStudentTeacherAndRoomFromPlanificationID($model->filterPlanificationId($responseJSON));
        echo $responseJSON;
        $students = $model->getStudents($responseJSON);
        foreach ($students as $key => $value) {
            echo "<p>$value</p>";
        }
    }
?>
