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
        require('src/views/viewAttendanceAPI.php');
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
                $nameTopic = $model->topicNow($value);
                displayListStudent($id, $value, $nameTopic);
                break;
            }
        }
    }

    function displayListStudent($idSeance, $coursId, $nameTopic) {
        $model = new ModelApi();
        $responseJSON = $model->searchRoomAndPlanificationIdFromSeances($idSeance);
        $roomName = $model->filterRoomName($responseJSON);
        $responseJSON = $model->takeStudentTeacherAndRoomFromPlanificationID($model->filterPlanificationId($responseJSON));
        $model->displayStudents($responseJSON);
        echo "<div class=\"col-sm-4 p-3 bg-primary text-white\">";
        echo "<h2>Information</h2><ul>";
        echo "<li>Room : ".$roomName."</li>";
        echo "<li>Topic : ".$nameTopic."</li>";
        echo "</ul></div>";
        echo "</div>";
        echo "</div>";
    }
?>
