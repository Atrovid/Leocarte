<?php
    function requestCurlGetTagLogID($csn){
        $body = "{\"numeroId\":\"keyboard-secondary-id\",\"csn\":\"$csn\"}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://nfc-tag.ensicaen.fr/csn-ws");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $resultJson = curl_exec($ch);
        $resultPHP=json_decode($resultJson);
        return $resultPHP;
    }

    function requestCurlGetStudentID($tagLogId){
        $result = file_get_contents("https://nfc-tag.ensicaen.fr/nfc-ws/display?numeroId=keyboard-secondary-id&id=".$tagLogId);
        return $result;
    }

    function logCSN($csn){
        $requestTagLog = requestCurlGetTagLogID($csn);
        if ($requestTagLog->{'code'}=== "OK"){
            $tagLogID = $requestTagLog->{'taglogId'}; 
            $studentID = requestCurlGetStudentID($tagLogID);        
            $codeStudent = substr($studentID, -6);
            return $codeStudent;
        } else {
            return "ERROR";
        }
    }
?>
