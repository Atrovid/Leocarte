<?php
    ini_set('display_errors', 1);

    class ModelApi {
        private $pdo;

        public function getTeacherId($name, $firsname){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Enseignant?\$filter=Nom%20eq%20'" . $name . "%20" . $firsname . "'&\$select=Id");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            $result = json_decode($responseJSON, true);
            foreach ($result['value'] as $personId){
                $id = $personId['Id'];
            }
            curl_close($ch);
            return $id;
        }

        public function getTeacherTaught($teacherId) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Enseignant/". $teacherId . "/EnseignantsCours/?\$select=Nom,CoursId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);

            curl_close($ch);
            return $responseJSON;
        }

        function filterTopicName($responseJSON) {
            $array = json_decode($responseJSON, true);
            $topicTeachingName = array();
            foreach($array['value'] as $topic) {
                $topicTeachingName[] = $topic['Nom'];
            }
            return $topicTeachingName;
        }

        function filterTopicId($responseJSON) {
            $array = json_decode($responseJSON, true);
            $topicId = array();
            foreach($array['value'] as $topic) {
                $topicId[] = $topic['CoursId'];
            }
            return $topicId;
        }

        function searchSeancesFromCoursAndTeacher($coursId, $name, $firsname) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Cours/". $coursId . "/Seances?\$filter=Enseignants%20eq%20'" . $name . "%20" . $firsname ."'&\$select=Id,%20Debut,%20Fin");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            return $responseJSON;
        }

        function getTime() {
            $date1 = new DateTime("now");
            echo $date1->format('Y-m-d H:i:sP');
        }

        function filterSeancesNow($responseJSON){
            $array = json_decode($responseJSON, true);
            $id = 0;
            $dateNow = new DateTime("now");

            foreach ($array['value'] as $seance) {
                $dateStartSeance = new DateTime($seance['Debut']);
                $diff = $dateNow->diff($dateStartSeance);
                $total_minutes = ($diff->days * 24 * 60); 
                $total_minutes += ($diff->h * 60); 
                $total_minutes += $diff->i; 
                if (($total_minutes <= 60) && ($total_minutes >= -15)) {
                    $id = $seance['Id'];
                } else {
                    $id = $id;
                }
            }
            return $id;
        }

        function searchRoomAndPlanificationIdFromSeances($idSeance) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Seance/" . $idSeance ."?\$select=PlanificationId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            return $responseJSON;
        }
        
        function filterPlanificationId($responseJSON) {
            $array = json_decode($responseJSON, true);
            $planificationId = $array['PlanificationId'];
            return $planificationId;
        }

        function takeStudentTeacherAndRoomFromPlanificationID($planificationId) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Planification/".$planificationId."/PlanificationsRessource?\$select=Nom,%20TypeRessourceId");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            return $responseJSON;
        }
        
        function checkStudent($typeRessourceId) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/TypeRessource/".$typeRessourceId."?\$select=Nom");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            $array = json_decode($responseJSON, true);
            return ($array['Nom']=="Apprenant");
        }

        function getStudents($responseJSON){
            $array = json_decode($responseJSON, true);
            $students = array();
            foreach($array['value'] as $ressource) {
                if ($this->checkStudent($ressource['TypeRessourceId']) == true) {
                    $students [] = $ressource['Nom'];
                }
            }
            return $students;
        }

        
    }
?>
