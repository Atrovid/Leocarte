<?php
    ini_set('display_errors', 1);

    class ModelApi {
        private $pdo;

        public function getTeacherId($name, $firsname){
            $cf = parse_ini_file('config.ini');
            $username = $cf['API_username'];
            $password = $cf['API_password'];
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, "https://graphprojet2ainfo.aimaira.net/GraphV1/Enseignant?\$filter=Nom%20eq%20'" . $name . "%20" . $firsname . "'");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
    }

?>
