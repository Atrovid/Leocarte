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

    }

?>
