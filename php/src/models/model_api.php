<?php
    ini_set('display_errors', 1);

    class ModelApi {

        function appelGetAPI($request){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $request);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $reponseJSON = curl_exec($ch);
            curl_close($ch);
            return $reponseJSON;
        }

        function recupererIdTypeRessource($reponseJSON){
            $array=json_decode($reponseJSON, true);
            foreach ($array['value'] as $typeRessource){
                $idTypeRessource = $typeRessource['Id'];
            }
            return $idTypeRessource;
        }

        function recupererIdSalle($reponseJSON, $nomSalleRecherchee){
            $array= json_decode($reponseJSON, true);
            foreach ($array['value'] as $salle){
                $nomSalle = $salle['Nom'];
                if (strcmp($nomSalle, $nomSalleRecherchee) == 0){
                    return $salle['Id'];
                }
            }
            return 0;
        }

        function recupererIdPlanificationCourante($planificationsJSON){
            $array = json_decode($planificationsJSON, true);
            $id = 0;
            $dateMaintenant = new DateTime("now");

            foreach ($array['value'] as $planification) {
                $dateDebutSeance = new DateTime($planification['PlanificationDebut']);
                $diff = $dateMaintenant->diff($dateDebutSeance);
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

        function filtrerDateDebut($planificationsJSON){
            $array = json_decode($planificationsJSON, true);
            $id = 0;
            $dateMaintenant = new DateTime("now");

            foreach ($array['value'] as $planification) {
                $dateDebutSeance = new DateTime($planification['PlanificationDebut']);
                $diff = $dateMaintenant->diff($dateDebutSeance);
                $total_minutes = ($diff->days * 24 * 60); 
                $total_minutes += ($diff->h * 60); 
                $total_minutes += $diff->i; 
                if (($total_minutes <= 60) && ($total_minutes >= -30)) {
                    $id = $planification['PlanificationId'];
                    $dateDebutSeance = $planification['PlanificationDebut'];
                    return $dateDebutSeance;
                }
            }
        }

        function recupererPlanificationRessourceEtudiant($requestStudentInPlanification){
            $array = json_decode($requestStudentInPlanification, true);
            foreach ($array['value'] as $student){
                return $student['Id'];
            }
            return 0;
        }

        function metEtudiantAbsent($InformationToPushPresent, $IdPlanificationRessource, $beginUrl){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            $dateNow = new DateTime("now");
            $array=json_decode($InformationToPushPresent, true);

            $data = array("Id" => $IdPlanificationRessource, "PlanificationId"=> $array["PlanificationId"], "TypeRessourceId" => $array["TypeRessourceId"], "Presence" => "false", "Reference" => $array["Reference"], "ControlePresence" => $dateNow->format('c'), "ProvenancePresence" => "Salle");
            curl_setopt($ch, CURLOPT_URL, $beginUrl."/PlanificationRessource/". $IdPlanificationRessource);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            $response = curl_exec($ch);
            curl_close($ch);
        }

        function metTousEtudiantsAbsents($codesEtudiantJSON, $beginUrl, $idPlanificationCourante){
            $array = json_decode($codesEtudiantJSON, true);
            print_r($array);
            foreach ($array['value'] as $etudiant){
                $idEtudiant = $etudiant['Code'];
                $url = $beginUrl."/Planification/".$idPlanificationCourante."/PlanificationsRessource?\$select=Id,%20Code&\$filter=Code%20eq%20'".$idEtudiant."'";
                $idPlanificationRessourceEtudiant = recupererPlanificationRessourceEtudiant(appelGetAPI($url));
                $url = $beginUrl."/PlanificationRessource/".$idPlanificationRessourceEtudiant."?\$select=Id,PlanificationId,TypeRessourceId,Reference,ControlePresence,ProvenancePresence,Presence";
                $informationsPourMettreAbsent = this->appelGetAPI($url);
                metEtudiantAbsent($informationsPourMettreAbsent, $idPlanificationRessourceEtudiant, $beginUrl);
            }
        }

        function metEtudiantPresent($InformationToPushPresent, $IdPlanificationRessource, $beginUrl){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            $dateNow = new DateTime("now");
            $array=json_decode($InformationToPushPresent, true);

            $data = array("Id" => $IdPlanificationRessource, "PlanificationId"=> $array["PlanificationId"], "TypeRessourceId" => $array["TypeRessourceId"], "Presence" => "true", "Reference" => $array["Reference"], "ControlePresence" => $dateNow->format('c'), "ProvenancePresence" => "Salle");
            curl_setopt($ch, CURLOPT_URL, $beginUrl."/PlanificationRessource/". $IdPlanificationRessource);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

            $response = curl_exec($ch);
            curl_close($ch);
        }

        function recupereNomPrenom($reponseJSON){
            $array=json_decode($reponseJSON, true);
            foreach ($array['value'] as $etudiant){
                $nom = $etudiant['NomUsage'];
                $prenom = $etudiant['PrenomUsage'];
            }
            $result = $nom."/".$prenom;
            return $result;
        }

        public function getInfoFromPresentForm(){
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                $studentID = $_POST['studentID'];
                if (!isset($studentID)){
                    echo "Veuillez entrer un numéroID";
                }
                return $studentID;
            }
        }
    }
?>
