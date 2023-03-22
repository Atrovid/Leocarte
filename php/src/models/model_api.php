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
                if (($total_minutes <= 60) && ($total_minutes >= -30)) {
                    $id = $seance['Id'];
                } else {
                    $id = $id;
                }
            }
            return $id;
        }

        function topicNow($coursId) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Cours/".$coursId."/?\$select=Nom");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            $array = json_decode($responseJSON, true);
            return $array['Nom'];
        }
        
        function searchRoomAndPlanificationIdFromSeances($idSeance) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Seance/" . $idSeance ."?\$select=PlanificationId,%20NomSalle");
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

        function filterRoomName($responseJSON) {
            $array = json_decode($responseJSON, true);
            $roomName = $array['NomSalle'];
            return $roomName;
        }

        function takeStudentTeacherAndRoomFromPlanificationID($planificationId) {
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Planification/".$planificationId."/PlanificationsRessource?\$select=Nom,%20TypeRessourceId,%20Presence");
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

        function displayStudents($responseJSON){
            $array = json_decode($responseJSON, true);
            echo "<script>";
            echo "div = document.getElementById(\"students\");";
            echo "tbody = document.createElement(\"tbody\");";
            echo "div.appendChild(tbody);";
            foreach($array['value'] as $ressource) {
                if ($this->checkStudent($ressource['TypeRessourceId']) == true) {
                    echo "tr = document.createElement(\"tr\");";
                    echo "tbody.appendChild(tr);";
                    echo "th = document.createElement(\"th\");";
                    echo "tr.appendChild(th);";
                    echo "text = document.createTextNode(\"".$ressource['Nom']."\");";
                    echo "th.appendChild(text);";
                    echo "thP = document.createElement(\"th\");";
                    echo "tr.appendChild(thP);";
                    if ($ressource['Presence'] == false) {
                        echo "text = document.createTextNode(\"No\");";
                    } else {
                        echo "text = document.createTextNode(\"Yes\");";
                    }
                    echo "thP.appendChild(text);";
                }
            }
            echo "</script>";
        }    
        
        function tratIDRoomFromNameOfRoom($responseJSON, $codeStudent, $nameRoomSearch){
            $array= json_decode($responseJSON, true);
            foreach ($array['value'] as $room){
                $nameRoom = $room['Nom'];
                if (strcmp($nameRoom, $nameRoomSearch) == 0){
                    return $room['Id'];
                }
            }
            return 0;
        }
        
        function getIDRoomFromNameOfRoom($codeStudent, $nameRoomSearch){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Ressource/?\$filter=TypeRessourceId%20eq%20334210&\$select=Id,%20Nom");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            
            $array= json_decode($responseJSON, true);
            foreach ($array['value'] as $room){
                $nameRoom = $room['Nom'];
                if (strcmp($nameRoom, $nameRoomSearch) == 0){
                    return $room['Id'];
                }
            }
            return 0;
        }

        function getPlanificationsForTheRoom($IdRoom){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Ressource/".$IdRoom."/PlanificationRessources?\$select=PlanificationId,PlanificationDebut,PlanificationFin");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            return $responseJSON;
        }

        function filterCurrentPlanification($planificationJSON){
            $array = json_decode($planificationJSON, true);
            $id = 0;
            $dateNow = new DateTime("now");

            foreach ($array['value'] as $planification) {
                $dateStartSeance = new DateTime($planification['PlanificationDebut']);
                $diff = $dateNow->diff($dateStartSeance);
                $total_minutes = ($diff->days * 24 * 60); 
                $total_minutes += ($diff->h * 60); 
                $total_minutes += $diff->i; 
                if (($total_minutes <= 60) && ($total_minutes >= -30)) {
                    $id = $planification['PlanificationId'];
                    return $id;
                }
            }
            return $id;
        }

        function filterStartDate($planificationJSON){
            $array = json_decode($planificationJSON, true);
            $id = 0;
            $dateNow = new DateTime("now");

            foreach ($array['value'] as $planification) {
                $dateStartSeance = new DateTime($planification['PlanificationDebut']);
                $diff = $dateNow->diff($dateStartSeance);
                $total_minutes = ($diff->days * 24 * 60); 
                $total_minutes += ($diff->h * 60); 
                $total_minutes += $diff->i; 
                if (($total_minutes <= 60) && ($total_minutes >= -30)) {
                    $id = $planification['PlanificationId'];
                    $dateStart = $planification['PlanificationDebut'];
                    return $dateStart;
                }
            }
            return $dateNow;
        }

        function getStudentInPlanification($idCurrentPlanification, $codeStudent){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Planification/".$idCurrentPlanification."/PlanificationsRessource?\$select=Id,%20Code&\$filter=Code%20eq%20'".$codeStudent."'");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            return $responseJSON;
        }

        function filterPlanificationRessourceFromTheStudent($requestStudentInPlanification){
            $array = json_decode($requestStudentInPlanification, true);
            foreach ($array['value'] as $student){
                return $student['Id'];
            }
            return 0;
        }

        function getInformationToPushPresent($IdPlanificationRessource){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/PlanificationRessource/".$IdPlanificationRessource."?\$select=Id,PlanificationId,TypeRessourceId,Reference,ControlePresence,ProvenancePresence,Presence");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            return $responseJSON;
        }

        function getControlePresence($InformationToPushPresent){
            $array = json_decode($InformationToPushPresent, true);
            return $array["ProvenancePresence"];
        }

        function getStudents($idCurrentPlanification){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Planification/".$idCurrentPlanification."/PlanificationsRessource?\$select=Id,Code&\$filter=TypeRessourceId%20eq%20334212");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $responseJSON = curl_exec($ch);
            curl_close($ch);
            return $responseJSON;
        }

        function pushPresent($InformationToPushPresent, $IdPlanificationRessource){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            $dateNow = new DateTime("now");
            echo $dateNow->format('c');
            $array=json_decode($InformationToPushPresent, true);

            $data = array("Id" => $IdPlanificationRessource, "PlanificationId"=> $array["PlanificationId"], "TypeRessourceId" => $array["TypeRessourceId"], "Presence" => "true", "Reference" => $array["Reference"], "ControlePresence" => $dateNow->format('c'), "ProvenancePresence" => "Salle");
            print_r($data);
            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/PlanificationRessource/". $IdPlanificationRessource);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            $response = curl_exec($ch);
            curl_close($ch);
            if (!$response){
                return false;
            }
            return true;
        }


        function pushAbsent($InformationToPushPresent, $IdPlanificationRessource, $dateStartSeance){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            $dateNow = new DateTime("now");
            echo $dateNow->format('c');
            $array=json_decode($InformationToPushPresent, true);

            $data = array("Id" => $IdPlanificationRessource, "PlanificationId"=> $array["PlanificationId"], "TypeRessourceId" => $array["TypeRessourceId"], "Presence" => "false", "Reference" => $array["Reference"], "ControlePresence" => $dateNow->format('c'), "ProvenancePresence" => "Salle");
            print_r($data);
            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/PlanificationRessource/". $IdPlanificationRessource);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            $response = curl_exec($ch);
            curl_close($ch);
            if (!$response){
                return false;
            }
            return true;
        }

        public function getInfoFromPresentForm(){
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $studentID = $_POST['studentID'];

                if (!isset($studentID)){
                    echo "Veuillez entrer un numéroID";
                }
                print "Le numéro de l'étudiant est : ". $studentID;

            }
        }
    }
?>
