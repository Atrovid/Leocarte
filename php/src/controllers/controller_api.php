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
        echo "<script>";
        echo "div = document.getElementById(\"information\");";
        echo "ul = document.createElement(\"ul\");";
        echo "div.appendChild(ul);";
        echo "li = document.createElement(\"li\");";
        echo "ul.appendChild(li);";
        echo "text = document.createTextNode(\"Room  :".$roomName."\");";
        echo "li.appendChild(text);";
        echo "li = document.createElement(\"li\");";
        echo "ul.appendChild(li);";
        echo "text = document.createTextNode(\"Topic  :".$nameTopic."\");";
        echo "li.appendChild(text);";
        echo "</script>";

        
        if (array_key_exists('inCSV', $_POST)) {
            convertInCSV($responseJSON, $nameTopic);
        }
    }

    function convertInCSV($responseJSON, $nameTopic) {
        $array = json_decode($responseJSON, true);
        $dateToday = new DateTime("now");
        $nameFile = $nameTopic."_".$dateToday->format('Y_m_d');
        foreach ($array['value'] as $student) {
            $fp = fopen($nameFile, 'a');
            if ($student['Presence'] == false) {
                $data = array($student['Nom'], "No");
            } else {
                $date = array($student['Nom'], "Yes");
            }
            fputcsv($fp, $data, ',');
            fclose($fp);
        }
    }

    function getInfoFromPresentForm(){
        if($_SERVER["REQUEST_METHOD"]=="GET"){
            require('src/views/viewFormAddStudentPresent.php');
    
        }
        if($_SERVER["REQUEST_METHOD"]=="POST"){
            $studentID = $_POST['studentID'];
            echo "Le numéro etudiant est : " . $studentID;
            return $studentID;
        }


    }


?>
